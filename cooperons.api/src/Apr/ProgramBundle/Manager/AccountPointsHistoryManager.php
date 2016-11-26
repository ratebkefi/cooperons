<?php

namespace Apr\ProgramBundle\Manager;

use Doctrine\ORM\EntityManager;
use Apr\UserBundle\Manager\BaseManager as BaseManager;
use Apr\ProgramBundle\Entity\AccountPointsHistory;
use Apr\ProgramBundle\Entity\PreProdAccountPointsHistory;

class AccountPointsHistoryManager extends BaseManager
{
    protected $em;
    protected $container;

    public function __construct(EntityManager $em, $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function getRepository() {
        return $this->getProdOrPreProdRepository('prod');
    }

    public function getProdOrPreProdRepository($status)
    {
        $programBundle = 'AprProgramBundle:';
        $entity = 'AccountPointsHistory';
        $prefix = ($status == 'preprod')?'PreProd':'';

        return $this->em->getRepository($programBundle.$prefix.$entity);
    }

    public function loadPointsByMember($member)
    {
        return array_merge($this->getProdOrPreProdRepository('preprod')->getAccountPointsHistory($member),
            $this->getProdOrPreProdRepository('prod')->getAccountPointsHistory($member));
    }

    public function loadPointsBySettlement($settlement)
    {
        $program = $settlement->getContract()->getProgram();
        // Récupération des HistoryPoints par ordre décroissant d'Id pour pouvoir les supprimer sans FK ...
        return $this->getProdOrPreProdRepository($program->getStatus())->findBy(array('settlement' => $settlement), array('id' => 'DESC'));
    }

    public function loadMultiPointsBySponsor($sponsor)
    {
        $program = $sponsor->getProgram();
        return $this->getProdOrPreProdRepository($program->getStatus())->getAccountPointsHistory($sponsor->getMember(), $program, true);
    }

    public function loadPointsByProgram($program)
    {
        $status = $program->getStatus();

        $allPoints =  $this->getProdOrPreProdRepository($status)->getAccountPointsHistory(null, $program);
        $result = array();
        $settlementPoints = array();

        $oldSettlement = null;
        foreach($allPoints as $my) {
            $newSettlement = $my->getSettlement();
            if($newSettlement != $oldSettlement or $status == 'preprod') {
                $oldSettlement = $newSettlement;
                if(count($settlementPoints)>0) array_push($result, $settlementPoints);
                $settlementPoints = array('settlement' => $newSettlement, 'historyPoints' => array());
            }
            array_push($settlementPoints['historyPoints'], $my);
        }
        if(count($settlementPoints)>0) array_push($result, $settlementPoints);

        return $result;
    }

    public function buildDescriptionSettlement($header, $member, $arrayPoints, $isMulti) {
        $points = $arrayPoints[0]['points'];
        $totalPoints = 0;
        foreach($arrayPoints as $my) $totalPoints += $my['points'];
        $description = $totalPoints." ".($isMulti?"MultiPoints":"Points")." suite à l'opération ".$header.
            " ayant attribué ".$points." ".($isMulti?"MultiPoints":"Points")." à ".$member->getLabel();
        if(count($arrayPoints) > 1)
            $description .= " (+ ".(count($arrayPoints)-1)." parrain".((count($arrayPoints) > 2)?"s ":" ").")";
        return $description;
    }

    public function buildDescriptionPoints($header, $member, $points, $isMulti) {
        return $header.' : '.$points.($isMulti?" MultiPoints ":" Points ").$member->getLabel();
    }

    public function buildDescriptionPointsFilleul($affiliateMemberInfo, $points) {
        return $points ." MultiPoints Filleul (". $affiliateMemberInfo.")";
    }

    public function addPoints($participatesTo, $datas, &$listeEmails) {
        $programManager = $this->container->get('apr_program.program_manager');
        $cooperonsManager = $this->container->get('apr_admin.cooperons_manager');
        $coopPlusManager = $this->container->get('apr_admin.coop_plus_manager');
        $participatesToManager = $this->container->get('apr_program.participates_to_manager');
        $operationCreditManager = $this->container->get('apr_program.operation_credit_manager');

        if(!$participatesTo) return array('error' => 4008);

        $program = $participatesTo->getProgram();

        $bypassMandataireActions = $programManager->bypassMandataireActions($program);
        $historyPoints = null;

        if(!$bypassMandataireActions) {
            $settlementsManager = $this->container->get('apr_mandataire.settlements_manager');
            $mandataire = $program->getMandataire();
        }

        foreach($datas as $data){
            $labelOperation = isset($data['labelOperation'])?$data['labelOperation']:null;
            $amount = isset($data['amount'])?$data['amount']:0;
            $info = isset($data['info'])?$data['info']:'';

            if($amount > 0){
                if(!$labelOperation) return array('error' => 40014);

                if($labelOperation == "__multi") {
                    // Label réservé au cas de post-parrainage ...
                    $descHeader = "Post-parrainage Filleul";
                    $isMulti = true;
                } else {
                    $operation = $operationCreditManager->getOperationByLabel($program, $labelOperation);

                    if(!$operation) return array('error' => 4007);

                    if(!$amount){
                        $amount = $operation->getDefaultAmount();
                    }

                    if(!$amount) return array('error' => 40017, 'operation_err' => $labelOperation);
                    $descHeader = $labelOperation;

                    $isMulti = false;
                    if($operation->isMulti()){
                        $isMulti = true;
                    }
                }

                $points = $amount;

                if($info != ''){
                    $descHeader .= " (".$info.")";
                }

                $arrayPoints = $isMulti?$participatesToManager->buildUpline($participatesTo, $points):array();

                // Rajout du self ...
                $arrayPoints[0] = array('participatesTo' => $participatesTo, 'points' => $points);
                ksort($arrayPoints);

                $totalPoints = 0;
                foreach($arrayPoints as $my) $totalPoints += $my['points'];
                $calculation = $coopPlusManager->calculatePoints($program, $totalPoints);

                $settlement = null;
                // Création du Settlement points
                if(!$bypassMandataireActions) {
                    $description = $this->buildDescriptionSettlement($descHeader, $participatesTo->getMember(), $arrayPoints, $isMulti);
                    $settlement = $settlementsManager->createSettlement($mandataire, $description, $calculation, 'points');
                    $this->persist($settlement);
                }

                $oldHistoryPoints = null;
                $historyPoints = null;
                foreach($arrayPoints as $key => $my) {
                    if($my['points']) {
                        if($key or $labelOperation == "__multi") {
                            $type = "__multi";
                            if($key) {
                                $filleulInfo = $oldHistoryPoints->getParticipatesTo()->getMember()->getFirstName()." ".$oldHistoryPoints->getParticipatesTo()->getMember()->getLastName();
                            } else {
                                $filleulInfo = $info;
                            }
                            $description = $this->buildDescriptionPointsFilleul($filleulInfo, $my['points']);
                        } else {
                            $type = $labelOperation;
                            $description = $this->buildDescriptionPoints($descHeader, $my['participatesTo']->getMember(), $my['points'], $isMulti);
                        }

                        $historyPoints = ($program->getStatus() == 'preprod')?new PreProdAccountPointsHistory($my['participatesTo'], $my['points'], $isMulti, $description, $type):
                            new AccountPointsHistory($my['participatesTo'], $my['points'], $isMulti, $description, $type);

                        if($key) {
                            $historyPoints->setAffiliateHistory($oldHistoryPoints);
                        }

                        if(!$bypassMandataireActions) {
                            $historyPoints->setSettlement($settlement);
                        } else {
                            $this->confirmPoints($historyPoints, $listeEmails);
                        }
                        $this->persist($historyPoints);
                        $oldHistoryPoints = $historyPoints;
                    }
                }
            }
        }

        $this->flush();

        // On vérifie qu'au moins un historyPoints a été créé -> nous sert pour récupérer le status ...
        if(!$bypassMandataireActions & !is_null($historyPoints)) {
            $settlementsManager->updateWaitingSettlements($mandataire, $listeEmails);
            if($historyPoints->getStatus() != 'confirmed') {
                $totalPoints = 0;
            }
        }

        return array('participatesTo' => $participatesTo, 'amount' => $totalPoints);
    }

    public function confirmPointsBySettlement($settlement, &$listeEmails = null) {
        $allHistoryPoints = $this->loadPointsBySettlement($settlement);
        foreach($allHistoryPoints as $historyPoints) {
            $this->confirmPoints($historyPoints, $listeEmails);
        }
    }

    public function confirmPoints($historyPoints, &$listeEmails = null) {
        $historyPoints->confirm();
        $this->persistAndFlush($historyPoints);
        array_push($listeEmails, $this->getMailConfirmationPoints($historyPoints, $listeEmails));
    }

    public function cancelPointsBySettlement($settlement, &$listeEmails = null) {
        $allHistoryPoints = $this->loadPointsBySettlement($settlement);
        foreach($allHistoryPoints as $historyPoints) {
            $this->removeAndFlush($historyPoints);
        }
    }

    public function getMailConfirmationPoints($historyPoints){

        $participatesTo = $historyPoints->getParticipatesTo();
        $member = $participatesTo->getMember();
        $program = $participatesTo->getProgram();

        return array(
            'to' => $member->getEmail(),
            'subject' => (($program->getStatus() == 'preprod')?'[PRE-PRODUCTION] ':'').'Vous avez gagné ' . $historyPoints->getPoints() . ($historyPoints->isMulti()?' MultiPoints':' Points'),
            'body' => array(
                'template' => 'AprProgramBundle:Emails:confirmationPoints.html.twig',
                'parameter' => array(
                    'program'           => $program,
                    'historyPoints'     => $historyPoints,
                    'participatesTo'    => $participatesTo,
                    'member'            => $member
                )
            )
        );
    }
}
?>
