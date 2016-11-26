<?php

namespace Apr\AutoEntrepreneurBundle\Manager;

use Apr\CoreBundle\ApiException\ApiException;
use Apr\AutoEntrepreneurBundle\Entity\QuarterlyTaxation;
use Apr\AutoEntrepreneurBundle\Entity\AutoEntrepreneur;
use Apr\AutoEntrepreneurBundle\Entity\ServiceType;
use Apr\ContractBundle\Entity\Contract;
use Apr\ContractBundle\Entity\LegalDocument;
use Doctrine\ORM\EntityManager;
use Apr\UserBundle\Manager\BaseManager as BaseManager;
use Apr\CoreBundle\Tools\Tools;

class AutoEntrepreneurManager extends BaseManager
{

    protected $em;
    protected $container;

    public function __construct(EntityManager $em, $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function getRepository()
    {
        return $this->em->getRepository('AprAutoEntrepreneurBundle:AutoEntrepreneur');
    }

    public function getServiceTypeRepository()
    {
        return $this->em->getRepository('AprAutoEntrepreneurBundle:ServiceType');
    }

    public function getServiceTypeById($id)
    {
        return $this->getServiceTypeRepository()->find($id);
    }

    public function loadAutoEntrepreneurById($id)
    {
        return $this->getRepository()->find($id);
    }

    public function editAutoEntrepreneur($member, $autoEntrepreneurData, &$listeEmails)
    {
        $participatesToManager = $this->container->get('apr_program.participates_to_manager');
        $cooperonsManager = $this->container->get('apr_admin.cooperons_manager');
        $coopAEManager = $this->container->get('apr_admin.coop_ae_manager');

        $welcome = is_null($autoEntrepreneurData->getId());
        $this->persist($autoEntrepreneurData);

        if($welcome) {
            $autoEntrepreneurData->setMember($member);

            // ROLE_AE
            $user = $member->getUser();
            $user->addRole('ROLE_AE');
            $this->persist($user);

            // Mandataire Cooperons <-> AE (taux de provision 30%)
            $party = $autoEntrepreneurData->getParty();
            $cooperonsManager->createMandataireCooperons($party);
            $party->setProvisionRate(30);
            $this->persist($party);

            // Programme Coopérons AE
            $programAE = $coopAEManager->loadProgramCoopAE();
            // id ProgramAE = id ProgramPlus = UserId

            $participatesToAE = $participatesToManager->createParticipatesTo($programAE, $member, $member->getUser()->getId());

            array_push($listeEmails, $this->getEmailWelcomeAutoEntrepreneur($member));
        }
        $this->flush();
        return $autoEntrepreneurData;
    }

    /**
     * @param $autoEntrepreneur
     * @param $bankAccountData
     */
    public function updateBankAccount(AutoEntrepreneur $autoEntrepreneur, $bankAccountData) {
        $autoEntrepreneur->setIBAN($bankAccountData->IBAN);
        $autoEntrepreneur->setBIC($bankAccountData->BIC);
        $this->persistAndFlush($autoEntrepreneur);
    }

    /**
     * @param $autoEntrepreneur
     * @param $bankAccount
     */
    public function updateExternal(AutoEntrepreneur $autoEntrepreneur, $externalData) {
        $autoEntrepreneur->setExternalEmail($externalData->externalEmail);
        $autoEntrepreneur->setExternalOldPassword($autoEntrepreneur->getExternalPassword());
        $autoEntrepreneur->setExternalPassword($externalData->externalPassword);
        $this->persistAndFlush($autoEntrepreneur);
    }

    /**
     * @param $autoEntrepreneur
     */
    public function activate(AutoEntrepreneur $autoEntrepreneur, $isPostActivated = false, &$listeEmails = null) {
        $now = Tools::DateTime('now');
        if($isPostActivated && $now < Tools::firstDayOf('quarter', $autoEntrepreneur->getCreatedDate()->modify('+3 month'))) {
            throw new ApiException(40086);
        }  else {
            $autoEntrepreneur->activate();
            $this->persistAndFlush($autoEntrepreneur);
            if($isPostActivated) array_push($listeEmails, $this->getEmailActivationAutoEntrepreneur($autoEntrepreneur->getMember()));
        }
    }

    public function addServiceType(LegalDocument $legalDocument, $label, $unitDefaultAmount, $unitLabel){
        $serviceType = new ServiceType($legalDocument);
        $serviceType->setLabel($label);
        $serviceType->setUnitDefaultAmount($unitDefaultAmount);
        $serviceType->setUnitLabel($unitLabel);
        $this->persistAndFlush($serviceType);
        return $serviceType;
    }

    public function updateServiceType($id, $label, $unitDefaultAmount, $unitLabel){
        $serviceType = $this->getServiceTypeById($id);
        if(!$serviceType){
            return false;
        }
        $serviceType->setLabel($label);
        $serviceType->setUnitDefaultAmount($unitDefaultAmount);
        $serviceType->setUnitLabel($unitLabel);
        $this->persistAndFlush($serviceType);
        return $serviceType;
    }

    public function isSponsorableContract(Contract $contract, $member) {
        $coopAEManager = $this->container->get('apr_admin.coop_ae_manager');

        if($contract->isCreatedByOwner()) {
            $creatorMember = $contract->getOwnerMember();
        } else {
            $creatorMember = $contract->getClientMember();
        }

        if($member == $creatorMember) {
            return false;
        } else {
            $participatesTo = $coopAEManager->loadParticipatesToCoopAE($member);
            if($participatesTo->getSponsor()) {
                return false;
            } else {
                $creatorParticipatesTo = $coopAEManager->loadParticipatesToCoopAE($creatorMember);
                if($creatorParticipatesTo->getKing() == $participatesTo) {
                    return false;
                } else {
                    return true;
                }
            }
        }
    }

    public function createServiceSettlements($contract, $params, &$listeEmails) {
        $settlementsManager = $this->container->get('apr_mandataire.settlements_manager');
        $mandataire = $contract->getMandataire();

        $allSettlements = array();
        foreach($params as $my) {
            $my['unitAmount'] = (float) $my['unitAmount'];
            $my['quantity'] = (float) $my['quantity'];
            if($my['unitAmount'] > 0 && $my['quantity'] > 0) {
                $serviceType = $my['serviceType'];
                $description = $serviceType->getLabel().' ('.$my['quantity'].' ';
                $description .= $serviceType->getUnitLabel()?$serviceType->getUnitLabel():'opération';
                $description .= $my['quantity']>1?'s':'';
                $description .= ')';
                $result = $settlementsManager->calculateSettlement($mandataire, $my['unitAmount'], $my['quantity']);
                if(!is_null($result)) {
                    array_push($allSettlements, $settlementsManager->createSettlement($mandataire, $description, $result, 'service'));
                }
            }
        }

        if(count($allSettlements)) {
            foreach($allSettlements as $settlement) {
                $this->persist($settlement);
            }
            $this->flush();

            $settlementsManager->updateWaitingSettlements($mandataire, $listeEmails);
        }

        return (count($allSettlements) != 0);
    }

    public function getGrossUpRate($settlement) {
        $coopAEManager = $this->container->get('apr_admin.coop_ae_manager');
        $mandataire = $settlement->getMandataire();
        $contract = $mandataire->getContract();
        
        return ($settlement->getType() == 'service' && $contract->getType() == 'default')?$coopAEManager->rateFeeCooperons:0;
    }

    public function beforeRecordSettlements($mandataire, &$allSettlements, &$listeEmails) {
        $coopAEManager = $this->container->get('apr_admin.coop_ae_manager');
        $recruitmentManager = $this->container->get('apr_affair.recruitment_manager');

        if($mandataire->getContract()) {
            // Provient de Contract.beforeRecordSettlements ...
            $totalAmount = 0;
            foreach($allSettlements as $settlement) {
                if($settlement->getType() == 'service') $totalAmount += $settlement->getAmountHt();
            }

            $coopAEManager->givePointsToSponsorCoopAE($mandataire, $totalAmount, $listeEmails);

            $creditSettlement = $recruitmentManager->createRecruitmentSettlements($mandataire, $totalAmount, $listeEmails);
            if(!is_null($creditSettlement)) array_push($allSettlements, $creditSettlement);
        }
    }

    public function afterPayment($mandataire, $payment, &$listeEmails) {
        $paymentsManager = $this->container->get('apr_mandataire.payments_manager');

        if($payment->getQuarterlyTaxation()) {
            $provision = $paymentsManager->calculateProvision($mandataire->getClient());
            array_push($listeEmails, $this->getEmailConfirmQuarterlyTaxation($mandataire, $payment->getQuarterlyTaxation(), $provision));
        }
    }

    public function afterSettlements($mandataire, $allSettlements, &$listeEmails) {
        $coopAEManager = $this->container->get('apr_admin.coop_ae_manager');
        $recruitmentManager = $this->container->get('apr_affair.recruitment_manager');

        if($mandataire->getContract()) {
            // Provient de Contract.beforeRecordSettlements ...
            $totalAmount = 0;
            foreach ($allSettlements as $settlement) {
                if ($settlement->getType() == 'service') $totalAmount += $settlement->getAmountHt();
            }

            $recruitmentManager->createRecruitmentSettlements($mandataire, $totalAmount, $listeEmails, true);
            $coopAEManager->createSettlementFeeCoopAE($mandataire, $totalAmount, $listeEmails);
        }
    }

    public function buildQuarterlyTaxations() {
        $settlementsManager = $this->container->get('apr_mandataire.settlements_manager');

        $allSettlements = $settlementsManager->getTotalSettlementsForQuarterlyTaxation(null, false);

        $allQuarterlyTaxations = array();
        $autoEntrepreneur = null;
        $totalIncomeHt = 0;

        foreach($allSettlements as $settlement) {
            $owner = $settlement->getMandataire()->getOwner();
            // allSettlements triés par AutoEntrepreneur ...
            if($owner->getAutoEntrepreneur() != $autoEntrepreneur) {
                if(!is_null($autoEntrepreneur)) array_push($allQuarterlyTaxations, new QuarterlyTaxation($autoEntrepreneur, $totalIncomeHt));
                $autoEntrepreneur = $owner->getAutoEntrepreneur();
                $totalIncomeHt = 0;
            }
            $totalIncomeHt += $settlement->getAmountHt();
        }
        if(!is_null($autoEntrepreneur)) array_push($allQuarterlyTaxations, new QuarterlyTaxation($autoEntrepreneur, $totalIncomeHt));

        return $allQuarterlyTaxations;
    }

    public function rescheduleQuarterlyTaxation(AutoEntrepreneur $autoEntrepreneur, &$listeEmails) {
        $autoEntrepreneur->setLastQuarterlyDeclarationDate();
        $this->persistAndFlush($autoEntrepreneur);

        array_push($listeEmails, $this->getEmailRescheduleQuarterlyTaxation($autoEntrepreneur->getMandataire()));
    }


    public function createQuarterlyTaxation(AutoEntrepreneur $autoEntrepreneur, $amount, &$listeEmails) {
        $settlementsManager = $this->container->get('apr_mandataire.settlements_manager');
        $paymentsManager = $this->container->get('apr_mandataire.payments_manager');

        $mandataire = $autoEntrepreneur->getMandataire();
        $totalIncomeHt = $settlementsManager->getTotalSettlementsForQuarterlyTaxation($autoEntrepreneur, false);
        $quarterlyTaxation = new QuarterlyTaxation($autoEntrepreneur, $totalIncomeHt);
        $this->persist($quarterlyTaxation);

        $payment = $paymentsManager->createDebitPayment($mandataire, -$amount);
        $quarterlyTaxation->confirm($payment);

        $paymentsManager->validatePayment($mandataire, $payment, $listeEmails);
    }

    public function buildJson(AutoEntrepreneur $autoEntrepreneur)
    {
        return array(
            'id' => $autoEntrepreneur->getId(),
            'firstName' => $autoEntrepreneur->getMember()->getFirstName(),
            'lastName' => $autoEntrepreneur->getMember()->getLastName(),
            'idParty' => $autoEntrepreneur->getParty()->getId()
        );
    }

    public function getEmailWelcomeAutoEntrepreneur($member){
        return array(
            'to' => $member->getEmail(),
            'subject' =>'Votre compte Auto-Entrepreneur a bien été créé',
            'body' => array(
                'template' => 'AprAutoEntrepreneurBundle:Emails:welcomeAutoEntrepreneur.html.twig',
                'parameter' => array(
                    'member' => $member,
                )),
        );
    }

    public function getEmailActivationAutoEntrepreneur($member){
        return array(
            'to' => $member->getEmail(),
            'subject' =>'Votre compte Auto-Entrepreneur est désormais activé',
            'body' => array(
                'template' => 'AprAutoEntrepreneurBundle:Emails:activationAutoEntrepreneur.html.twig',
                'parameter' => array(
                    'member' => $member,
                )),
        );
    }

    public function getEmailRescheduleQuarterlyTaxation($mandataire){
        $mandataireManager = $this->container->get('apr_mandataire.mandataire_manager');

        $mailParam = $mandataireManager->prepareEmailMandataire($mandataire);
        $mailParam['subject'] = "Report de déclaration d'activité";
        $mailParam['body']['template'] = 'AprAutoEntrepreneurBundle:Emails:rescheduleQuarterlyTaxation.html.twig';
        return $mailParam;
    }

    public function getEmailConfirmQuarterlyTaxation($mandataire, QuarterlyTaxation $quarterlyTaxation, $provision){
        $mandataireManager = $this->container->get('apr_mandataire.mandataire_manager');

        $mailParam = $mandataireManager->prepareEmailMandataire($mandataire);
        $mailParam['body']['parameter']['quarterlyTaxation'] = $quarterlyTaxation;
        $mailParam['body']['parameter']['provision'] = $provision;
        $mailParam['subject'] = "Déclaration d'activité et règlement de vos cotisations sociales";
        $mailParam['body']['template'] = 'AprAutoEntrepreneurBundle:Emails:confirmationQuarterlyTaxation.html.twig';
        return $mailParam;
    }
}

?>
