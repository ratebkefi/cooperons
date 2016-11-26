<?php

namespace Apr\ProgramBundle\Manager;

use Apr\ProgramBundle\Entity\Commission;
use Apr\ProgramBundle\Entity\PreProdCommission;
use Apr\UserBundle\Manager\BaseManager as BaseManager;
use Doctrine\ORM\EntityManager;

class AffairManager extends BaseManager
{
    
    protected  $em;
    protected $container;
   
    public function __construct(EntityManager $em , $container)
    {
        $this->em = $em;
	$this->container = $container;
       
    }
    public function getRepository()
    {
        // Nécessaire pour implémenter BaseManager - non utilisé
    }

    public function getProdOrPreProdRepository($status)
    {
        $programBundle = 'AprProgramBundle:';
        $entity = 'Affair';
        $prefix = ($status == 'preprod')?'PreProd':'';

        return $this->em->getRepository($programBundle.$prefix.$entity);
    }

    public function loadAffairById($program, $id){
        return $this->getProdOrPreProdRepository($program->getStatus())->findOneBy(array('program' => $program, 'id' => $id));
    }

    public function loadAffairByParticipatesTo($program, $participatesTo){
        $params = array('program' => $program);
        if(!is_null($participatesTo)) $params['participatesTo'] = $participatesTo;
        return $this->getProdOrPreProdRepository($program->getStatus())->findBy($params);
    }

    public function cancelAffair($affair, $cancelMsg, &$listeEmails) {
        $affair->cancel($cancelMsg);
        $this->persistAndFlush($affair);
        array_push($listeEmails, $this->getMailAffair($affair));
    }

    // Easy

    public function calculateCommissionPoints($affair, $amount)
    {
        $coopPlusManager = $this->container->get('apr_admin.coop_plus_manager');

        $program = $affair->getProgram();
        if($amount) {
            $member = $affair->getParticipatesTo()->getMember();
            $pointsSimple = $member->convertToPoints($amount * $affair->getSimpleRate()/100);
            $pointsMulti = $member->convertToPoints($amount * $affair->getMultiRate()/100);

            return array(
                'simple' => array_merge($coopPlusManager->calculatePoints($program, $pointsSimple), array('points' => $pointsSimple)),
                'multi' => array_merge($coopPlusManager->calculatePoints($program, $pointsMulti), array('points' => $pointsMulti))
            );
        } else {
            return null;
        }
    }

    public function processAffair($affair, $amount, &$listeEmails) {
        $commission = null;
        if(!is_null($amount)) {
            if($affair->getStatus() == 'approach') {
                $affair->setAmount($amount);
            } elseif($affair->getStatus() == 'negotiation') {
                $affair->setAmount($amount);
                $affair->close();
            } elseif($affair->getStatus() == 'payable') {
                if($amount <= $affair->getRemains()) {
                    $commission = $affair->addCommission($amount);
                    $program = $affair->getProgram();
                    $commissionPoints = $this->calculateCommissionPoints($affair, $commission->getBase());
                    $labelOperation = $program->getEasySetting()->getLabelOperation();
                    $accountManager = $this->container->get('apr_program.account_points_history_manager');
                    $datas = array(
                        array('labelOperation' => $labelOperation['simple'], 'amount' => $commissionPoints['simple']['points'], 'info' => $affair->getLabel()),
                        array('labelOperation' => $labelOperation['multi'], 'amount' => $commissionPoints['multi']['points'], 'info' => $affair->getLabel()),
                    );

                    $params = $accountManager->addPoints($affair->getParticipatesTo(), $datas, $listeEmails);
                    // ToDo: revoir gestion en cas de non paiement du Settlement Point ?
                }
            }
        }

        $this->persistAndFlush($affair);

        array_push($listeEmails, $this->getMailAffair($affair, $commission));
    }

