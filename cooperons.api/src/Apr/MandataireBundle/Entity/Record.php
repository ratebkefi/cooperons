<?php

namespace Apr\MandataireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Apr\CoreBundle\Tools\Tools;

/**
 * @ORM\Entity(repositoryClass="Apr\MandataireBundle\Repository\RecordRepository")
 * @ORM\Table(name="records")
 */
class Record
{

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var \Apr\ContractBundle\Entity\Party
     *
     * @ORM\ManyToOne(targetEntity="\Apr\ContractBundle\Entity\Party", inversedBy="allRecords", cascade = {"persist"})
     * @ORM\JoinColumn(name="party_id", referencedColumnName="id")
     * 
     */
    private $party;

    /**
     * @var Mandataire
     *
     * @ORM\ManyToOne(targetEntity="Mandataire", inversedBy="allRecordsAsDebit", cascade = {"persist"})
     * @ORM\JoinColumn(name="debit_mandataire_id", referencedColumnName="id", nullable=true)
     *
     */
    private $debitMandataire;

    /**
     * @var Mandataire
     *
     * @ORM\ManyToOne(targetEntity="Mandataire", inversedBy="allRecordsAsCredit", cascade = {"persist"})
     * @ORM\JoinColumn(name="credit_mandataire_id", referencedColumnName="id", nullable=true)
     *
     */
    private $creditMandataire;

    /**
     * @ORM\Column(name="debit_account_ref" , type="string", length=5)
     */
    private $debitAccountRef;

    /**
     * @ORM\Column(name="credit_account_ref" , type="string", length=5)
     */
    private $creditAccountRef;

    /**
     * @ORM\Column(name="debit_account_id" , type="integer", nullable=true)
     */
    private $debitAccountId;

    /**
     * @ORM\Column(name="credit_account_id" , type="integer", nullable=true)
     */
    private $creditAccountId;

    /**
     * @ORM\Column(name="amount", type="float")
     */
    private $amount = 0;

    /**
     * @ORM\Column(name="created_date", type="datetime")
     * @var DateTime
     */
    private $createdDate;

    /**
     * @ORM\OneToMany(targetEntity="Payment", mappedBy="record", cascade = {"persist"})
     */
    private $allPayments;

    /**
     * @ORM\OneToMany(targetEntity="Settlement", mappedBy="record", cascade = {"persist"})
     */
    private $allSettlements;

    /**
     * @ORM\OneToOne(targetEntity="Transfer", mappedBy="record", cascade = {"persist"})
     */
    private $transfer;

    /**
     * @var Record
     *
     * @ORM\ManyToOne(targetEntity="Record", inversedBy="allNextRecords", cascade = {"persist"})
     * @ORM\JoinColumn(name="first_record_id", referencedColumnName="id")
     *
     */
    private $firstRecord;

    /**
     * @var Record
     *
     * @ORM\OneToMany(targetEntity="Record", mappedBy="firstRecord", cascade = {"persist"})
     *
     */
    private $allNextRecords;

    /**
     * @var Record
     *
     * @ORM\OneToOne(targetEntity="Record", inversedBy="previousRecord", cascade = {"persist"})
     * @ORM\JoinColumn(name="next_record_id", referencedColumnName="id", nullable=true)
     *
     */
    private $nextRecord;

    /**
     * @var Record
     *
     * @ORM\OneToOne(targetEntity="Record", mappedBy="nextRecord", cascade = {"persist"})
     *
     */
    private $previousRecord;

    /**
     * @var Invoice $invoice
     * @ORM\ManyToOne(targetEntity="Invoice")
     * @ORM\JoinColumn(name="invoice_id", referencedColumnName="id")
     */
    private $invoice ;

