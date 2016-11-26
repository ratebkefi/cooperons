<?php

namespace Apr\CorporateBundle\Manager;

use Apr\CoreBundle\ApiException\ApiException;
use Apr\CorporateBundle\Entity\Attestation;
use Doctrine\ORM\EntityManager;
use Apr\UserBundle\Manager\BaseManager as BaseManager;
use Apr\CoreBundle\Tools\Tools;
use Apr\CorporateBundle\Entity\College;

class CollegeManager extends BaseManager
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
        return $this->em->getRepository('AprCorporateBundle:College');
    }

    public function loadCollegeById($id)
    {
        return $this->getRepository()->find($id);
    }

    /**
     * Get member colleges
     * @author Fondative <devteam@fondative.com>
     *
     * @param $member
     * @return array colleges
     */
    public function getAllCollegesOfMember($member){
        return $this->getRepository()->findBy(array('member' => $member));
    }

    public function getAllCollegesOfMemberForCorporate($member, $corporate){
        return $this->getRepository()->findBy(array('member' => $member, 'corporate' => $corporate));
    }

    public function getAllCollegesByCorporate($corporate, $includeStandBy){
        return $this->getRepository()->getAllCollegesByCorporate($corporate, null, $includeStandBy);
    }

    public function createInvitationCollege($invitation, &$listeEmails)
    {
        if(!is_null($invitation)) {
            $member = $invitation->getSponsor()->getMember();
            if(!$member->getCollege()){
                $college = new College(null, $member);
                $college->setInvitation($invitation);
                $this->persistAndFlush($college);

                array_push($listeEmails, $this->getMailInvitationCollege($invitation));
            }
        }
    }

    public function candidateCollege($member, $corporate,  &$listeEmails)
    {
        if(!$member->getCollege() and !is_null($corporate)){
            $college = new College($corporate, $member);
            $this->persistAndFlush($college);
            if($corporate->getAdministrator()->getMember()->getId() != $member->getId()) {
                $this->broadcastNewCandidateAllColleges($college, $listeEmails);
            } else {
                $this->confirmCollege($college, $listeEmails);
            }
        }
    }

    public function confirmCollege(College $college, &$listeEmails)
    {
        $welcome = $college->confirm();
        $this->persistAndFlush($college);

        if($welcome) {
            array_push($listeEmails, $this->getMailWelcomeCollege($college));
        } else {
            array_push($listeEmails, $this->getMailConfirmationCollegeToDelegate($college));
        }
    }

    public function leaveCollege(College $college, &$listeEmails)
    {
        $newDelegate = null;
        $corporate = $college->getCorporate();

        // new Delegate ...
        if($corporate) {
            $delegate = $corporate->getDelegate();
            if ($delegate) {
                if ($college->getId() == $delegate->getId()) {
                    $allColleges = $this->getRepository()->getAllCollegesByCorporate($corporate, $delegate);
                    if ($allColleges) {
                        $newDelegate = $allColleges[0];
                    }
                }
            }
        }

        $status = $college->getStatus();
        if(in_array($status,array('wait_for_corporate', 'wait_for_delegate'))) {
            $college->leave();
            $this->removeAndFlush($college);
        } else {
            $college->leave($newDelegate);
            $this->persistAndFlush($college);
            if($college->getCorporate()->getDelegate()) {
                array_push($listeEmails, $this->getMailLeaveCollege($college));
            }
        }
    }

    public function changeDelegate($delegate,  &$listeEmails)
    {
        $corporate = $delegate->getCorporate();
        $corporate->setDelegate($delegate);
        $this->persistAndFlush($delegate);
    }

    public function buildYearlyAttestations($memberCorporate, $member, $year = null, $validate = false, &$listeEmails = null) {
        $avantageManager = $this->container->get('apr_program.avantage_manager');

        if(is_null($member)) {
            $allAttestations = $avantageManager->getAllAttestations($year, $memberCorporate);
        } else {
            $allAttestations = $member->getAllAttestations();
        }

        $yearlyAttestation = $myCorporate = $myMember = null;
        $result = array();
        foreach($allAttestations as $attestation) {
            $member = $attestation->getMember();
            $corporate = $attestation->getCorporate();
            $year = $attestation->getYear();
            if(is_null($myMember) or is_null($myCorporate) or $member->getId() != $myMember->getId() or  $corporate->getId() != $myCorporate->getId()) {
                if($yearlyAttestation) {
                    if($validate) {
                        $this->confirmAttestation($year, $yearlyAttestation, $listeEmails);
                    }
                    $result[] = $yearlyAttestation;
                }
                $myMember = $member;
                $myCorporate = $corporate;
                $yearlyAttestation = array(
                    'member' => $member->getLabel(),
                    'corporate' => $corporate->getRaisonSocial(),
                    'year' => $year,
                    'fileName' => $attestation->buildRef($year, $member, $corporate),
                    'amount' => 0,
                    'cotisation' => 0,
                    'attestations' => array());
            }
            $yearlyAttestation['amount'] += $attestation->getTotalAvantage();
            $yearlyAttestation['cotisation'] += $attestation->getCotisation();
            $yearlyAttestation['attestations'][] = $attestation;
            if($validate) {
                $this->persist($attestation);
            }
        }

        if($yearlyAttestation) {
            if($validate) {
                $this->confirmAttestation($year, $yearlyAttestation, $listeEmails);
            }
            $result[] = $yearlyAttestation;
        }

        if($validate) {
            $this->flush();
        }

        return $result;
    }

    public function confirmAttestation($year, $yearlyAttestation, &$listeEmails) {
        $member = $yearlyAttestation['attestations'][0]->getMember();
        $corporate = $yearlyAttestation['attestations'][0]->getCorporate();
        $this->generatePDFAttestationAnnuelle($year, $member, $corporate, $yearlyAttestation);
        array_push($listeEmails, $this->getMailAttestationAnnuelle($year, $member, $corporate, $yearlyAttestation));
    }

    /**
     * @author Fondative <devteam@fondative.com>
     * @param $id
     * @return Attestation
     */
    public function getAttestation($id){
        $repository = $this->em->getRepository('AprCorporateBundle:Attestation');
        return $repository->find($id);
    }

    public function broadcastNewCandidateAllColleges(College $candidate, &$listeEmails)
    {
        $corporate = $candidate->getCorporate();
        if(is_null($corporate)) {
            $invitation = $candidate->getInvitation();
            array_push($listeEmails, $this->getMailInvitationCollege($invitation));
        } elseif($corporate->getDelegate()) {
            $allColleges = $this->getRepository()->getAllCollegesByCorporate($corporate, $candidate);
            foreach($allColleges as $college) {
                array_push($listeEmails, $this->getMailNewCandidateAllColleges($college, $candidate));
            }
        } else {
            array_push($listeEmails, $this->getMailNewCollegeWaitingForAdministrator($candidate));
        }

    }

    public function getMailInvitationCollege($invitation){
        $member = $invitation->getSponsor()->getMember();
        return array(
            'to' => $invitation->getEmail(),
            'subject' =>'[Le bon plan de '.$member->getLabel().
                ']: Découvrez comment augmenter la rémunération de vos salariés sans aucun coût pour votre entreprise',
            'body' => array(
                'template' => 'AprCorporateBundle:Emails:invitationCollege.html.twig',
                'parameter' => array(
                    'member' => $member,
                    'invitation' => $invitation
                )),
        );
    }

    public function getMailStandByCollegeWaitingForCorporate($userMember, $collegeMember){
        return array(
            'to' => $userMember->getEmail(),
            'cc' => array($collegeMember->getEmail()),
            'subject' =>'Rappel: '.$collegeMember->getFirstName().' '.$collegeMember->getLastName().
                ' attend la création de votre Compte Entreprise',
            'body' => array(
                'template' => 'AprCorporateBundle:Emails:notificationCollegeWaitingForCorporate.html.twig',
                'parameter' => array(
                    'member' => $userMember,
                    'collegeMember' => $collegeMember
                )),
        );
    }

    public function getMailNewCollegeWaitingForAdministrator($candidate){
        $corporate = $candidate->getCorporate();
        $administrator = $corporate->getAdministrator();
        $member = $administrator->getMember();

        return array(
            'to' => $member->getEmail(),
            'cc' => array($candidate->getMember()->getEmail()),
            'subject' => $candidate->getMember()->getFirstName().' '.$candidate->getMember()->getLastName().
                " souhaite créer le Collège Coopérons de ".$corporate->getRaisonSocial(),
            'body' => array(
                'template' => 'AprCorporateBundle:Emails:notificationNewCandidateAllColleges.html.twig',
                'parameter' => array(
                    'member' => $member,
                    'corporate' => $corporate,
                    'candidate' => $candidate,
                )),
        );
    }

    public function getMailWelcomeCollege($college)
    {
        $member = $college->getMember();
        $corporate = $college->getCorporate();

        return array(
            'to' => $member->getEmail(),
            'subject' => "Félicitations ! Vous faites désormais partie du Collège Coopérons de ".$corporate->getRaisonSocial(),
            'body' => array(
                'template' => 'AprCorporateBundle:Emails:welcomeCollege.html.twig',
                'parameter' => array(
                    'member' => $member,
                    'corporate' => $corporate,
                )),
        );
    }

    public function getMailConfirmationCollegeToDelegate($college){
        $corporate = $college->getCorporate();
        $delegate = $corporate->getDelegate();
        $member = $delegate->getMember();

        $ccArr = array($corporate->getAdministrator()->getMember()->getEmail());
        if(!$delegate->isAdministrator()) array_push($ccArr, $delegate->getMember()->getEmail());
        return array(
            'to' => $member->getEmail(),
            'cc' => $ccArr,
            'subject' => $college->getMember()->getFirstName().' '.$college->getMember()->getLastName().
                " vient de confirmer être toujours salarié de ".$corporate->getRaisonSocial(),
            'body' => array(
                'template' => 'AprCorporateBundle:Emails:confirmationCollegeToDelegate.html.twig',
                'parameter' => array(
                    'member' => $member,
                    'corporate' => $corporate,
                    'college' => $college,
                )),
        );
    }

    public function getMailNewCandidateAllColleges($collegeToSend, $collegeCandidate){
        $corporate = $collegeToSend->getCorporate();
        $member = $collegeToSend->getMember();
        return array(
            'to' => $member->getEmail(),
            'subject' => $collegeCandidate->getMember()->getFirstName().' '.$collegeCandidate->getMember()->getLastName().
                " souhaite rejoindre le Collège Coopérons de ".$corporate->getRaisonSocial(),
            'body' => array(
                'template' => 'AprCorporateBundle:Emails:notificationNewCandidateAllColleges.html.twig',
                'parameter' => array(
                    'member' => $member,
                    'corporate' => $corporate,
                    'candidate' => $collegeCandidate,
                    'college' => $collegeToSend,
                )),
        );
    }

    public function getMailLeaveCollege($college){
        $corporate = $college->getCorporate();
        $delegate = $corporate->getDelegate();
        $member = $delegate->getMember();

        $ccArr = array($corporate->getAdministrator()->getMember()->getEmail());
        if(!$delegate->isAdministrator()) array_push($ccArr, $delegate->getMember()->getEmail());

        return array(
            'to' => $member->getEmail(),
            'cc' => $ccArr,
            'subject' => $college->getMember()->getFirstName().' '.$college->getMember()->getLastName().
                " vient de quitter le collège Coopérons de ".$corporate->getRaisonSocial(),
            'body' => array(
                'template' => 'AprCorporateBundle:Emails:leaveCollegeToDelegate.html.twig',
                'parameter' => array(
                    'member' => $member,
                    'corporate' => $corporate,
                    'college' => $college,
                )),
        );
    }

    public function getMailAttestationAnnuelle($year, $member, $corporate, $yearlyAttestation){
        return  array(
            'to' => $member->getEmail(),
            'cc' => $corporate->getAdministrator()->getMember()->getEmail(),
            'subject' => "Attestation de ".$member->getLabel().", salarié de ".
                $corporate->getRaisonSocial()." pour l'année ".$year,
            'body' => array(
                'template' => 'AprCorporateBundle:Emails:attestationAnnuelle.html.twig',
                'parameter' => array(
                    'corporate'  => $corporate,
                    'member'    => $member,
                    'amount'     => $yearlyAttestation['amount'],
                    'cotisation'     => $yearlyAttestation['cotisation'],
                    'year'      => $year
                )
            ),
            'attach' => $this->container->getParameter('uploadsPath').'EmployerAttestations/'.$yearlyAttestation['fileName'].'.pdf',
        );
    }

    public function generatePDFAttestationAnnuelle($year, $member, $corporate, $yearlyAttestation)
    {
        $ref = $yearlyAttestation['fileName'];
        $html = $this->container->get('templating')->render('AprCorporateBundle:PDF:attestationAnnuelle.html.twig', array(
            'corporate' => $corporate,
            'member' => $member,
            'year' => $year,
            'dateNow' => Tools::DateTime(),
            'allAttestations'   => $yearlyAttestation['attestations'],
            'totalAmount'   => $yearlyAttestation['amount'],
            'totalCotisation'   => $yearlyAttestation['cotisation'],
            'smic'  => $yearlyAttestation['attestations'][0]->getSmic(),
            'baseurl' => $this->container->getParameter('baseUrl'),
        ));
        $uploadsPath = $this->container->getParameter('uploadsPath');
        $filePath = $uploadsPath . 'EmployerAttestations/'.$ref . '.pdf';

        $this->container->get('knp_snappy.pdf')->generateFromHtml($html, $filePath);

    }

    /**
     * Check if member can update college
     *
     * @param $member
     * @param $modifiedCollege
     * @param $memberOnly
     * @return bool
     * @throws ApiException
     */
    public function securityCheck($member, $modifiedCollege, $memberOnly)
    {
        $memberCollege = $member->getCollege();
        if ($modifiedCollege == null) {
            return $memberCollege == null;
        }

        // Member can update his college
        if ($modifiedCollege->getMember() == $member) {
            return true;
        }

        $corporate = $modifiedCollege->getCorporate();
        if ($corporate) {
            $isAdministrator =  $corporate->getAdministrator() && $corporate->getAdministrator()->getMember() == $member;
            $isDelegate = $corporate->getDelegate() && $corporate->getDelegate()->getMember() == $member;
            // Administrator and delegate can update any college in their corporate
            if ($isAdministrator || $isDelegate) {
                return true;
            } else if (!$memberOnly && ($memberCollege->getCorporate() == $modifiedCollege->getCorporate())) {
                return true;
            }
        }

        throw new ApiException(4037);
    }
}