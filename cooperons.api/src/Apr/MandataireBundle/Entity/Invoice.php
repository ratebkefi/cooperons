<?php
namespace Apr\MandataireBundle\Entity;

use Apr\MandataireBundle\Repository\SettlementRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Apr\CoreBundle\Tools\Tools;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\PreSerialize;

/**
 * @ORM\Entity
 * @ORM\Table(name="invoices")
 */
class Invoice
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var Program $program
     * @ORM\ManyToOne(targetEntity="Mandataire")
     * @ORM\JoinColumn(name="mandataire_id", referencedColumnName="id")
     * @Exclude
     */
    private $mandataire ;

    /**
     * @ORM\Column(name="balance", type="float")
     *
     */
    private $balance;

    /**
     * @ORM\Column(name="amount_ht", type="float")
     *
     */
    private $amountHt;

    /**
     * @ORM\Column(name="amount_ttc", type="float")
     *
     */
    private $amountTtc;

    /**
     * @ORM\Column(name="series", type="integer", nullable = true)
     */
    private $series = null;

    /**
     * @ORM\Column(name="year", type="integer")
     */
    private $year;

    /**
     * @ORM\Column(name="month", type="integer")
     */
    private $month;

    /**
     * @ORM\Column(name="rank", type="integer")
     */
    private $rank;

    /**
     * @ORM\Column(name="created_date", type="datetime")
     * @var DateTime
     */
    private $createdDate;

    /**
     * @var Invoice
     *
     * Facture précédente pour le même mandataire ...
     *
     * @ORM\OneToOne(targetEntity="Invoice", cascade = {"persist"})
     * @ORM\JoinColumn(name="last_invoice_id", referencedColumnName="id", nullable=true)
     */
    private $lastInvoice = null;

    /**
     * @ORM\Column(name="invoicing_date", type="datetime")
     * @var DateTime
     */
    private $invoicingDate;

    /**
     * @ORM\OneToMany(targetEntity="Settlement", mappedBy="invoice", cascade = {"persist"}) 
     */
    private $allSettlements;

    /**
     * @ORM\OneToMany(targetEntity="Record", mappedBy="invoice", cascade = {"persist"})
     * @Exclude
     */
    private $allRecords;
    
    /**
     * @author Fondative <dev devteam@fondative.com>
     * @var string
     */
    private $ref;
    
    /**
     * @author Fondative <dev devteam@fondative.com>
     * @var float
     */
    private $startBalance;

    public function __construct(Mandataire $mandataire, $amountHt, $amountTtc) {
        $this->createdDate = Tools::DateTime('now');
        $this->mandataire = $mandataire;
        $this->amountHt = $amountHt;
        $this->amountTtc = $amountTtc;

        $invoicingDate = $mandataire->getCutOffDate();
        $invoicingDate->modify('-1 day');
        $this->invoicingDate = $invoicingDate;
        $this->year = $invoicingDate->format('Y');
        $this->month = $invoicingDate->format('n');

        $lastPartyInvoice = $this->mandataire->getOwner()->getLastInvoice();
        if(is_null($lastPartyInvoice)) {
            $this->rank = 1;
        } else {
            if($this->year == $lastPartyInvoice->getYear() && $this->month == $lastPartyInvoice->getMonth()) {
                $this->rank = $lastPartyInvoice->getRank()+1;
            } else {
                $this->rank = 1;
            }
        }

        $lastMandataireInvoice = $mandataire->getLastInvoice();
        if(is_null($lastMandataireInvoice)) {
            $this->balance = 0;
        } else {
            $this->balance = $lastMandataireInvoice->getEndBalance();
            $this->lastInvoice = $lastMandataireInvoice;
        }

        $mandataire->setLastInvoice($this);
    }
    
    public function getId(){
        return $this->id;
    }

    public function getCreatedDate() {
        return $this->createdDate;
    }

    public function getInvoicingDate() {
        return $this->invoicingDate;
    }

    public function getMandataire() {
        return $this->mandataire;
    }

    public function getLastInvoice() {
        return $this->lastInvoice;
    }

    public function getStartDate() {
        return is_null($this->lastInvoice)?null:$this->lastInvoice->getInvoicingDate();
    }

    public function getStartBalance() {
        return is_null($this->lastInvoice)?0:$this->lastInvoice->getEndBalance();
    }

    public function getEndBalance() {
        return $this->balance;
    }

    public function getAmountHt() {
        return $this->amountHt;
    }

    public function getAmountTtc() {
        return $this->amountTtc;
    }

    public function getSeries() {
        return $this->series;
    }

    public function getYear() {
        return $this->year;
    }

    public function getMonth() {
        return $this->month;
    }

    public function getRank() {
        return $this->rank;
    }

    public function getAllSettlements() {
        return $this->allSettlements;
    }

    public function addSettlement(Settlement $settlement) {
        $this->allSettlements[] = $settlement;
        $settlement->setInvoice($this);
    }

    public function getAllRecords() {
        return $this->allRecords;
    }

    public function addRecord(Record $record) {
        $this->allRecords[] = $record;
        $side = $record->getSide($this->mandataire);
        $amount = $record->getAmount();
        if($side == 'debit')  $amount = -$amount;
        $this->balance += $amount;
        $record->setInvoice($this);
    }

    public function getRef() {
        $ref = '';
        if(!is_null($this->series)) $ref .= sprintf("%'.02d", $this->series);
        $ref .= $this->year;
        $ref .= sprintf("%'.02d", $this->month);
        $ref .= sprintf("%'.04d", $this->rank);
        return $ref;
    }

    public function getFileName() {
        return sprintf("%'.05d", $this->mandataire->getOwner()->getId()).'-'.$this->getRef().'.pdf';
    }
    
    /**
     * Set attributes before serialization
     *
     * @author Fondative <dev devteam@fondative.com>
     * @PreSerialize
     */
    public function beforeSerialization() {
        $this->ref = $this->getRef();
        $this->startBalance = $this->getStartBalance();
    }

}