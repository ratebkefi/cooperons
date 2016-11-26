<?php
namespace Apr\ProgramBundle\Manager;

use Apr\ProgramBundle\Entity\Program;
use Doctrine\ORM\EntityManager;
use Apr\UserBundle\Manager\BaseManager as BaseManager;
use Apr\ProgramBundle\Entity\Member;
use Apr\ProgramBundle\Entity\ParticipatesTo;
use Apr\ProgramBundle\Entity\PreProdParticipatesTo;

class ParticipatesToManager extends BaseManager {
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

    public function getProdOrPreProdRepository($status)
    {
        $programBundle = 'AprProgramBundle:';
        $entity = 'ParticipatesTo';
        $prefix = ($status == 'preprod')?'PreProd':'';

        return $this->em->getRepository($programBundle.$prefix.$entity);
    }

    public function replaceMemberAfterConnectToken(ParticipatesTo $participatesTo, Member $memberNew) {
        $memberOld = $participatesTo->getMember();
        // Pas de connexion possible avec un participatesTo en preprod => POINT CRITIQUE DU SYSTEME - PERMET DE SEPARER TOTALEMENT PROD / PRE-PROD
        if($memberOld != $memberNew && !$memberOld->isPreProd()  && !$memberNew->isPreProd()) {
            $participatesTo->setMember($memberNew);

            // Récupère solde des points gagnés le cas échéant ...
            $memberNew->addPoints($memberOld->getRemainingPoints());
            $this->removeAndFlush($memberOld);
        }
    }

    public function createParticipatesTo($program, $member, $memberProgramId, $codeMail='default'){
        $mailsManager = new MailsManager($this->em);
        $mail = $mailsManager->loadByCode($program, $codeMail);
        $participatesTo = ($program->getStatus() == 'preprod')?new PreProdParticipatesTo($program, $memberProgramId):new ParticipatesTo($program, $memberProgramId);
        //\Doctrine\Common\Util\Debug::dump($member);

        $participatesTo->setMember($member);

        if($mail){
            $participatesTo->setMailInvitation($mail);
        }
        $this->persistAndFlush($participatesTo);
        return $participatesTo;
    }

    /**
     * Create a new member from VALIDATED data
     */
    public function createParticipatesToWithData($program, $data, &$listeEmails){
        $memberManager = $this->container->get('apr_program.member_manager');
        $invitationManager = $this->container->get('apr_program.invitation_manager');

        if(!$program->isEasy()) {
            // Pour les program Easy, on crée sans userId ...
            $participatesTo = $this->loadParticipatesToByMemberProgramId($program, $data['userId']);
            if($participatesTo){
                // Fondative
                $params = array('ID' => $data['userId'], 'PROGRAM' => $program->getLabel());
                return array('error' => 40016, 'participatesTo' => $participatesTo, 'errorParams' => $params);
            }
        }

        $result = array();

        //Member
        $participatesTo = $this->loadParticipatesToByEmail($program, $data['email']);
        if(!$participatesTo) {
            $member = $memberManager->createMember($data['email'],$data['firstName'],$data['lastName'], $program);
            // Program Easy: memberProgramId = memberId ...
            if($program->isEasy()) {
                $data['userId'] = $member->getId();
            }
        } else
        {
            // Fondative
            $params = array('EMAIL' => $data['email'], 'PROGRAM' => $program->getLabel());
            return array('error' => 40015, 'participatesTo' => $participatesTo, 'errorParams' => $params);
        }
        $result['member'] = $member;

        //ParticipatesTo
        $participatesTo = $this->createParticipatesTo($program, $member, $data['userId']);
        $result['participatesTo'] = $participatesTo;

        // Sponsorship & Invitation

        if(isset($data['tokenInvitation']) && $data['tokenInvitation'] != '' && !is_null($data['tokenInvitation'])) {
            $result['sponsor'] = $invitationManager->confirmProgramInvitation($program, $data['tokenInvitation'], $participatesTo, $listeEmails);
        } else {
            $invitation = $invitationManager->loadInvitationByEmail($program, $data['email']);
            if($invitation) {
                $result['sponsor'] = $invitationManager->confirmProgramInvitation($program, $invitation->getToken(), $participatesTo, $listeEmails);
            }

            $result['sponsor'] = null;
        }

        if($program->isEasy()) {
            array_push($listeEmails, $this->getMailWelcomeEasy($participatesTo));
        }

        return $result;
    }

    public function updateCodeMail($participatesTo, $codeMail = 'default') {
        $mailsManager = new MailsManager($this->em);
        $mailInvitation = $mailsManager->loadByCode($participatesTo->getProgram(), $codeMail);
        if(!$codeMail) {
            $participatesTo->setMailInvitation(null);
            $this->persistAndFlush($participatesTo);
            return array('participatesTo' => $participatesTo);
        } elseif($mailInvitation){
            $participatesTo->setMailInvitation($mailInvitation);
            $this->persistAndFlush($participatesTo);
            return array('participatesTo' => $participatesTo);
        } else{
            // Fondative
            return array('error' => 40022, 'errorParams' => array('PROGRAM' => $participatesTo->getProgram()->getLabel()));
        }

    }