    public function __construct(\Apr\ContractBundle\Entity\Party $party, $amount, $firstRecord = null, $debitMandataire = null, $creditMandataire = null,
                                $debitType = 'account', $creditType = 'account', $debitAccountRef = null, $creditAccountRef = null) {
        $this->createdDate = Tools::DateTime();
        $this->party = $party;
        $this->amount = $amount;
        $this->debitMandataire = $debitMandataire;
        $this->creditMandataire = $creditMandataire;

        $this->firstRecord = is_null($firstRecord)?$this:$firstRecord;

        if(!is_null($debitMandataire)) {
            // ATTENTION: Risque de Bug si getSide(party) = null => party DOIT être client ou owner de debitMandataire
            $debitSide = $debitMandataire->getSide($party);
            $accountRefMethodName = 'get'.ucfirst($debitSide).ucfirst($debitType).'Ref';
            $accountIdMethodName = 'get'.ucfirst($debitSide).'AccountId';

            if(is_null($debitAccountRef)) {
                $this->debitAccountRef = $debitMandataire->$accountRefMethodName();
                $this->debitAccountId = $debitMandataire->$accountIdMethodName();
            } else {
                $this->debitAccountRef = $debitAccountRef;
            }

            if($debitType == 'account') {
                if($debitSide == 'owner') {
                    $this->debitMandataire->addDepotAmount(-$amount);
                } else {
                    $this->debitMandataire->addDepotAmount($amount);
                }
            }
        } else {
            $debitType = 'income';
            if(is_null($debitAccountRef)) {
                $this->debitAccountRef = '100';
                $this->debitAccountId = 1;
            } else {
                $this->debitAccountRef = $debitAccountRef;
            }
        }

        if(!is_null($creditMandataire)) {
            // ATTENTION: Risque de Bug si getSide(party) = null => party DOIT être client ou owner de creditMandataire
            $creditSide = $creditMandataire->getSide($party);
            $accountRefMethodName = 'get' . ucfirst($creditSide) . ucfirst($creditType) . 'Ref';
            $accountIdMethodName = 'get' . ucfirst($creditSide) . 'AccountId';

            if(is_null($creditAccountRef)) {
                $this->creditAccountRef = $creditMandataire->$accountRefMethodName();
                $this->creditAccountId = $creditMandataire->$accountIdMethodName();
            } else {
                $this->creditAccountRef = $creditAccountRef;
            }

        } else {
            $creditType = 'income';
            if(is_null($creditAccountRef)) {
                $this->creditAccountRef = '100';
                $this->creditAccountId = 1;
            } else {
                $this->creditAccountRef = $creditAccountRef;
            }
        }

        if($debitType == 'income') {
            $this->party->addBalance(-$amount);
        }

        if($creditType == 'income') {
            $this->party->addBalance($amount);
        }
    }

    public function getId(){
        return $this->id;
    }

    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    public function getParty()
    {
        return $this->party;
    }

    public function getDebitMandataire()
    {
        return $this->debitMandataire;
    }

    public function getCreditMandataire()
    {
        return $this->creditMandataire;
    }

    public function getSide(Mandataire $mandataire) {
        if($this->debitMandataire == $mandataire) {
            return 'debit';
        } elseif($this->creditMandataire == $mandataire) {
            return 'credit';
        } else {
            return null;
        }
    }

    public function getCounterMandataire(Mandataire $mandataire) {
        if($this->debitMandataire == $mandataire) {
            return $this->creditMandataire;
        } elseif($this->creditMandataire == $mandataire) {
            return $this->debitMandataire;
        } else {
            return null;
        }
    }

    public function getDebitAccountRef()
    {
        return $this->debitAccountRef;
    }

    public function getCreditAccountRef()
    {
        return $this->creditAccountRef;
    }

    public function getDebitAccountId()
    {
        return $this->debitAccountId;
    }