    public function buildEasyUpline($affair) {
        $coopPlusManager = $this->container->get('apr_admin.coop_plus_manager');
        $participatesToManager = $this->container->get('apr_program.participates_to_manager');

        $program = $affair->getProgram();
        $sponsor = $participatesToManager->loadParticipatesToByMember($program, $affair->getParticipatesTo()->getMember());

        if(!is_null($affair->getAmount())) {
            $allCommissions = $affair->getAllCommissions();
            if(!count($allCommissions)) {
                // Commissions prévisionnelles ...
                $allCommissions = array($affair);
            }

            foreach($allCommissions as $commission) {
                if($commission instanceof Commission or $commission instanceof PreProdCommission ) {
                    $amount = $commission->getBase();
                } else {
                    // Instance of Affair ... pour
                    $amount = $commission->getAmount();
                }
                $calcComm = $this->calculateCommissionPoints($affair, $amount);

                $my_upline = $participatesToManager->buildUpline($sponsor, $calcComm['multi']['points']);
                $my_upline[0] = array('participatesTo' => $sponsor, 'points' => $calcComm['simple']['points'], 'multiPoints' => $calcComm['multi']['points']);
                if(!isset($upline)) {
                    $upline = $my_upline;
                } else {
                    foreach($my_upline as $key => $value) {
                        // ToDo: buildUpline devrait renvoyer la clé multiPoints et non points ...
                        // Actuellement, au rang 0 les points sont des Points, mais au rang > 0 les points ont des multiPoints ...
                        if(isset($upline[$key]['points'])) $upline[$key]['points'] += $my_upline[$key]['points'];
                        if(isset($upline[$key]['multiPoints'])) $upline[$key]['multiPoints'] += $my_upline[$key]['multiPoints'];
                    }
                }
            }

            foreach($upline as $key => $my) {
                $points = $my['points'];
                if(isset($my['multiPoints'])) $points += $my['multiPoints'];

                $upline[$key]['calc'] = $coopPlusManager->calculatePoints($program, $points);
            }
            ksort($upline);
        } else {
            $upline = array();
        }

        return $upline;

    }

    public function getMailAffair($affair, $commission = null)
    {
        $labelAffair = $affair->getLabel();
        $program = $affair->getProgram();

        $maxCommissionPoints = $this->calculateCommissionPoints($affair, $affair->getAmount());
        $maxValuePoints = (!is_null($maxCommissionPoints))?$affair->getParticipatesTo()->getMember()->getValuePoints(
            $maxCommissionPoints['simple']['points']+$maxCommissionPoints['multi']['points']):0;

        $commissionPoints = $valuePoints = null;
        if(!is_null($commission)) {
            $commissionPoints = $this->calculateCommissionPoints($affair, $commission->getBase());
            $totalPoints = $commissionPoints['simple']['points']+$commissionPoints['multi']['points'];
            $valuePoints = (!is_null($commissionPoints))?$affair->getParticipatesTo()->getMember()->getValuePoints($totalPoints):0;
            $subject = "Affaire ".$labelAffair.": vous avez gagné ".$totalPoints." Points !";
        } else {
            $arraySubject = array(
                'cancel' => "Abandon de l'affaire ".$labelAffair,
                'approach' => "Une nouvelle affaire vous est attribuée: ".$labelAffair,
                'negotiation' => "L'affaire ".$labelAffair." se présente bien !",
                'payable' => "Félicitations ! L'affaire ".$labelAffair." a été conclue !",
            );
            $subject = $arraySubject[$affair->getStatus()];
        }

        $collaboratorMember = $program->getCollaborator()->getMember();

        $ccArr = array($program->getSenderEmail(), $collaboratorMember->getEmail());

        return array(
            'to' => $affair->getParticipatesTo()->getMember()->getEmail(),
            'cc' => $ccArr,
            'subject' => (($program->getStatus() == 'preprod')?'[PRE-PRODUCTION] ':'').$subject,
            'body' => array(
                'template' => 'AprProgramBundle:Emails:notificationAffair.html.twig',
                'parameter' => array(
                    'affair' => $affair,
                    'program' => $program,
                    'maxCommissionPoints' => $maxCommissionPoints,
                    'maxValuePoints' => $maxValuePoints,
                    'commission' => $commission,
                    'commissionPoints' => $commissionPoints,
                    'valuePoints' => $valuePoints
                )),
        );
    }

}
