<?php
namespace Apr\ProgramBundle\Manager;

use Apr\ProgramBundle\Entity\Program;
use Doctrine\ORM\EntityManager;
use Apr\UserBundle\Manager\BaseManager as BaseManager;
use Apr\ProgramBundle\Entity\Invitation;
use Apr\ProgramBundle\Entity\PreProdInvitation;
use Apr\CoreBundle\Tools\Tools;

class InvitationManager extends BaseManager {
    protected $em;
    protected $container;

    public function __construct(EntityManager $em, $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function getRepository()
    {
        return $this->em->getRepository('AprProgramBundle:Invitation');
    }

    public function getProdOrPreProdRepository($status)
    {
        $programBundle = 'AprProgramBundle:';
        $entity = 'Invitation';
        $prefix = ($status == 'preprod')?'PreProd':'';

        return $this->em->getRepository($programBundle.$prefix.$entity);
    }

    public function createInvitation($program, $sponsor, $filleul) {
        $invitation = ($program->getStatus() == 'preprod')?
            new PreProdInvitation($sponsor, $filleul['firstName'], $filleul['lastName'], $filleul['email']):
            new Invitation($sponsor, $filleul['firstName'], $filleul['lastName'], $filleul['email']);
        $this->persistAndFlush($invitation);
        return $invitation;
    }

    public function refreshInvitation($program, $invitation = null, $token = null, &$listeEmails) {
        if(is_null($invitation)) $invitation = $this->loadInvitationByToken($token, $program);
        if($invitation) {
            $invitation->refresh();
            $this->persistAndFlush($invitation);
            
            $sponsor = $invitation->getSponsor();
            $mailInvitation =  $sponsor->getMailInvitation();
            if(!is_null($listeEmails)) {
                array_push($listeEmails, $this->getMailInvitation($mailInvitation, $sponsor, $invitation));
            }
        }
    }

    public function loadInvitationsBySponsor($sponsor){
        if(is_null($sponsor)) {
            return null;
        } else {
            return $this->getProdOrPreProdRepository($sponsor->getProgram()->getStatus())->findBy(array('sponsor' => $sponsor));
        }
    }

    public function loadInvitationByEmail($program, $email){
        if(is_null($program)) {
            return null;
        } else {
            return $this->getProdOrPreProdRepository($program->getStatus())->findOneBy(array('email' => $email, 'program' => $program));
        }
    }

    public function loadInvitationById($program, $id){
        if(is_null($program)) {
            return null;
        } else {
            return $this->getProdOrPreProdRepository($program->getStatus())->find($id);
        }
    }

    public function loadInvitationByToken($token, $program = null){
        $params = array('token' => $token);
        // Sécurité doublon $token - si on dispose du $program, filtre aussi sur $program ...
        if($program) $params['program'] = $program;

        $invitation = $this->getProdOrPreProdRepository('prod')->findOneBy($params);
        if($invitation) {
            return $invitation;
        } else {
            return $this->getProdOrPreProdRepository('preprod')->findOneBy($params);
        }
    }

    public function loadAllInvitations($member, $old = false)
    {
        if(is_null($member)) {
            return null;
        } else {
            return array_merge(
                $this->getProdOrPreProdRepository('preprod')->getInvitationsByMember($member, $old),
                $this->getProdOrPreProdRepository('prod')->getInvitationsByMember($member, $old)
            );
        }
    }

    public function purgeOldInvitations($member){
        $oldInvitations = $this->loadAllInvitations($member, true);
        if($oldInvitations) foreach($oldInvitations as $invitation) $this->em->remove($invitation);
        $this->flush();
    }

    public function createMultipleInvitations($participatesTo, $filleuls = array(), &$listeEmails = null, $postSponsor = false ){
        $sponsorShipManager = $this->container->get('apr_program.sponsorship_manager');
        $participatesToManager = $this->container->get('apr_program.participatesto_manager');
        $mailsManager = $this->container->get('apr_program.mails_manager');

        $program = $participatesTo->getProgram();
        $sponsorMember = $participatesTo->getMember();

        // ToDo: CRON ...
        $this->purgeOldInvitations($sponsorMember);

        $invitations = array();
        $filleulsNew = array();
        foreach ($filleuls as $filleul) {
            $invitationForSameEmail = $this->loadInvitationByEmail($program,  $filleul['email']);
            $emailAlreadyParticipatesTo = $participatesToManager->loadParticipatesToByEmail($program, $filleul['email']);
            if($invitationForSameEmail) {
                // Si une invitation existe déjà pour le même Sponsor, on la rafraichit ...
                if($invitationForSameEmail->getSponsor()->getId() == $participatesTo->getId()) {
                    $this->refreshInvitation($program, $invitationForSameEmail, null, $listeEmails);
                    array_push($filleulsNew, array(
                        'lastName' => $filleul['lastName'],
                        'firstName' => $filleul['firstName'],
                        'email' => $filleul['email']
                    ));

                    array_push($invitations, $invitationForSameEmail);
                } else {
                    if(!isset($invitations['error'])) {
                        $invitations['error'] = array();
                    }
                    array_push($invitations['error'],  $filleul['email']);
                }
            } elseif(!$emailAlreadyParticipatesTo){
                if(isset($filleul['mailInvitation'])) {
                    $mailInvitation = $mailsManager->loadByCode($program, $filleul['mailInvitation']);
                } else {
                    $mailInvitation = $participatesTo->getMailInvitation();
                }

                if(!is_null($mailInvitation)) {
                    // Si MailInvitation null : impossible de créer d'invitation ...
                    $invitation = $this->createInvitation($program, $participatesTo, $filleul);
                    if($invitation) {
                        if(!is_null($listeEmails)){
                            array_push($listeEmails, $this->getMailInvitation($mailInvitation, $participatesTo, $invitation));
                        }

                        array_push($filleulsNew, array(
                            'lastName' => $filleul['lastName'],
                            'firstName' => $filleul['firstName'],
                            'email' => $filleul['email']
                        ));

                        array_push($invitations, $invitation);

                        if($postSponsor) {
                            // Si appel via API - et filleul est devenu membre avant la création de l'invitation => création du sponsorship
                            $filleulParticipatesTo = $participatesToManager->loadParticipatesToByEmail($program, $filleul['email']);
                            if($filleulParticipatesTo){
                                $sponsorShipManager->createSponsorship($program, $participatesTo, $filleulParticipatesTo, $listeEmails);
                            }
                        }
                    }
                }
            } else {

                if(!isset($invitations['error'])) {
                    $invitations['error'] = array();
                }
                array_push($invitations['error'],  $filleul['email']);
            }
        }

        if(!is_null($listeEmails) && count($filleulsNew) > 0){
            array_push($listeEmails, $this->getMailConfirmationInvitation($participatesTo, $filleulsNew));
        }

        return $invitations;
    }

    public function confirmProgramInvitation($program, $token, $affiliate, &$listeEmails) {
        $sponsorshipManager = $this->container->get('apr_program.sponsorship_manager');

        $invitation = $this->loadInvitationByToken($token, $program);
        if($invitation) {
            if ($invitation->getProgram()->getId() == $program->getId()) {
                $sponsor = $invitation->getSponsor();
                $sponsorshipManager->createSponsorship($program, $sponsor, $affiliate, $listeEmails);
            } else {
                return array('error' => 40019);
            }
        } else $sponsor = null;
        return $sponsor;
    }

    public function buildJsonInvitation($invitation){
        return array(
            'id' => $invitation->getId(),
            "email"         => $invitation->getEmail(),
            "lastName"      => $invitation->getLastName(),
            "firstName"     => $invitation->getFirstName(),
            "filleuls"      => "#N/A",
            "points"        => "#N/A",
        );
    }

    /**
     * Search invitations by program and label(for generic API service)
     *
     * @author Fondative <dev devteam@fondative.com>
     * @param $program Program Invitations program
     * @param $search string Searching label
     * @return array
     */
    public function loadInvitationsByProgram($program, $search)
    {
        $invitations = $this->getProdOrPreProdRepository($program->getStatus())->getInvitationsByProgram($program, $search);
        return $invitations;
    }

    public function searchInvitations($program, $search)
    {
        $result = array();

        if($program) {
            $allInvitations = $this->getProdOrPreProdRepository($program->getStatus())->searchInvitations($program, $search);
            foreach($allInvitations as $invitation) {
                array_push($result, $this->buildJsonInvitation($invitation));
            }
        }
        return $result;
    }

    public function getMailInvitation($invitationEmail, $sponsor, $invitation){
        $coopPlusManager = $this->container->get('apr_admin.coop_plus_manager');

        $sponsorMember = $sponsor->getMember();
        $sponsorLastName = $sponsorMember->getLastName();
        $sponsorFirstName = $sponsorMember->getFirstName();
        $invitationLastName = $invitation->getLastName();
        $invitationFirstName = $invitation->getFirstName();

        if(!$invitationEmail) return;
        $program = $invitationEmail->getProgram();
        $parent = $sponsor->getSponsor();

        // Subject
        $subject = $invitationEmail->getSubject();

        // Body
        $body = $invitationEmail->getHeader().$invitationEmail->getContent().$invitationEmail->getFooter();

        foreach(array('subject', 'body') as $varName) {
            $$varName = str_replace('%LASTNAME%',  $invitationLastName, $$varName);
            $$varName = str_replace('%FIRSTNAME%',  $invitationFirstName, $$varName);
            $$varName = str_replace('%LASTNAME_FILLEUL%',  $invitationLastName, $$varName);
            $$varName = str_replace('%FIRSTNAME_FILLEUL%',  $invitationFirstName, $$varName);
            $$varName = str_replace('%LASTNAME_PARRAIN%',  $sponsorLastName,$$varName);
            $$varName = str_replace('%FIRSTNAME_PARRAIN%',  $sponsorFirstName,$$varName);

            if($parent){
                $$varName = str_replace('%LASTNAME_PARENT%',  $parent->getMember()->getLastName(),$$varName);
                $$varName = str_replace('%FIRSTNAME_PARENT%',  $parent->getMember()->getFirstName(),$$varName);
            }
        }

        // Landing URL
        $token = $invitation->getToken();
        if($program->isEasy()) {
            $url = "http://".$this->container->get('router')->getContext()->getHost().$this->container->get('router')->generate('landing_easy');
        } elseif($coopPlusManager->isProgramPlus($program)) {
            $url = "http://".$this->container->get('router')->getContext()->getHost().$this->container->get('router')->generate('landing_invitation_plus');
        } else {
            $url = $program->getLandingUrl();
        }

        if($url){
            if (substr($url, 0, 7) != "http://" && substr($url, 0, 8) != "https://")
            {
                $url = 'http://'.$url;
            }

            $url .= '?tkn='.$token ;
        }else{
            $url = "ATTENTION: LIEN D'INVITATION NON PARAMETRE DANS CONFIGURATION";
        }

        $body = str_replace('%LANDING_URL%',  $url, $body);

        // Préproduction Préfixe Subject
        if($program->getStatus() == 'preprod'){
            $subject = '[PRE-PRODUCTION] '.$subject;
        }

        $body = str_replace("\n", "<br />", $body);
        $body = str_replace("<br /><br />", "<br />", $body);

        // ToDo: Permettre la gestion d'adresse emails expéditrices avec répondeur automatique ...
        return array(
            'to' => $invitation->getEmail(),
            'subject' => $subject,
            'body' => $body,
            'cc' => array($program->getSenderEmail() => $program->getSenderName()),
        );
    }

    public function getMailConfirmationInvitation($sponsor, $filleuls){
        $str = '';
        $sponsorMember = $sponsor->getMember();
        $program = $sponsor->getProgram();
        foreach($filleuls as $filleul){
            if($str != '') $str .=', ';
            if(isset($filleul['firstName']) && isset($filleul['lastName'])){
                $str .= $filleul['firstName'].' '.$filleul['lastName'];
            }else{
                $str .= $filleul['email'];
            }
        }
        return array(
            'to' => $sponsorMember->getEmail(),
            'subject' =>(($program->getStatus() == 'preprod')?'[PRE-PRODUCTION] ':'').'Vous avez gagné un nouveau filleul Coopérons Plus: '.$str,
            'body' => array(
                'template' => 'AprProgramBundle:Emails:confirmationInvitation.html.twig',
                'parameter' => array(
                    'label_program' => $program->getLabel(),
                    'parrain' => $sponsorMember,
                    'filleuls' => $filleuls,
                )),
        );
    }

}

?>
