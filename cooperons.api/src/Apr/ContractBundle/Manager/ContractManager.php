<?php

namespace Apr\ContractBundle\Manager;

use Apr\ContractBundle\Entity\Collaborator;
use Apr\CoreBundle\ApiException\ApiException;
use Doctrine\ORM\EntityManager;
use Apr\UserBundle\Manager\BaseManager as BaseManager;
use Apr\ContractBundle\Entity\Contract;
use Apr\ContractBundle\Entity\ContractInvitation;
use Apr\ContractBundle\Entity\LegalDocument;
use Apr\ContractBundle\Entity\Habilitation;


class ContractManager extends BaseManager
{

    protected $em;
    protected $container;

    // A copier dans Entity & Repository
    private $arrTypes = array(
        0 => 'default',
        1 => 'affair'
    );

    public function __construct(EntityManager $em, $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function securityCheck($user, $contract, $isOwner = false, $isClient = false)
    {
        if ($contract == null) {
            throw new ApiException(400100);
        }

        if ($user->hasRole('ROLE_SUPER_ADMIN') || $user->hasRole('ROLE_ADMIN')) {
            return true;
        }
        $member = $user->getMember();

        $party = null;
        if ($isOwner) {
            $notAuthorized = !$contract->getOwner()->isAuthorized($member);
        } else if ($isClient) {
            $notAuthorized = !$contract->getClient()->isAuthorized($member);
        } else {
            $party = $contract->getAuthorizedParty($member);
            $notAuthorized = is_null($party);
        }
        if ($notAuthorized) {
            throw new ApiException(403102);
        } else {
            return $party;
        }
    }

    public function getRepository()
    {
        return $this->em->getRepository('AprContractBundle:Contract');
    }

    public function getPartyRepository()
    {
        return $this->em->getRepository('AprContractBundle:Party');
    }

    public function loadContractById($id)
    {
        return $id ? $this->getRepository()->find($id) : null;
    }

    public function loadPartyById($id)
    {
        return $id ? $this->getPartyRepository()->find($id) : null;
    }

    public function loadAllParties()
    {
        return $this->getPartyRepository()->getAllParties();
    }

    public function getAllContracts($party, $filterContract, $collaborator = null)
    {
        $this->checkFilterContract($filterContract);
        return $this->getRepository()->getAllContracts($party, $filterContract, $collaborator);
    }

    /**
     * @param Collaborator $ownerCollaborator
     * @param Collaborator $clientCollaborator
     * @param $filterContract
     * @param null $listeEmails
     * @return Contract
     */
    public function createContract(Collaborator $ownerCollaborator, Collaborator $clientCollaborator, $filterContract, &$listeEmails = null)
    {
        $coopAEManager = $this->container->get('apr_admin.coop_ae_manager');
        $recruitmentManager = $this->container->get('apr_affair.recruitment_manager');

        $arr = explode(":", $filterContract);
        $typeContract = $arr[0];
        $isCreatedByOwner = ($arr[1] == 'owner');

        $ownerAE = $ownerCollaborator->getParty()->getAutoEntrepreneur();
        $clientAE = $clientCollaborator->getParty()->getAutoEntrepreneur();
        // Tant qu'AE non activé - pas d'autre contrat que client ...
        if(($ownerAE && is_null($ownerAE->getActivationDate()) ||
                $clientAE && is_null($clientAE->getActivationDate())) && $typeContract != 'default')
            throw new ApiException(400410);

        if(!$isCreatedByOwner) {
            $switchCollaborator = $clientCollaborator;
            $clientCollaborator = $ownerCollaborator;
            $ownerCollaborator = $switchCollaborator;
        }

        $owner = $client = null;
        if (!is_null($ownerCollaborator)) {
            $owner = $ownerCollaborator->getParty();
            $ownerAccountRef = null;
            $ownerIncomeRef = null;
        }

        if (!is_null($clientCollaborator)) {
            $client = $clientCollaborator->getParty();
            $clientAccountRef = null;
            $clientIncomeRef = null;
        }

        if(!$owner->getStatus()['Contracts']['cpContracts']) throw new ApiException(403103);

        if($this->getRepository()->getContractBetweenParties($owner, $client, $typeContract)) throw new ApiException(400110);

        if (!is_null($owner) && !is_null($client) && $owner != $client) {
        $contract = new Contract($owner, $client, $isCreatedByOwner, $typeContract);
        $contract->setOwnerCollaborator($ownerCollaborator);
        $contract->setClientCollaborator($clientCollaborator);

        $this->persistAndFlush($contract);
        if (!is_null($listeEmails)) {
            array_push($listeEmails, $this->getEmailConfirmationContract($contract));
        }

        $coopAEManager->createSponsorshipCoopAEFromContract($contract, null, $listeEmails);

        // Recherche d'un ancien recrutement ...
        $recruitmentManager->followCorporateWithRecruitment($contract);

        return $contract;
        }
    }

    public function createMandataireContract(LegalDocument $legalDocument) {
        $mandataireManager = $this->container->get('apr_mandataire.mandataire_manager');

        $ownerLabel = 'Contrat ' . $legalDocument->getContract()->getClient()->getLabel();
        $clientLabel = 'Contrat ' . $legalDocument->getContract()->getOwner()->getLabel();
        $ownerAccountRef = '419';
        $clientAccountRef = '409';
        $ownerIncomeRef = '706';
        $clientIncomeRef = '622';
        return $mandataireManager->createMandataire($legalDocument, $ownerLabel, $clientLabel, $ownerAccountRef, $clientAccountRef, $ownerIncomeRef, $clientIncomeRef);
    }

    public function transferContract(Contract $contract, $collaborator, &$listeEmails)
    {
        $side = $contract->getCollaboratorSide($collaborator);
        if ($side) {
            if ($side == 'owner') {
                $oldCollaborator = $contract->getOwnerCollaborator();
            } else {
                $oldCollaborator = $contract->getClientCollaborator();
            }
            if ($contract->transfer($collaborator)) {
                $this->persistAndFlush($contract);
                array_push($listeEmails, $this->getEmailConfirmationTransferContract($contract, $oldCollaborator));
            };
        }
    }

    public function removeContract(Contract $contract, &$listeEmails = null)
    {
        if ($contract->isRemovable()) {
            $this->removeAndFlush($contract);
        } else {
            throw new ApiException(400120);
        }
    }

    public function reactivateContract(Contract $contract, &$listeEmails)
    {
        $contract->reactivate();
        $this->persistAndFlush($contract);

        array_push($listeEmails, $this->getEmailConfirmationContract($contract));
    }

    public function beforeCancelSettlement($mandataire, $settlement, &$listeEmails)
    {
        $cooperonsManager = $this->container->get('apr_admin.cooperons_manager');

        if ($cooperonsManager->isMandataireCooperons($mandataire)) {
            return $cooperonsManager->beforeCancelSettlement($mandataire, $settlement, $listeEmails);
        } else {
            return true;
        }
    }

    public function beforeRecordSettlements($mandataire, &$allSettlements, &$listeEmails)
    {
        $autoEntrepreneurManager = $this->container->get('apr_auto_entrepreneur.auto_entrepreneur_manager');

        $contract = $mandataire->getContract();
        $autoEntrepreneur = $mandataire->getOwner()->getAutoEntrepreneur();

        if ($autoEntrepreneur && $contract->getType() == 'default') {
            $autoEntrepreneurManager->beforeRecordSettlements($mandataire, $allSettlements, $listeEmails);
        }
    }

    public function afterSettlements($mandataire, $allSettlements, &$listeEmails)
    {
        $cooperonsManager = $this->container->get('apr_admin.cooperons_manager');
        $autoEntrepreneurManager = $this->container->get('apr_auto_entrepreneur.auto_entrepreneur_manager');

        $contract = $mandataire->getContract();
        $autoEntrepreneur = $mandataire->getOwner()->getAutoEntrepreneur();

        if ($autoEntrepreneur && $contract->getType() == 'default') {
            $autoEntrepreneurManager->afterSettlements($mandataire, $allSettlements, $listeEmails);
        }

        if ($cooperonsManager->isMandataireCooperons($mandataire)) {
            $cooperonsManager->afterSettlements($mandataire, $allSettlements, $listeEmails);
        }
    }

    public function getLegalTemplate(Contract $contract, $member)
    {
        $myParty = $contract->getAuthorizedParty($member);
        $filterContract = $contract->getFilter($myParty);

        if (in_array($filterContract, array('affair:client', 'affair:owner'))) {
            return 'AprAutoEntrepreneurBundle:Legal:recruitment.html.twig';
        } elseif ($filterContract == 'default:owner') {
            return 'AprAutoEntrepreneurBundle:Legal:service.html.twig';
        }
    }

    public function getEmailConfirmationContract(Contract $contract)
    {
        $toMember = $contract->getOwnerMember();
        $otherMember = $contract->getClientMember();

        $mailParam = array(
            'to' => $toMember->getEmail(),
            'cc' => array($otherMember->getEmail()),
            'body' => array(
                'template' => 'AprContractBundle:Emails:notificationContract.html.twig',
                'parameter' => array(
                    'member' => $toMember,
                    'otherMember' => $otherMember,
                    'contract' => $contract,
                )
            )
        );
        $mailParam['subject'] = 'Création de votre contrat avec ' . $contract->getClient()->getLabel();
        return $mailParam;
    }

    public function getEmailConfirmationTransferContract(Contract $contract, $oldCollaborator)
    {
        $side = $contract->getCollaboratorSide($oldCollaborator);
        $methodName = "get" . ucfirst($side) . "Collaborator";
        $member = $contract->$methodName()->getMember();
        $label = 'contrat ' . $contract->getOwner()->getLabel();
        $mailParam = array();
        $mailParam['subject'] = "Changement de gestionnaire du " . $label;
        $mailParam['cc'] = array($oldCollaborator->getMember()->getEmail());
        $mailParam['to'] = $member->getEmail();
        $mailParam['body']['template'] = 'AprContractBundle:Emails:confirmationTransferCollaborator.html.twig';
        $mailParam['body']['parameter'] = array(
            'member' => $member,
            'label' => $label,
        );
        return $mailParam;
    }

    public function getStandByFor(ContractInvitation $invitation) {
        $filterContract = $invitation->getInfos();
        $arr = explode(":", $filterContract);
        $typeContract = $arr[0];
        $strOwnerClient = $arr[1];

        if($typeContract == 'affair') {
            return 'autoEntrepreneur';
        } else {
            if($strOwnerClient == 'client') return 'autoEntrepreneur';
            if($strOwnerClient == 'owner') return 'corporate';
        }
    }

    public function getEmailPostConfirmInvitation(ContractInvitation $invitation, $userMember){
        $arrLabels = array('autoEntrepreneur' => 'Auto-Entrepreneur', 'corporate' => 'Entreprise');

        $sponsorMember = $invitation->getSponsorMember();
        $standByFor = $this->getStandByFor($invitation);

        return array(
            'to' => $userMember->getEmail(),
            'subject' => 'Rappel: '.$sponsorMember->getFirstName().' '.$sponsorMember->getLastName().
                ' attend la création de votre Compte '.$arrLabels[$standByFor],
            'body' => array(
                'template' => 'AprContractBundle:Emails:notificationMemberContractWaitingFor.html.twig',
                'parameter' => array(
                    'member' => $userMember,
                    'sponsorMember' => $sponsorMember,
                    'standByFor' => $standByFor
                )),
        );
    }

    public function checkFilterContract($filterContract) {
        $arr = explode(":", $filterContract);
        $typeContract = array_search($arr[0], $this->arrTypes);
        if(is_null($typeContract)) throw new ApiException(400104);

        $strOwnerClient = $arr[1];
        if(!in_array($strOwnerClient, array('owner', 'client'))) throw new ApiException(400104);
        return array('type' => $arr[0], 'strOwnerClient' => $strOwnerClient);
    }
}