    public function buildUpline($participatesTo, $multiPoints = null) {
        $arrayId = $participatesTo->getUpline();
        $program = $participatesTo->getProgram();
        $upline = array();
        if(count($arrayId) > 0) {
            $allParticipatesTo = $this->getProdOrPreProdRepository($program->getStatus())
                ->getParticipatesToByArrayMemberProgramId($program, $arrayId);
            foreach($allParticipatesTo as $sponsor) {
                $sponsorId = $sponsor->getMemberProgramId();
                // Décalage de la clef: rang 0 = self ...
                $key = array_search($sponsorId, $arrayId)+1;
                if(is_null($multiPoints)) {
                    // Renvoie array(ParticipatesTo)
                    $upline[$key] = $sponsor;
                } else {
                    $multiPoints = floor(($multiPoints*2)/3);
                    $upline[$key] = array('participatesTo' => $sponsor, 'points' => $multiPoints);
                }
            }
        }
        ksort($upline);
        return $upline;
    }


    public function loadParticipatesToById($program, $id) {
        return $this->getProdOrPreProdRepository($program->getStatus())->findOneBy(array('program' => $program, 'id' => $id));
    }

    public function loadParticipatesToByMemberProgramId($program, $memberProgramId) {
        return $this->getProdOrPreProdRepository($program->getStatus())->findOneBy(array('program' => $program, 'memberProgramId' => $memberProgramId));
    }

    public function loadParticipatesToByMember($program, $member) {
        if(is_null($program) or is_null($member)) {
            return null;
        } else {
            return $this->getProdOrPreProdRepository($program->getStatus())->findOneBy(array('program' => $program, 'member' => $member));
        }
    }

    public function loadParticipatesToByToken($token){
            $participatesTo = $this->getProdOrPreProdRepository('prod')->findOneBy(array('token' => $token));
            if($participatesTo) {
                return $participatesTo;
            } else {
                return $this->getProdOrPreProdRepository('preprod')->findOneBy(array('token' => $token));
            }
    }

    public function loadParticipatesToByEmail($program, $email)
    {
        return $this->getProdOrPreProdRepository($program->getStatus())->getParticipatesToByEmail($program, $email);
    }

    public function buildJsonParticipatesTo($participatesTo){
        return array(
            'id' => $participatesTo->getId(),
            'points'=> $participatesTo->getTotal()['points'],
            'filleuls' => $participatesTo->getCountAffiliates(),
            'hasParrain' => $participatesTo->getSponsor()?true:false,
        );
    }

    public function buildFullJsonParticipatesTo($participatesTo){
        $my = array(
            "email"         => $participatesTo->getMember()->getEmail(),
            "lastName"      => $participatesTo->getMember()->getLastName(),
            "firstName"     => $participatesTo->getMember()->getFirstName(),
            "isUser"        => !is_null($participatesTo->getMember()->getUser())
        );
        return array_merge($my, $this->buildJsonParticipatesTo($participatesTo));
    }

    /**
     * Search participates to program
     *
     * @author Fondative <dev devteam@fondative.com>
     * @param $program Program
     * @param $search string
     * @return array
     */
    public function loadParticipates($program, $search)
    {
        $participatesTo = $this->getProdOrPreProdRepository($program->getStatus())->findParticipatesTo($program, $search);
        return $participatesTo;
    }

    public function searchParticipatesTo($program, $search, $sponsorExcludedFromUpline = null)
    {
        $result = array();

        if($program) {
            $allParticipatesTo = $this->getProdOrPreProdRepository($program->getStatus())->searchParticipatesTo($program, $search, $sponsorExcludedFromUpline);
            foreach($allParticipatesTo as $participatesTo) {
                array_push($result, $this->buildFullJsonParticipatesTo($participatesTo));
            }
        }
        return $result;

    }

    public function getMailWelcomeEasy($participatesTo){
        $member = $participatesTo->getMember();
        $program = $participatesTo->getProgram();
        return array(
            'to' => $member->getEmail(),
            'subject' =>(($program->getStatus() == 'preprod')?'[PRE-PRODUCTION] ':'').
                "Félicitations ! Vous pouvez désormais gagner jusqu'à 1'000 € de chèques cadeau Amazon par trimestre grâce au programme ".$program->getLabel(),
            'body' => array(
                'template' => 'AprProgramBundle:Emails:welcomeMemberEasy.html.twig',
                'parameter' => array(
                    'program' => $program,
                    'member' => $member,
                    'participatesTo' => $participatesTo
                )),
        );
    }


}

?>
