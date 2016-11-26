<?php

namespace Apr\AdminBundle\Manager;

use Doctrine\ORM\EntityManager;
use Apr\UserBundle\Manager\BaseManager as BaseManager;
use Apr\ProgramBundle\Entity\Member;
use Apr\ProgramBundle\Entity\Program;
use Apr\UserBundle\Entity\Token;

class CoopPlusManager extends BaseManager
{

    protected $em;
    protected $container;

    private $abonnementEasy=39;
    private $abonnementAPI=79;

    private $pricePoint = 0.25;

    public function __construct(EntityManager $em, $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function getRepository() {
        // Nécessaire pour implémenter BaseManager - non utilisé
    }

    public function isProgramPlus($program){
        $idProgramPlus = $this->container->getParameter('idProgramPlus');
        return $program->getId() == $idProgramPlus;
    }

    public function loadProgramPlus() {
        $programManager = $this->container->get('apr_program.program_manager');
        $idProgramPlus = $this->container->getParameter('idProgramPlus');
        return $programManager->loadProgramById($idProgramPlus);
    }

    public function postCreateMember(Member $member, Token $tokenObject, &$listeEmails) {
        // Inscription suite à Invitation Plus ou ParticipatesTo a nécessité l'acceptation des CGU Membres
        if($tokenObject->hasProgram()) $member->acceptCGUMember();
        $this->createParticipatesToPlus($member);
    }

    public function createParticipatesToPlus(Member $member) {
        $participatesToManager = $this->container->get('apr_program.participates_to_manager');
        $programPlus = $this->loadProgramPlus();
        $participatesToManager->createParticipatesTo($programPlus, $member, $member->getUser()->getId());
    }

    public function loadParticipatesToPlus(Member $member) {
        $participatesToManager = $this->container->get('apr_program.participates_to_manager');
        return $participatesToManager->loadParticipatesToByMember($this->loadProgramPlus(), $member);
    }

    public function postInvitationPlus(&$invitations, $idCollaborator, $typeInvitation, &$listeEmails) {
        $corporateManager = $this->container->get('apr_corporate.corporate_manager');
        $collaboratorManager = $this->container->get('apr_contract.collaborator_manager');
        $collegeManager = $this->container->get('apr_corporate.college_manager');
        $autoEntrepreneurManager = $this->container->get('apr_auto_entrepreneur.auto_entrepreneur_manager');
        $userManager = $this->container->get('apr_user.user_manager');

        if(isset($invitations['error'])) {
            $email = $invitations['error'][0];
            $user = $userManager->loadUserByEmail($email);
            $member = $user->getMember();
            if($typeInvitation == 'college') {
                $allCorporatesAsAdministrator = $corporateManager->getAllCorporatesByAdministratorMember($member);
                $invitations['error'] = $member->getFirstName()." ".$member->getLastName()." dispose déjà d'un compte Utilisateur";
                if(count($allCorporatesAsAdministrator)) {
                    $invitations['error'] .= " et est bien administrateur d'un Compte Entreprise: merci de vérifier le SIREN saisi.";
                } else {
                    $invitations['error'] .= " mais n'a encore créé aucun Compte Entreprise";
                }
            } elseif(in_array($typeInvitation, array('affair:client', 'affair:owner', 'default:client'))) {
                $autoEntrepreneur = $member->getAutoEntrepreneur();
                if($autoEntrepreneur) {
                    $invitations['autoEntrepreneur'] = $autoEntrepreneurManager->buildJson($autoEntrepreneur);
                } else {
                    $invitations['autoEntrepreneur'] = array();
                }
            } elseif($typeInvitation == 'collaborator')  {
                $collaborator = $collaboratorManager->loadCollaboratorById($idCollaborator);
                if($collaborator) {
                    $collaboratorManager->createCollaborator($collaborator->getCorporate(), $member, $listeEmails);
                    unset($invitations['error']);
                }
            }
        } else {
            $invitation = $invitations[0];
            if($typeInvitation == 'college') {
                $collegeManager->createInvitationCollege($invitation, $listeEmails);
            } elseif($typeInvitation == 'collaborator') {
                $collaborator = $collaboratorManager->loadCollaboratorById($idCollaborator);
                if($collaborator) {
                    $collaboratorManager->inviteCollaborator($collaborator->getParty(), $invitation, $listeEmails);
                }
            }
        }
    }

    public function createSponsorshipPlusAfterInvitation($invitation, Member $userMember, &$listeEmails) {
        $sponsorShipManager = $this->container->get('apr_program.sponsorship_manager');
        $participatesToPlus = $this->loadParticipatesToPlus($userMember);

        $sponsorShipManager->createSponsorship($participatesToPlus->getProgram(),
            $this->loadParticipatesToPlus($invitation->getSponsorMember()),
            $participatesToPlus, $listeEmails);
    }

    public function givePointsSponsorPlusIfFirstActive(Program $program, &$listeEmails)
    {
        $historyPointsManager = $this->container->get('apr_program.account_points_history_manager');
        $programManager = $this->container->get('apr_program.program_manager');
        $labelOperation = $this->container->getParameter('code.operation.points.premier_program_filleul');

        // Les points sont offerts au parrain de l'Administrateur (et non du Collaborator ...)
        $member = $program->getCollaborator()->getParty()->getAdministrator()->getMember();

        if($programManager->getRepository()->countActiveProgramsAsAdministrator($member) == 0) {
            $participatesToPlus = $this->loadParticipatesToPlus($member);
            $sponsor = $participatesToPlus->getSponsor();
            if($sponsor){
                $datas =  array(array(
                    'labelOperation' => $labelOperation,
                    'info' => $member->getFirstName()." ".$member->getLastName()
                ));
                $historyPointsManager->addPoints($sponsor, $datas, $listeEmails);
            }
        }
        return ;
    }

    public function calculatePoints(Program $program, $points) {
        $settlementsManager = $this->container->get('apr_mandataire.settlements_manager');
        $result = $settlementsManager->calculateSettlement($program->getCorporate()->getParty()->getMandataire(), $this->pricePoint, $points);
        $result['program'] = $program;
        return $result;
    }

    public function beforeCancelSettlement($settlement, &$listeEmails) {
        // Bien supprimer la référence au $settlement pour ne pas avoir de problème de FK sur DELETE ...
        $type = $settlement->getType();

        if($type == 'points') {
            $historyPointsManager = $this->container->get('apr_program.account_points_history_manager');
            $historyPointsManager->cancelPointsBySettlement($settlement, $listeEmails);
            return true;
        } elseif($type == 'abonnement') {
            // Expiration confirmée du programme ...
            $program = $settlement->getProgramWithPendingSettlementAbonnement();
            $program->cancel();
            array_push($listeEmails, $this->getEmailResiliationProgram($program));
            return true;
        } else {
            return false;
        }
    }

    public function afterSettlement($settlement, &$listeEmails) {
        $programManager = $this->container->get('apr_program.program_manager');
        $historyPointsManager = $this->container->get('apr_program.account_points_history_manager');

        $type = $settlement->getType();
        if($type == 'abonnement') {
            $program = $settlement->getProgramWithPendingSettlementAbonnement();
            $programManager->renewal($program, $listeEmails);
        } elseif($type == 'points') {
            $historyPointsManager->confirmPointsBySettlement($settlement, $listeEmails);
        }
    }

    public function calculateAbonnement(Program $program) {
        $settlementsManager = $this->container->get('apr_mandataire.settlements_manager');
        $unitAmount = $program->isEasy()?$this->abonnementEasy:$this->abonnementAPI;
        $result = $settlementsManager->calculateSettlement($program->getCorporate()->getParty()->getMandataire(), $unitAmount, 12);
        $result['program'] = $program;
        return $result;
    }

    public function createPendingSettlementAbonnement(Program $program){
        $settlementsManager = $this->container->get('apr_mandataire.settlements_manager');
        $cooperonsManager = $this->container->get('apr_admin.cooperons_manager');

        if(!$cooperonsManager->isCorporateCooperons($program->getCorporate())) {
            if($program->getOldProgram()) {
                return $this->createPendingSettlementAbonnement($program->getOldProgram());
            } else {
                // Le program a-t-il un premier SettlementAbonnement ? Ou alors, son SettlementAbonnement est "ancien" (settled - abonnement précédent)
                if($program->getStatus() != 'cancel' && (is_null($program->getSettlementAbonnement()) or $program->getSettlementAbonnement()->getStatus() == "settled")) {

                    $refreshDateExpiration = !is_null($program->getNewProgram());
                    $dateExpiration = $program->getNewDateExpiration($refreshDateExpiration);
                    $description = 'abonnement annuel au service Coopérons Plus';
                    if(!$program->isEasy()) $description .= ' (Option API)';
                    $description .= ' (expirant le '.$dateExpiration->format('d/m/Y').')';

                    $result = $this->calculateAbonnement($program);
                    $settlement = $settlementsManager->createSettlement($program->getCorporate()->getParty()->getMandataire(), $description, $result, 'abonnement');
                    $program->setPendingSettlementAbonnement($settlement);
                    return $settlement;
                }
            }
        }
    }

    public function updateAbonnement(Program $program, &$listeEmails)
    {
        $settlementsManager = $this->container->get('apr_mandataire.settlements_manager');
        $cooperonsManager = $this->container->get('apr_admin.cooperons_manager');

        if(!$cooperonsManager->isCorporateCooperons($program->getCorporate())) {
            $settlement = $this->createPendingSettlementAbonnement($program);
            if($settlement) {
                $this->persistAndFlush($settlement);
                $mandataire = $program->getCorporate()->getParty()->getMandataire();
                $settlementsManager->updateWaitingSettlements($mandataire, $listeEmails);
            }
        }
    }

    // Mandataire - $this->flush géré par MandataireBundle

    public function getEmailWelcomeProgram(Program $program){
        $mailParam = array();
        $mailParam['subject'] = "Coopérons - ".$program->getLabel().": votre réseau d'apporteurs d'affaires est prêt à grandir !";
        $mailParam['to'] = $program->getCollaborator()->getMember()->getEmail();
        if($program->isEasy()){
            $mailParam['body']['template'] = 'AprAdminBundle:Emails/CoopPlus:welcomeProgramEasy.html.twig';
        }else{
            $mailParam['body']['template'] = 'AprAdminBundle:Emails/CoopPlus:welcomeProgramAPI.html.twig';
        }
        $mailParam['body']['parameter'] = array(
            'program'     => $program,
        );
        return $mailParam;
    }

    public function getEmailResiliationProgram(Program $program){
        $member = $program->getCollaborator()->getMember();
        $mailParam = array();
        $mailParam['subject'] = 'Résiliation de votre programme '.$program->getLabel();
        $mailParam['to'] = $member->getEmail();
        $mailParam['body']['template'] = 'AprAdminBundle:Emails/CoopPlus:cancelProgram.html.twig';
        $mailParam['body']['parameter'] = array(
            'member'   => $member,
            'program'  => $program,
        );
        return $mailParam;
    }


}