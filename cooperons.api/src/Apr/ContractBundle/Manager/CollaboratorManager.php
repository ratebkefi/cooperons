<?php

namespace Apr\ContractBundle\Manager;

use Apr\CoreBundle\ApiException\ApiException;
use Doctrine\ORM\EntityManager;
use Apr\UserBundle\Manager\BaseManager as BaseManager;
use Apr\Classes\Tools;
use \Apr\ContractBundle\Entity\Party;
use \Apr\ContractBundle\Entity\Collaborator;
use Apr\ContractBundle\Entity\ContractInvitation;

class CollaboratorManager extends BaseManager
{

    protected $em;
    protected $container;

    public function __construct(EntityManager $em, $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function securityCheck($member, Collaborator $collaborator, $adminOnly = false)
    {
        if ($collaborator->getMember() != $member) throw new ApiException(403101);

        if ($adminOnly && !$collaborator->isAdministrator()) {
            throw new ApiException(403101);
        }
    }

    public function getRepository()
    {
        return $this->em->getRepository('AprContractBundle:Collaborator');
    }

    public function loadCollaboratorByMember(Party $party, $member)
    {
        return $this->getRepository()->findOneBy(array('party' => $party, 'member' => $member));
    }

    public function loadCollaboratorById($id)
    {
        return $id?$this->getRepository()->find($id):null;
    }

    public function createCollaborator(Party $party, $member, &$listeEmails) {
        if(!$this->loadCollaboratorByMember($party, $member)) {
            $collaborator = new Collaborator($party, $member);
            $this->persistAndFlush($collaborator);

            if(!is_null($member)) {
                array_push($listeEmails, $this->getMailWelcomeCollaborator($collaborator));
            }
        };
    }

    public function inviteCollaborator(Party $party, $invitation, &$listeEmails){
        $collaborator = new Collaborator($party);
        $collaborator->setInvitation($invitation);
        $this->persistAndFlush($collaborator);
        array_push($listeEmails, $this->getMailInvitationCollaborator($collaborator));
    }

    public function confirmCollaborator(Collaborator $collaborator, $member, &$listeEmails) {
        $collaborator->confirm($member);
        $this->persistAndFlush($collaborator);

        array_push($listeEmails, $this->getMailWelcomeCollaborator($collaborator));
    }

    public function transferCollaborator(Collaborator $collaborator, $transfer, &$listeEmails) {
        $contractManager = $this->container->get('apr_contract.contract_manager');

        if(!is_null($transfer) && $transfer->getId() != $collaborator->getId()) {
            $party = $collaborator->getParty();
            $member = $collaborator->getMember();
            $wasAdministrator = $collaborator->isAdministrator();

            $collaborator->transfer($transfer);

            foreach($collaborator->getAllContracts() as $contract) {
                $contractManager->transferContract($contract, $transfer, $listeEmails);
            }

            $this->removeAndFlush($collaborator);

            if($wasAdministrator) {
                array_push($listeEmails, $this->getMailNewAdministrator($party));
            }

            array_push($listeEmails, $this->getMailLeaveCollaborator($party, $member));
        }
    }

    public function leaveCollaborator(Collaborator $collaborator, $transfer = null, &$listeEmails) {
        $corporateManager = $this->container->get('apr_corporate.corporate_manager');

        $corporate = $collaborator->getCorporate();
        $administrator = $corporate->getParty()->getAdministrator();

        if(is_null($collaborator->getMember())) {
            // Invitation ...
            $this->removeAndFlush($collaborator);
        } elseif($collaborator->isAdministrator() && is_null($transfer)) {
            $corporateManager->cancelCorporate($corporate, $listeEmails);
        } else {
            if(is_null($transfer)) $transfer = $administrator;
            $this->transferCollaborator($collaborator, $transfer, $listeEmails);
        }
    }


    // Invitation Contract

    public function createInvitationContract(Collaborator $collaborator, $filterContract, $filleul, &$listeEmails)
    {
        $userManager = $this->container->get('apr_user.user_manager');
        $contractManager = $this->container->get('apr_contract.contract_manager');
        $strOwnerClient = $contractManager->checkFilterContract($filterContract)['strOwnerClient'];

        $emailAlreadyHasUser = $userManager->loadUserByEmail($filleul['email']);

        $result = array();

        if($emailAlreadyHasUser) {
            $member = $emailAlreadyHasUser->getMember();
            $allCollaborators = $member->getAllCollaborators();

            $result['collaborators'] = array();

            if(count($allCollaborators)) {
                foreach($allCollaborators as $my_collaborator) {
                    if($strOwnerClient == 'client' && !$my_collaborator->getParty()->getStatus()['Contracts']['cpContracts']) continue;
                    $result['collaborators'][$my_collaborator->getId()] =  $my_collaborator->getCorporate()->getRaisonSocial();
                }
            }
            
        } else {
            $invitation = new ContractInvitation($collaborator, $filterContract, $filleul['firstName'], $filleul['lastName'], $filleul['email']);
            $this->persistAndFlush($invitation);

            array_push($listeEmails, $this->getEmailInvitationContract($invitation));
        }
        return $result;
    }

    public function getEmailInvitationContract(ContractInvitation $invitation){
        $contract = $invitation->getContract();
        if(!is_null($contract)) {
            $member = $invitation->getCollaborator()->getMember();
            $subject = $member->getLabel(). ' vous invite à découvrir Coopérons !';

            return array(
                'to' => $invitation->getEmail(),
                'subject' => $subject,
                'body' => array(
                    'template' => 'AprContractBundle:Emails:invitationContract.html.twig',
                    'parameter' => array(
                        'member' => $member,
                        'invitation' => $invitation,
                        'contract' => $contract,
                    )),
            );
        }
    }

    public function getMailWelcomeCollaborator(Collaborator $collaborator){
        $member = $collaborator->getMember();
        $corporate = $collaborator->getCorporate();
        return array(
            'to' => $member->getEmail(),
            'subject' =>'Vous avez rejoint le compte Entreprise de '.$corporate->getRaisonSocial(),
            'body' => array(
                'template' => 'AprContractBundle:Emails:welcomeCollaborator.html.twig',
                'parameter' => array(
                    'member' => $member,
                    'corporate' => $corporate
                )),
        );
    }

    public function getMailInvitationCollaborator(Collaborator $collaborator){
        $party = $collaborator->getParty();
        $invitation = $collaborator->getInvitation();
        return array(
            'to' => $invitation->getEmail(),
            'subject' =>'Création de compte utilisateur pour le compte Entreprise de '.$party->getCorporate()->getRaisonSocial(),
            'body' => array(
                'template' => 'AprContractBundle:Emails:invitationCollaborator.html.twig',
                'parameter' => array(
                    'invitation' => $invitation,
                    'party' => $party,
                    'token' => $invitation->getToken()
                )),
        );
    }


    public function getMailNewAdministrator(Party $party)
    {
        $corporate = $party->getCorporate();
        $member = $party->getAdministrator()->getMember();
        return array(
            'to' => $member->getEmail(),
            'subject' => "Nouvel Administrateur du compte Entreprise " . $corporate->getRaisonSocial(),
            'body' => array(
                'template' => 'AprContractBundle:Emails:confirmationNewAdministrator.html.twig',
                'parameter' => array(
                    'member' => $member,
                    'corporate' => $corporate
                )),
        );
    }

    public function getMailLeaveCollaborator(Party $party, $member){
        $administrator = $party->getAdministrator();
        $corporate = $party->getCorporate();

        $ccArr = array();
        if($administrator->getMember()->getId() != $member->getId()) array_push($ccArr, $administrator->getMember()->getEmail());
        return array(
            'to' => $member->getEmail(),
            'cc' => $ccArr,
            'subject' => "Vous n'êtes plus utilisateur du compte Coopérons ! de ".$corporate->getRaisonSocial(),
            'body' => array(
                'template' => 'AprContractBundle:Emails:leaveCollaborator.html.twig',
                'parameter' => array(
                    'member' => $member,
                    'corporate' => $corporate,
                )),
        );
    }

}

?>
