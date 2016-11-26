<?php
namespace Apr\ProgramBundle\Manager;

use Apr\CoreBundle\ApiException\ApiException;
use Doctrine\ORM\EntityManager;
use Apr\UserBundle\Manager\BaseManager as BaseManager;
use Apr\ProgramBundle\Entity\Member;
use Apr\ProgramBundle\Entity\SponsorshipBase;
use Apr\CoreBundle\Tools\Tools;

class MemberManager extends BaseManager {
    protected $em;
    protected $container;

    public function __construct(EntityManager $em, $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function getRepository()
    {
        return $this->em->getRepository('AprProgramBundle:Member');
    }

    public function createMember($email, $firstName='', $lastName='', $program = null){
        $member = New Member();
        $member->setEmail($email);
        if(!empty($firstName))$member->setFirstName($firstName);
        if(!empty($lastName))$member->setLastName($lastName);

        // Restriction PreProd si le program d'origine est en preprod (pas de connexion possible à compte prod ...)
        if(!is_null($program) && $program->getStatus() == 'preprod') $member->restrictPreProd();

        $this->persistAndFlush($member);
        return $member;
    }

    /**
     * Create a new member with User
     *
     */
    public function createMemberWithUserContact($user, $contact, $token, &$listeEmails){
        $tokenManager = $this->container->get('apr_user.token_manager');
        $userManager = $this->container->get('apr_user.user_manager');
        $coopPlusManager = $this->container->get('apr_admin.coop_plus_manager');

        $member = $this->createMember($user->getEmail(), $user->getFirstName(), $user->getLastName());
        $user = $userManager->addUser($user, $contact);
        $member->setUser($user);
        $this->persistAndFlush($member);

        $tokenObject = $tokenManager->loadToken($token);
        $coopPlusManager->postCreateMember($member, $tokenObject, $listeEmails);
        $this->connectToken($member, $token, $listeEmails);
    }

    public function connectToken(Member $member, $token, &$listeEmails) {
        $cooperonsManager = $this->container->get('apr_admin.cooperons_manager');
        $tokenManager = $this->container->get('apr_user.token_manager');
        $participatesToManager = $this->container->get('apr_program.participates_to_manager');

        $tokenObject = $tokenManager->loadToken($token);
        if($tokenObject->isParticipatesTo()) {
            $participatesToManager->replaceMemberAfterConnectToken($tokenObject->getObject(), $member);
        }

        $cooperonsManager->postConnectToken($member, $tokenObject, $listeEmails);
    }
    
    public function loadMemberById($id){
        return $this->getRepository()->findOneBy(array('id' => $id));
    }

    public function buildJsonMember($member){
        $result = array(
            'id' => $member->getId(),
            'lastName' => $member->getLastName(),
            'firstName' => $member->getFirstName(),
            'email' => $member->getEmail(),
            'remainingPoints' => $member->getRemainingPoints(),
            'totalAvantage' => $member->getTotalAvantage(),
        );
        return $result;
    }

    public function getAllParticipatesToMemberIds($member) {
        $allMembersId = array();
        foreach($member->getAllParticipatesTo() as $participatesTo) {
            foreach($participatesTo->getAffiliates() as $affiliate) {
                $memberId = $affiliate->getMember()->getId();
                if(!in_array($memberId, $allMembersId)) {
                    array_push($allMembersId, $memberId);
                }
            }
        }

        return $allMembersId;
    }

    public function buildSponsorshipInvitationDetails ($sponsorshipOrInvitation, $year, $programId, &$details) {
        // ToDo: AngularJS ...
        $sponsorShipManager = $this->container->get('apr_program.sponsorship_manager');
        $isSponsorship = ($sponsorshipOrInvitation instanceof SponsorshipBase ) ;

        if($isSponsorship) {
            $filleul = $sponsorshipOrInvitation->getMember();
        } else {
            $filleul = $sponsorshipOrInvitation;
        }

        $emailFilleul = $filleul->getEmail();
        $yearDate = $sponsorshipOrInvitation->getCreatedDate()->format('Y');
        $program = $sponsorshipOrInvitation->getProgram();

        $details['listeDates'][$filleul->getCreatedDate()->format('Y')] = 1;
        $details['listePrograms'][$program->getId()] = $program->getLabel();

        if(($year == 0 or $year == $yearDate) and ($programId == 0 or $programId == $program->getId())) {
            if(!isset($details['filleuls'][$emailFilleul])) {
                $details['filleuls'][$emailFilleul] = array(
                    "id"            => $filleul->getId(),
                    "email"         => $emailFilleul,
                    "lastName"      => $filleul->getLastName(),
                    "firstName"     => $filleul->getFirstName(),
                    "multipoints"   => 0,
                    "filleuls"  => 0,
                    "programs"      => array(),
                );
                if($isSponsorship) $details['nbre_filleuls']++;
            }

            if($isSponsorship) {
                $accountHistoryManager = $this->container->get('apr_program.account_points_history_manager');
                $accounts = $accountHistoryManager->loadMultiPointsBySponsor($sponsorshipOrInvitation->getAffiliate());
                $solde = 0;
                foreach ($accounts as $account){
                    $solde += $account->getPoints();
                    $details['total_multipoints_filleuls'] += $account->getPoints();
                    $details['total_multipoints'] += floor(2/3*$account->getPoints());
                }

                $details['filleuls'][$emailFilleul]['date'] = $filleul->getCreatedDate()->format('d/m/Y');
                $details['filleuls'][$emailFilleul]['multipoints'] += $solde;
                $details['filleuls'][$emailFilleul]['filleuls'] += count($sponsorShipManager->loadAllSponsorships($filleul));
            } else {
                $details['filleuls'][$emailFilleul]['date'] = "En attente ...";
            }

            $labelProgram = $program->getLabel();

            if(!in_array($program->getLabel(), $details['filleuls'][$emailFilleul]['programs'])){
                array_push($details['filleuls'][$emailFilleul]['programs'], $labelProgram);
            }
        }

        return;
    }

    public function getFilleulsDetails($member, $year = 0, $programId = 0) {
        $invitationsManager = $this->container->get('apr_program.invitation_manager');
        $sponsorShipManager = $this->container->get('apr_program.sponsorship_manager');

        $filleulsDetails = array('nbre_filleuls' => 0, 'total_multipoints' => 0, 'total_multipoints_filleuls' => 0,
            'filleuls' => array(), 'listeDates' => array(), 'listePrograms' => array());

        $invitations = $invitationsManager->loadAllInvitations($member);

        foreach($invitations as $invitation){
            $this->buildSponsorshipInvitationDetails($invitation, $year, $programId, $filleulsDetails);
        }

        $sponsorships = $sponsorShipManager->loadAllSponsorships($member);
        foreach ($sponsorships as $sponsorship) {
            $this->buildSponsorshipInvitationDetails($sponsorship, $year, $programId, $filleulsDetails);
        }

        // Préparation pour le Grid jQuery: transformation clé email -> i++
        $result = $filleulsDetails;
        foreach($filleulsDetails['filleuls'] as $emailFilleul => $filleul) {
            unset($result['filleuls'][$emailFilleul]);
            $filleul['programs'] = join('-', $filleul['programs']);
            array_push($result['filleuls'], $filleul);
        }

        return $result;
    }

    /**Emails**/

    public function BuildPoints($member, $programId = 0, $year = 0){
        $accountHistoryManager = $this->container->get('apr_program.account_points_history_manager');
        $result = array('allPoints'=>array(),'allAvantages' => array(), 'allYears' => array(), 'allPrograms' => array());

        $totalPoints = $totalAvantages = 0;
        // Points
        foreach($accountHistoryManager->loadPointsByMember($member) as $point){
            $date = $point->getCreatedDate();
            $yearDate = $date->format('Y');
            $date = date("d/m/Y", mktime(0, 0, 0, $date->format('m'), $date->format('d'), $date->format('Y')));
            $program = $point->getProgram();
            if($programId && $program->getId() != $programId) continue;
            if($year && $year != $yearDate) continue;
            array_push($result['allPoints'], array(
                'date'   => $date,
                'program'  => $program->getLabel(),
                'description'  => $point->getDescription(),
                'points' =>  $point->getPoints(),
            ));
            $totalPoints += $point->getPoints();
            $result['allYears'][$yearDate] = 1;
            $result['allPrograms'][$program->getId()] = $program->getLabel();
        }
        //Avantages
        foreach ($member->getAllAvantages() as $avantage) {
            $paymentDate = $avantage->getPaymentDate();
            array_push($result['allAvantages'], array(
                'date'   => $paymentDate->format('d/m/Y'),
                'description'  => $avantage->getDescription().' - '. number_format($avantage->getAmount(),2,',',' ').' €',
                'points' => $member->convertToPoints($avantage->getAmount(), true),
            ));
            $totalAvantages += $member->convertToPoints($avantage->getAmount(), true);
            $yearDate = ($paymentDate != '')?$paymentDate->format('Y'):0;
            if($yearDate) $result['allYears'][$yearDate] = 1;
        }

        arsort($result['allPoints']);
        arsort($result['allAvantages']);
        $result['totalPoints'] = $totalPoints;
        $result['totalAvantages'] = $totalAvantages;

        return $result;
    }

    public function buildGiftsPendingDetails() {
        $membersWithPendingGifts = $this->getRepository()->getMembersWithGiftsPending();
        $result = array();
        foreach($membersWithPendingGifts as $member) {
            $maxAvantage = $member->calculateMaxAvantage(null, true);
            if($maxAvantage) {
                $my = $this->buildJsonMember($member);
                $my['maxAvantage'] = $maxAvantage;
                $my['corporateRaisonSocial'] = (is_null($member->getCollege()))?null:$member->getCollege()->getCorporate()->getRaisonSocial();
                array_push($result, $my);
            }
        }

        return $result;
    }

    /**
     * Activate Account
     */
    public function getActivateMail($user, $activationUrl){
        return array(
            'to' => $user->getEmail(),
            'subject' => 'Activez dès maintenant votre compte Coopérons',
            'body' => array(
                'template' => 'AprProgramBundle:Emails:activateAccount.html.twig',
                'parameter' => array(
                    'user' => $user,
                    'generatedUrl' => $activationUrl,
                )
            )
        );
    }
    
    /**
     * Return member data
     *
     * @author Fondative <dev devteam@fondative.com>
     * @param $participatesTo 
     * @param $isCooperons 
     * @return array
     */
    public static function buildAPIJsonReturnData($participatesTo, $isCooperons) {
        $member = $participatesTo->getMember();
        $result = array(
            'userId'          => $participatesTo->getMemberProgramId(),
            'email'           => $member->getEmail(),
            'firstName'       => $member->getFirstName(),
            'lastName'        => $member->getLastName(),
            'isRegistered'    => $member->getUser()?true:false,
            'token'           => $participatesTo->getToken(),
            'mailInvitation'  => $participatesTo->getMailInvitation()?$participatesTo->getMailInvitation()->getCodeMail():null,
            'upline'          => $participatesTo->getUpline()
        );

        if($isCooperons) {
            if($member) {
                $result['isAutoEntrepreneur'] = $member->isAutoEntrepreneur();
                $result['remainingPoints'] = $member->getRemainingPoints();
            }
        }

        return $result;

    }

}

?>