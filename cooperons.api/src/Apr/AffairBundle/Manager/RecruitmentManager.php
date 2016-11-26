<?php

namespace Apr\AffairBundle\Manager;

use Apr\CoreBundle\ApiException\ApiException;
use Apr\AffairBundle\Entity\Recruitment;
use Apr\ContractBundle\Entity\Contract;
use Doctrine\ORM\EntityManager;
use Apr\UserBundle\Manager\BaseManager as BaseManager;
use Apr\Classes\Tools;

class RecruitmentManager extends BaseManager
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
        return $this->em->getRepository('AprAffairBundle:Recruitment');
    }

    public function createRecruitment(Contract $recruitmentContract, Contract $customerContract, &$listeEmails){
        $contractManager = $this->container->get('apr_contract.contract_manager');
        
        if (!$recruitmentContract->getOwner() || !$customerContract->getOwner() ||
            $recruitmentContract->getOwner() !== $customerContract->getOwner()) {
            throw new ApiException(400100);
        }

        $recruitmentSettings = $recruitmentContract->getRecruitmentSettings();

        if($recruitmentSettings) {
            $recruitee = $recruitmentContract->getClient();
            $customer = $customerContract->getClient();
            
            // Check que le customer n'a pas déjà été apporté ...
            if(!$customerContract->getRecruitment()) {

                // Check pas de recrutement actif existant ...
                $allRecruitments = $this->getRepository()->getAllRecruitments($recruitee, $customer);
                if(count($allRecruitments) == 0) {
                    // Check que le recruitee n'a pas déjà un contrat avec le customer  ...
                    $allRecruiteeContracts = $contractManager->getAllContracts($recruitee, 'default:owner');
                    foreach($allRecruiteeContracts as $contract) {
                        if($contract->getClient() && $contract->getClient() == $customer) 
                            return array('error' => 'Ce client est déjà en relation avec votre partenaire prestataire.');
                    }

                    $recruitment = new Recruitment($customerContract, $recruitmentSettings);

                    // Création du contrat entre le recruitee et le customer ...
                    $recruiteeContract = $contractManager->createContract($recruitmentContract->getClientCollaborator(), 
                        $customerContract->getClientCollaborator(), 'default:owner', $listeEmails);
                    $recruitment->setRecruiteeCorpContract($recruiteeContract);
                    $this->persistAndFlush($recruitment);

                    array_push($listeEmails, $this->getEmailConfirmationRecruitment($recruitment));

                    return array('recruitment' => $recruitment);
                } else {
                    {
                        return array('error' => 'Ce client a déjà été apporté à votre partenaire prestataire.');
                    }
                }
            } else {
                return array('error' => 'Ce client vous a été apporté par un partenaire commercial.');
            }
        } else {
            throw new ApiException(400600);
        }
    }

    public function createRecruitmentSettlements($mandataire, $totalAmount, &$listeEmails, $isRecruiter = false) {
        $mandataireManager = $this->container->get('apr_mandataire.mandataire_manager');
        $settlementsManager = $this->container->get('apr_mandataire.settlements_manager');

        $contract = $mandataire->getContract();
        $recruitment = $contract->getRecruitment();

        if($recruitment && ! $recruitment->isExpired()) {
            if(!$this->expireRecruitment($recruitment, $listeEmails)) {
                $cumulatedBillings = $recruitment->getCumulatedBillings();
                $cumulatedRebate = $recruitment->getCumulatedRebate();

                $newCumulatedRebate = $recruitment->getRecruitmentSettings()->calcCumulatedRebate($cumulatedBillings + $totalAmount);

                $rebateAmount =  max($newCumulatedRebate-$cumulatedRebate,0);

                if($isRecruiter) {
                    $my_mandataire = $recruitment->getRecruiterCorpContract()->getMandataire();
                    $description = 'Frais de Recrutement Coopérons ('.$contract->getOwner()->getLabel().')';

                    /*
                     * Le Transfert est toujours possible - car allSettlements ont été sélectionnés sous
                     * la contrainte que le montant AVANT Remise est inférieur au Dépôt.
                     *
                     * Or, après la création de l'avoir à l'étape précédente (isRecruiter = false) ce montant est désormais disponible.
                     */
                    $mandataireManager->createTransfer($mandataire, $my_mandataire, $rebateAmount, $listeEmails);
                } else {
                    $recruitmentContract = $recruitment->getRecruitmentContract();
                    $my_mandataire = $mandataire;
                    $description = 'Avoir pour Recrutement Coopérons ('.$recruitmentContract->getOwner()->getLabel().')';
                    $rebateAmount = -$rebateAmount;
                }

                $result = $settlementsManager->calculateSettlement($mandataire, $rebateAmount);
                $settlement = $settlementsManager->createSettlement($my_mandataire, $description, $result, 'recruitment');
                $this->persist($settlement);

                if(!$isRecruiter) {
                    $this->beginRecruitment($recruitment, $listeEmails);
                } else {
                    // MAJ Recruitment
                    $recruitment->addBillings($totalAmount);
                    $recruitment->addRebate($result['amountHt']);
                    $this->persist($recruitment);

                    $settlementsManager->validateSettlements($my_mandataire, array($settlement), $listeEmails);
                }

                // flush géré par MandataireManager

                return $settlement;
            }
        } else {
            return null;
        }
    }

    public function followCorporateWithRecruitment(Contract $contract) {
        $recruitment = $this->getRepository()->getOldRecruitment($contract);
        if($recruitment) {
            $recruitment->setRecruiteeCorpContract($contract);
            $this->persistAndFlush($recruitment);
        }
    }

    public function beginRecruitment(Recruitment $recruitment, &$listeEmails)  {
        $expiryDate = $recruitment->getExpiryDate();
        if(is_null($expiryDate)) {
            $recruitment->setExpiryDate();
            $this->persist($recruitment);

            array_push($listeEmails, $this->getEmailStartRecruitmentPeriod($recruitment));
        }
    }

    public function expireRecruitment(Recruitment $recruitment, &$listeEmails)  {
        if($recruitment->isExpired()) {
            return true;
        } else {
            $expiryDate = $recruitment->getExpiryDate();
            if(!is_null($expiryDate) and $expiryDate <= Tools::DateTime()) {
                $recruitment->expire();
                $this->persist($recruitment);

                array_push($listeEmails, $this->getEmailExpirationRecruitment($recruitment));
                return true;
            } else {
                return false;
            }
        }
    }

    // ToDo: Très lourd ... CRON ???
    public function updateRecruitmentExpiryDates($allContracts, &$listeEmails) {
        foreach($allContracts as $contract) {
            $recruitment = $contract->getRecruitment();
            if($recruitment) $this->expireRecruitment($recruitment, $listeEmails);
            $allRecruitments = $contract->getAllActiveRecruitments();
            if(count($allRecruitments)) {
                foreach($allRecruitments as $key => $recruitment) {
                    if($this->expireRecruitment($recruitment, $listeEmails)) {
                        unset($allRecruitments[$key]);
                    }
                }
            }
        }
        $this->flush();
    }

    public function getEmailConfirmationRecruitment(Recruitment $recruitment){
        $customerContract = $recruitment->getRecruiterCorpContract();
        $collaborator = $customerContract->getClientCollaborator();
        $member = $collaborator->getMember();
        $memberSales = $customerContract->getOwnerMember();
        $memberProvider = $recruitment->getRecruitmentContract()->getClientMember();

        return array(
            'to' => $member->getEmail(),
            'cc' => array($memberSales->getEmail(), $memberProvider->getEmail()),
            'subject' =>'Un recrutement a été effectué par '.$memberSales->getFirstName().' '.$memberSales->getLastName(),
            'body' => array(
                'template' => 'AprAutoEntrepreneurBundle:Emails:confirmationRecruitment.html.twig',
                'parameter' => array(
                    'member' => $member,
                    'memberSales' => $memberSales,
                    'memberProvider' => $memberProvider,
                    'collaborator' => $collaborator,
                )),
        );
    }

    public function getEmailStartRecruitmentPeriod(Recruitment $recruitment){
        $customerContract = $recruitment->getRecruiterCorpContract();
        $recruitmentContract = $recruitment->getRecruitmentContract();

        $corporate = $customerContract->getClientCollaborator()->getCorporate();

        $memberSales = $customerContract->getOwnerMember();
        $memberProvider = $recruitmentContract->getClientMember();

        // Clone pour modify ...
        $expiryDate = clone $recruitment->getExpiryDate();

        return array(
            'to' => $memberSales->getEmail(),
            'cc' => array($memberProvider->getEmail()),
            'subject' =>'Début de commissionnement sur '.$corporate->getRaisonSocial().' (prestataire '.$memberProvider->getFirstName().' '.$memberProvider->getLastName().')',
            'body' => array(
                'template' => 'AprAutoEntrepreneurBundle:Emails:startRecruitmentPeriod.html.twig',
                'parameter' => array(
                    'member' => $memberSales,
                    'memberProvider' => $memberProvider,
                    'corporate' => $corporate,
                    'duration' => $recruitment->getRecruitmentSettings()->getDuration(),
                    'expiryDate' => $expiryDate->modify('-1 day')->format('d/m/Y')
                )),
        );
    }

    public function getEmailExpirationRecruitment(Recruitment $recruitment){
        $customerContract = $recruitment->getRecruiterCorpContract();
        $recruitmentContract = $recruitment->getRecruitmentContract();

        $corporate = $customerContract->getClientCollaborator()->getCorporate();

        $memberSales = $customerContract->getOwnerMember();
        $memberProvider = $recruitmentContract->getClientMember();

        return array(
            'to' => $memberSales->getEmail(),
            'cc' => array($memberProvider->getEmail()),
            'subject' =>'Fin de commissionnement sur '.$corporate->getRaisonSocial().' (prestataire '.$memberProvider->getFirstName().' '.$memberProvider->getLastName().')',
            'body' => array(
                'template' => 'AprAutoEntrepreneurBundle:Emails:expirationRecruitment.html.twig',
                'parameter' => array(
                    'member' => $memberSales,
                    'memberProvider' => $memberProvider,
                    'corporate' => $corporate,
                )),
        );
    }
}

?>