    public function getCreditAccountId()
    {
        return $this->creditAccountId;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getAllPayments()
    {
        return $this->allPayments;
    }

    public function getAllSettlements()
    {
        return $this->allSettlements;
    }

    public function getTransfer()
    {
        return $this->transfer;
    }

    public function setNextRecord(Record $nextRecord)
    {
        $this->nextRecord = $nextRecord;
    }

    public function getFirstRecord()
    {
        return $this->firstRecord;
    }

    public function getAllNextRecords()
    {
        return $this->allNextRecords;
    }

    public function getNextRecord()
    {
        return $this->nextRecord;
    }

    public function getPreviousRecord()
    {
        return $this->previousRecord;
    }

    public function buildArray($party = null, $mandataire = null) {

        $result = array(
            'createdDate' => date_format($this->createdDate,"d/m/Y"),
            'amount' => $this->amount,
            'debitAccountRef' => $this->debitAccountRef,
            'creditAccountRef' => $this->creditAccountRef,
            'operations' => array()
        );

        if(!is_null($this->debitMandataire)) {
            $result['debitAccountFullRef'] = $this->debitAccountRef.($this->debitAccountId?sprintf("%'.04d\n", $this->debitAccountId):null);
            $result['debitMandataireId'] = $this->debitMandataire->getId();
            $result['debitCounterPartyLabel'] = $this->debitMandataire->getCounterParty($party)->getLabel();
            $result['debitMandataireLabel'] = $this->debitMandataire->getShortLabel().' '.$result['debitCounterPartyLabel'];
        } else {
            $result['debitAccountFullRef'] = $this->debitAccountRef;
            $result['debitMandataireId'] = null;
            $result['debitCounterPartyLabel'] = null;
            $result['debitMandataireLabel'] = 'Report';
        }

        if(!is_null($this->creditMandataire)) {
            $result['creditAccountFullRef'] = $this->creditAccountRef.($this->creditAccountId?sprintf("%'.04d\n", $this->creditAccountId):null);
            $result['creditMandataireId'] = $this->creditMandataire->getId();
            $result['creditCounterPartyLabel'] = $this->creditMandataire->getCounterParty($party)->getLabel();
            $result['creditMandataireLabel'] = $this->creditMandataire->getShortLabel().' '.$result['creditCounterPartyLabel'];
        } else {
            $result['creditAccountFullRef'] = $this->creditAccountRef;
            $result['creditMandataireId'] = null;
            $result['creditCounterPartyLabel'] = null;
            $result['creditMandataireLabel'] = 'Report';
        }

        $firstRecord = $this->getFirstRecord();

        $allSettlements = $firstRecord->getAllSettlements();
        $allPayments = $firstRecord->getAllPayments();
        $transfer = $firstRecord->getTransfer();

        $counterMandataire = $side = null;
        if(!is_null($mandataire)) {
            $counterMandataire = $this->getCounterMandataire($mandataire);
            $side = $mandataire->getSide($party);
        }
        $nbSettlements = count($allSettlements);
        if($nbSettlements) {
            foreach($allSettlements as $settlement) {
                $operation = array(
                    'createdDate' => date_format($settlement->getCreatedDate(),"d/m/Y"),
                    'type' => $settlement->getType(),
                    'description' => ucfirst($settlement->getDescription()),
                    'amount' => -$settlement->getAmount()
                );
                if($this->invoice) {
                    $operation['invoiceId'] = $this->invoice->getId();
                    $operation['nbSettlements'] = $nbSettlements;
                }
                $result['operations'][]= $operation;
            }
        } elseif(count($allPayments)) {
            foreach($allPayments as $payment) {
                $operation = array(
                    'createdDate' => date_format($payment->getCreatedDate(),"d/m/Y"),
                    'type' => 'payment',
                    'amount' => $payment->getAmount(),
                );
                if($payment->getQuarterlyTaxation()) {
                    $operation['description'] = 'Cotisations sociales trimestrielles';
                } else {
                    $operation['description'] = 'Paiement par '.$payment->getMode();
                }
                if(!is_null($counterMandataire)) {
                    $methodName = 'get'.ucfirst($side);
                    $operation['description'] .= ' ('.$counterMandataire->getShortLabel();
                    if(!is_null($counterMandataire->getContract())) $operation['description'] .= ' '.$counterMandataire->$methodName()->getLabel();
                    $operation['description'] .= ')';
                }
                $result['operations'][]= $operation;
            }
        } elseif(!is_null($transfer)) {
            $operation = array(
                'createdDate' => date_format($transfer->getCreatedDate(),"d/m/Y"),
                'type' => 'transfer',
            );
            if(is_null($mandataire)) {
                $operation['description'] = 'Transfert' ;
                $operation['amount'] = $transfer->getAmount() ;
            } else {
                $checkSide = $this->getSide($mandataire).':'.$side;
                if ($checkSide == 'debit:client' or $checkSide == 'credit:owner') {
                    $operation['description'] = 'Transfert';
                    $operation['amount'] = $transfer->getAmount();
                } else {
                    $operation['description'] = 'Remboursement';
                    $operation['amount'] = -$transfer->getAmount();
                }
                if (!is_null($counterMandataire)) {
                    $operation['description'] .= ' (';
                    if ($checkSide == 'debit:client' or $checkSide == 'credit:owner') {
                        $operation['description'] .= '<- ';
                        $operation['description'] .= $counterMandataire->getShortLabel();
                        $methodName = 'get' . ucfirst($counterMandataire->getCounterSide($party));
                        if (!is_null($counterMandataire->getContract())) $operation['description'] .= ' ' . $counterMandataire->$methodName()->getLabel();
                    } else {
                        $operation['description'] .= '-> ';
                        $operation['description'] .= $counterMandataire->getShortLabel();
                        $methodName = 'get' . ucfirst($counterMandataire->getCounterSide($party));
                        if (!is_null($counterMandataire->getContract())) $operation['description'] .= ' ' . $counterMandataire->$methodName()->getLabel();
                    }

                    $operation['description'] .= ')';
                }
                $result['operations'][] = $operation;
            }
        }

        return $result;
    }

    public function setInvoice(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function getInvoice()
    {
        return $this->invoice;
    }

}