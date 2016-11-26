<?php

namespace Apr\MandataireBundle\Manager;

use Doctrine\ORM\EntityManager;
use Apr\UserBundle\Manager\BaseManager as BaseManager;
use Apr\ContractBundle\Entity\Party;
use Apr\MandataireBundle\Entity\Record;
use Apr\MandataireBundle\Entity\Mandataire;
use Apr\MandataireBundle\Entity\Payment;


class RecordManager extends BaseManager
{
    private $arrIncomeLabels = array(
        '622' => "Rémunérations d'intermédiaires et honoraires",
        '646' => "Cotisations sociales personnelles de l'exploitant",
        '706' => "Prestations de services",
    );

    protected $em;
    protected $container;

    public function __construct(EntityManager $em, $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function getRepository() {
        return $this->em->getRepository('AprMandataireBundle:Record');
    }

    public function createRecord(Party $party, $amount, $firstRecord = null, $debitMandataire = null, $creditMandataire = null,
                                 $debitType = 'account', $creditType = 'account', $debitAccountRef = null, $creditAccountRef = null) {
        if($amount != 0) {
            if($amount > 0) {
                $record = new Record($party, $amount, $firstRecord, $debitMandataire, $creditMandataire, $debitType, $creditType, $debitAccountRef, $creditAccountRef);
            } else {
                $record = new Record($party, -$amount, $firstRecord, $creditMandataire , $debitMandataire, $creditType, $debitType, $debitAccountRef, $creditAccountRef);
            }
            $this->persist($record);
            return $record;
        }
    }

    public function recordPayment(Mandataire $debitMandataire, Payment $payment, $creditMandataire = null, $firstRecord = null) {
        $amount = $payment->getAmount();

        $party = $debitMandataire->getClient();
        $counterParty = $debitMandataire->getOwner();

        if(is_null($creditMandataire) && $payment->getQuarterlyTaxation()) {
            // Pour charges sociales, imputation sur compte 646 (et non 100) - à généraliser ultérieurement ...
            $clientRecord = $this->createRecord($party, -$amount, null, null, $debitMandataire, 'income', 'account', '646', null);
        } else {
            $clientRecord = $this->createRecord($party, $amount, $firstRecord, $debitMandataire, $creditMandataire);
        }

        $nextRecord = null;
        if($counterParty->getMandataire()) {
            $nextRecord = $this->recordPayment($counterParty->getMandataire(), $payment, $debitMandataire, $clientRecord->getFirstRecord());
        } else {
            // Party.label = bank ...
            $nextRecord = $this->createRecord($counterParty, $amount, $clientRecord->getFirstRecord(), null, $debitMandataire);
        }

        if(!is_null($nextRecord)) {
            $clientRecord->setNextRecord($nextRecord);
        }

        if(is_null($creditMandataire)) {
            $payment->setRecord($clientRecord);
            $this->persist($payment);
            $this->persist($clientRecord);
        }

        /*
         * flush géré par MandataireManager
         * $this->flush();
         */

        return $clientRecord;
    }

    public function recordSettlements(Mandataire $mandataire, $allSettlements) {
        $amount = 0;
        foreach($allSettlements as $settlement) {
            $amount += $settlement->getAmount();
        }

        $clientExpenseRecord = $this->createRecord($mandataire->getClient(), $amount, null, $mandataire, $mandataire, 'income', 'account');
        $ownerIncomeRecord = $this->createRecord($mandataire->getOwner(), $amount, $clientExpenseRecord, $mandataire, $mandataire, 'account', 'income');
        $clientExpenseRecord->setNextRecord($ownerIncomeRecord);

        $this->persist($clientExpenseRecord);

        foreach($allSettlements as $settlement) {
            $settlement->setRecord($clientExpenseRecord);
            $this->persist($settlement);
        }

        /*
         * flush géré par MandataireManager
         * $this->flush();
         */
    }

    public function recordTransfer(Mandataire $debitMandataire, Mandataire $creditMandataire, $amount = 0) {
        if($amount < 0) {
            $switchMandataire = $creditMandataire;
            $creditMandataire = $debitMandataire;
            $debitMandataire = $switchMandataire;
            $amount = -$amount;
        }

        if($debitMandataire->getFreeDepot() >= $amount && $amount != 0) {
            // debitMandataire et creditMandataire ont même client ...
            $pivotParty = $debitMandataire->getClient();
            $debitedParty = $debitMandataire->getOwner();
            $creditedParty = $creditMandataire->getOwner();

            if($pivotParty->getMandataire() == $debitMandataire) {
                $creditedPartyCooperonsMandataire = $creditedParty->getMandataire();

                // debitedParty = Cooperons: 517-P_Id | 517-C_Id
                $debitedPartyRecord = $this->createRecord($debitedParty, $amount, null, $debitMandataire, $creditedPartyCooperonsMandataire);

                // creditedParty: 517 | 409-P_Id
                $creditedPartyRecord = $this->createRecord($creditedParty, $amount, $debitedPartyRecord, $creditedPartyCooperonsMandataire, $creditMandataire);
                $debitedPartyRecord->setNextRecord($creditedPartyRecord);

                // party: 419-C_Id | 517
                $pivotPartyRecord = $this->createRecord($pivotParty, $amount, $debitedPartyRecord, $creditMandataire, $debitMandataire);
                $creditedPartyRecord->setNextRecord($pivotPartyRecord);

            } elseif($pivotParty->getMandataire() == $creditMandataire) {
                $debitedPartyCooperonsMandataire = $debitedParty->getMandataire();

                // debitedParty : 409-P_Id | 517
                $debitedPartyRecord = $this->createRecord($debitedParty, $amount, null, $debitMandataire, $debitedPartyCooperonsMandataire);

                // creditedParty= Cooperons: 517-D_Id | 517-P_Id
                $creditedPartyRecord = $this->createRecord($creditedParty, $amount, $debitedPartyRecord, $debitedPartyCooperonsMandataire, $creditMandataire);
                $debitedPartyRecord->setNextRecord($creditedPartyRecord);

                // party: 517 | 419-D_Id
                $pivotPartyRecord = $this->createRecord($pivotParty, $amount, $debitedPartyRecord, $creditMandataire, $debitMandataire);
                $creditedPartyRecord->setNextRecord($pivotPartyRecord);

            } else {
                $creditedPartyCooperonsMandataire = $creditedParty->getMandataire();
                $debitedPartyCooperonsMandataire = $debitedParty->getMandataire();
                $cooperonsParty = $debitedPartyCooperonsMandataire->getOwner();

                // debitedParty : 409-P_Id | 517
                $debitedPartyRecord = $this->createRecord($debitedParty, $amount, null, $debitMandataire, $debitedPartyCooperonsMandataire);

                // creditedParty: 517 | 409-P_Id
                $creditedPartyRecord = $this->createRecord($creditedParty, $amount, $debitedPartyRecord, $creditedPartyCooperonsMandataire, $creditMandataire);
                $debitedPartyRecord->setNextRecord($creditedPartyRecord);

                // party: 419-C_Id | 419-D_Id
                $pivotPartyRecord = $this->createRecord($pivotParty, $amount, $debitedPartyRecord, $creditMandataire, $debitMandataire);
                $creditedPartyRecord->setNextRecord($pivotPartyRecord);

                // Cooperons: 517-D_Id | 517-C_Id
                $cooperonsRecord = $this->createRecord($cooperonsParty, $amount, $debitedPartyRecord, $debitedPartyCooperonsMandataire, $creditedPartyCooperonsMandataire);
                $pivotPartyRecord->setNextRecord($cooperonsRecord);
            }

            $this->persist($debitedPartyRecord);
            return $debitedPartyRecord;
        }
        /*
         * flush géré par MandataireManager
         * $this->flush();
         */
    }

    public function loadMandataireRecords(Party $party, Mandataire $mandataire, $forInvoice = false, $cutOffDate = null) {
        return $this->getRepository()->getAllRecords($party, $mandataire, $forInvoice, $cutOffDate);
    }

    public function loadPartyRecords(Party $party) {
        return $this->getRepository()->getAllRecords($party);
    }

    public function buildMandataireOperations(Party $party, Mandataire $mandataire, $allRecords)
    {
        $result = array();
        foreach($allRecords as $record) {
            $operations = $record->buildArray($party, $mandataire)['operations'];
            foreach($operations as $operation) {
                $result[] = $operation;
            }
        }
        return $result;
    }

    private function cleanBalance($balance) {
        return array(
            'debit' => max(0,$balance['debit']-$balance['credit']),
            'credit' => max(0,$balance['credit']-$balance['debit'])
        );
    }

    public function buildRecords(Party $party) {
        $allRecords = $this->loadPartyRecords($party);

        $result = array('accounts' => array(), 'records' => array());
        foreach($allRecords as $record) {
            $buildArray = $record->buildArray($party);
            $result['records'][] = $buildArray;
            foreach(array('debit', 'credit') as $strBalance) {
                $ref = $buildArray[$strBalance.'AccountFullRef'];
                if(!isset($result['accounts'][$ref])) {
                    $result['accounts'][$ref] = array(
                        'mandataireId' => $buildArray[$strBalance.'MandataireId'],
                        'debit' => 0,
                        'credit' => 0,
                    );
                    if(isset($this->arrIncomeLabels[$buildArray[$strBalance.'AccountRef']])) {
                        $result['accounts'][$ref]['label'] = $this->arrIncomeLabels[$buildArray[$strBalance.'AccountRef']];
                        if(!is_null($buildArray[$strBalance.'CounterPartyLabel'])) $result['accounts'][$ref]['label'] .=
                            " (".$buildArray[$strBalance.'CounterPartyLabel'].")";
                    } else {
                        $result['accounts'][$ref]['label'] = $buildArray[$strBalance.'MandataireLabel'];
                    }
                }
                $result['accounts'][$ref][$strBalance] += $buildArray['amount'];
            }
        }

        foreach($result['accounts'] as $key => $account) {
            $balance = $this->cleanBalance($account);
            $result['accounts'][$key]['debit'] = $balance['debit'];
            $result['accounts'][$key]['credit'] = $balance['credit'];
        }

        ksort($result['accounts']);

        return $result;
    }

}