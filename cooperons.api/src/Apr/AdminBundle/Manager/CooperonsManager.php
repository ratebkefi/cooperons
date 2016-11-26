<?php

namespace Apr\AdminBundle\Manager;

use Doctrine\ORM\EntityManager;
use Apr\UserBundle\Manager\BaseManager as BaseManager;
use Apr\MandataireBundle\Entity\Mandataire;
use Apr\ContractBundle\Entity\Party;
use Apr\ContractBundle\Entity\Contract;
use Apr\UserBundle\Entity\Token;
use Apr\ProgramBundle\Entity\Member;

class CooperonsManager extends BaseManager
{

    protected $em;
    protected $container;

    public function __construct(EntityManager $em, $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function getRepository() {
        // Nécessaire pour implémenter BaseManager - non utilisé
    }

    public function postConnectToken(Member $member, Token $tokenObject, &$listeEmails) {
        $coopPlusManager = $this->container->get('apr_admin.coop_plus_manager');
        $sponsorShipManager = $this->container->get('apr_program.sponsorship_manager');
        $invitationManager = $this->container->get('apr_program.invitation_manager');

        if($tokenObject->isInvitation()) {
            // L'invitation (et Token ...) est supprimée à la fin de la méthode (sauf dans le cas a priori impossible d'une Invitation pour un Program <> ProgramPlus ...)
            $invitation = $tokenObject->getObject();
            if($tokenObject->hasProgram()) {
                $program = $invitation->getProgram();
                if($coopPlusManager->isProgramPlus($program)) {
                    // Inscription suite à une invitation Plus - confirmée par Coopérons en tant que Promoteur ...
                    $participatesTo = $coopPlusManager->loadParticipatesToPlus($member);
                    $invitationManager->confirmProgramInvitation($program, $tokenObject->getValue(), $participatesTo, $listeEmails);
                }
            } else {
                $this->postConfirmInvitation($invitation, $member, $listeEmails);
            }
        } elseif($tokenObject->isParticipatesTo()) {
            $participatesTo = $tokenObject->getObject();
            // Couplage des parrainages - soit en tant que filleul, soit en tant que parrain ...
            $sponsorShipManager->couplagePlus($participatesTo->getProgram(), $participatesTo, null, $listeEmails);
            $sponsorShipManager->couplagePlus($participatesTo->getProgram(), null, $participatesTo, $listeEmails);
        }
    }

    public function postConfirmInvitation($invitation, Member $userMember, &$listeEmails) {
        $coopPlusManager = $this->container->get('apr_admin.coop_plus_manager');
        $coopAEManager = $this->container->get('apr_admin.coop_ae_manager');
        $collaboratorManager = $this->container->get('apr_contract.collaborator_manager');
        $collegeManager = $this->container->get('apr_corporate.college_manager');
        $contractManager = $this->container->get('apr_contract.contract_manager');

        $coopPlusManager->createSponsorshipPlusAfterInvitation($invitation, $userMember, $listeEmails);

        switch($invitation->getType()) {
            case 'CollaboratorInvitation':
                $collaboratorManager->confirmCollaborator($invitation->getCollaborator(), $userMember, $listeEmails);
                break;
            case 'CollegeInvitation':
                $college = $invitation->getCollege();
                $member = $college->getMember();
                $collegeManager->leaveCollege($college, $listeEmails);
                array_push($listeEmails, $collegeManager->getMailStandByCollegeWaitingForCorporate($userMember, $member));
                break;
            case 'ContractInvitation':
                $coopAEManager->createSponsorshipCoopAEFromContract($invitation->getContract(), $userMember, $listeEmails);
                array_push($listeEmails, $contractManager->getEmailPostConfirmInvitation($invitation, $userMember));
                $contractManager->removeContract($invitation->getContract(), $listeEmails);
        }
        $this->removeAndFlush($invitation);
    }

    public function isCorporateCooperons($corporate){
        if(!is_null($corporate)) {
            $idCorporateCooperons = $this->container->getParameter('idCorporateCooperons');
            return $corporate->getId() == $idCorporateCooperons;
        } else {
            return false;
        }
    }

    public function loadCorporateCooperons() {
        $corporateManager = $this->container->get('apr_corporate.corporate_manager');
        return $corporateManager->loadCorporateById($this->container->getParameter('idCorporateCooperons'));
    }

    public function loadPartyCooperons() {
        $contractManager = $this->container->get('apr_contract.contract_manager');
        return $contractManager->loadPartyById($this->container->getParameter('idPartyCooperons'));
    }

    public function loadAdministratorCooperons() {
        return $this->loadPartyCooperons()->getAdministrator();
    }

    public function loadMandataireCooperons() {
        return $this->loadPartyCooperons()->getMandataire();
    }

    public function loadContractCooperons() {
        return $this->loadPartyCooperons()->getCoopContract();
    }

    public function isContractCooperons(Contract $contract) {
        return $contract->getOwner()->isCooperons();
    }

    public function isMandataireCooperons(Mandataire $mandataire) {
        return $mandataire->getOwner()->isCooperons();
    }

    public function createMandataireCooperons(Party $party) {
        if(is_null($party->getMandataire())) {
            $labelOwner = "Compte Mandataire: ".$party->getLabel();
            $labelClient = "Compte Mandataire";
            $ownerAccountRef = "517";
            $clientAccountRef = "517";
            $ownerIncomeRef = "706";
            $clientIncomeRef = "622";
            $mandataire = new Mandataire(null, $this->loadPartyCooperons(), $party, $labelOwner, $labelClient,
                $ownerAccountRef, $clientAccountRef, $ownerIncomeRef, $clientIncomeRef);
            $party->setMandataire($mandataire);

            $mandataire->setInvoicingFrequency('month');

            $this->persist($party);
            return $mandataire;
        }
        // flush géré par Manager AutoEntrepreneur / Corporate ...
    }

    public function createContractCooperons($collaborator) {
        $contractManager = $this->container->get('apr_contract.contract_manager');

        $contract = $contractManager->createContract($this->loadAdministratorCooperons(), $collaborator, false);
        $contract->publish();
        return $contract;

        // PersistAndFlush géré par Manager appelant...
    }

    public function getPartyStatus(Party $party) {
        $status = array(
            'cgu' => false,
            'cgv' => false,
            'cpContracts' => false,
            'cpCorpAE' => false,
            'cpMandataireAE' => false,
            'cpPromoteur' => false,
            'cpStimulation' => false
        );
        if($party->isCooperons()) {
            foreach (array_keys($status) as $key) {
                $status[$key] = true;
            }
        } else {
            foreach (array_keys($status) as $key) {
                $methodName = "get"&ucfirst($key);
                $legalDocument = $party->$methodName();
                $status[$key] = ($legalDocument->getStatus() == 'active');
            }
        }

        return $status;
    }

    public function getGrossUpRate($settlement)
    {
        $autoEntrepreneurManager = $this->container->get('apr_auto_entrepreneur.auto_entrepreneur_manager');

        $mandataire = $settlement->getMandataire();
        $client = $mandataire->getClient();

        if ($client->getAutoEntrepreneur()) {
            return $autoEntrepreneurManager->getGrossUpRate($settlement);
        } else {
            return 0;
        }
    }

    public function beforeCancelSettlement(Mandataire $mandataire, $settlement, &$listeEmails)
    {
        $coopPlusManager = $this->container->get('apr_admin.coop_plus_manager');
        $type = $settlement->getType();

        if($type == 'abonnement' or $type == 'points') {
            return $coopPlusManager->beforeCancelSettlement($settlement, $listeEmails);
        } else {
            return true;
        }
    }

    public function afterPayment(Mandataire $mandataire, $payment, &$listeEmails) {
        $autoEntrepreneurManager = $this->container->get('apr_auto_entrepreneur.auto_entrepreneur_manager');

        $client = $mandataire->getClient();

        if($client->getAutoEntrepreneur()) {
            return $autoEntrepreneurManager->afterPayment($mandataire, $payment, $listeEmails);
        }
    }

    public function afterSettlements(Mandataire $mandataire, $allSettlements, &$listeEmails)
    {
        $coopPlusManager = $this->container->get('apr_admin.coop_plus_manager');

        foreach($allSettlements as $settlement) {
            $type = $settlement->getType();
            if($type == 'abonnement' or $type == 'points') {
                $coopPlusManager->afterSettlement($settlement, $listeEmails);
            }
        }

    }
}
