<?php
namespace Apr\MandataireBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Apr\CoreBundle\Tools\Tools;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\PreSerialize;

/**
 * @ORM\Entity(repositoryClass="Apr\MandataireBundle\Repository\SettlementRepository")
 * @ORM\Table(name="settlement")
 */
class Settlement
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Mandataire $mandataire
     * @ORM\ManyToOne(targetEntity="Mandataire", inversedBy="allSettlements", cascade = {"persist"})
     * @ORM\JoinColumn(name="mandataire_id", referencedColumnName="id") 
     */
    private $mandataire ;

    /**
     * @ORM\Column(name="unit_amount", type="float")
     */
    private $unitAmount;

    /**
     * @ORM\Column(name="quantity", type="float")
     */
    private $quantity;

    /**
     * @ORM\Column(name="amount_ht", type="float")
     *
     */
    private $amountHt;

    /**
     * @ORM\Column(name="rate_tva", type="float")
     *
     */
    private $rateTva;

    /**
     * @ORM\Column(name="amount", type="float")
     * @Assert\GreaterThan(value = "0", message = "Amount must be positive")
     */
    private $amount;

    /**
     * @ORM\Column(name="type" , type="string", length=255, nullable = true)
     *
     */
    private $type;

    /**
     * @ORM\Column(name="status", type="integer")
     *
     */
    private $status = 0;

    // Attention: bien mettre à jour aussi SettlementRepository ...
    private $arrStatus = array(
        0 => 'waitingForNotify',
        1 => 'waitingForPayment',
        2 => 'settled',
        3 => 'released');

    /**
     * @ORM\Column(name="priority", type="integer")
     */
    private $priority = 0;

    /**
     * @ORM\Column(name="description", type="text")
     *
     */
    private $description;

    /**
     * @ORM\Column(name="created_date", type="datetime")
     * @var DateTime
     */
    private $createdDate;

    /**
     * @ORM\Column(name="validated_date", type="datetime", nullable = true)
     * @var DateTime
     */
    private $validatedDate = null;

    /**
     * @var Invoice $invoice
     * @ORM\ManyToOne(targetEntity="Invoice")
     * @ORM\JoinColumn(name="invoice_id", referencedColumnName="id")
     */
    private $invoice ;

    /**
     * @var Record
     *
     * @ORM\ManyToOne(targetEntity="Record", inversedBy="allSettlements", cascade = {"persist"})
     * @ORM\JoinColumn(name="record_id", referencedColumnName="id")
     * @Exclude
     */
    private $record;

    /**
     * @var Settlement $settlementFee
     * @ORM\ManyToOne(targetEntity="Settlement")
     * @ORM\JoinColumn(name="settlement_fee_id", referencedColumnName="id")
     */
    private $settlementFee ;

    // Program ...

    /**
     * @var \Apr\ProgramBundle\Entity\Avantage $avantage
     * @ORM\OneToOne(targetEntity="\Apr\ProgramBundle\Entity\Avantage", mappedBy="settlement")
     */
    private $avantage ;

    /**
     * @var \Apr\ProgramBundle\Entity\Program $programWithPendingSettlementAbonnement
     * @ORM\OneToOne(targetEntity="\Apr\ProgramBundle\Entity\Program", mappedBy="pendingSettlementAbonnement")
     */
    private $programWithPendingSettlementAbonnement ;

    /**
     * @var ArrayCollection $allAccountPointsHistory
     * @ORM\OneToMany(targetEntity="\Apr\ProgramBundle\Entity\AccountPointsHistory", mappedBy="settlement")
     */
    private $allAccountPointsHistory ;

    public function __construct(Mandataire $mandataire, $description, $calculateSettlement, $type = null)
    {
        if($mandataire->getLiquidationDate()) {
            throw new \Exception('Compte mandataire cloturé');
        } else {
            $this->createdDate = Tools::DateTime('now');
            $this->mandataire = $mandataire;
            $this->amount = $calculateSettlement['amountTtc'];
            $this->description = $description;
            $this->type = $type;
            $this->quantity = $calculateSettlement['quantity'];
            $this->unitAmount = $calculateSettlement['unitAmount'];
            $this->amountHt = $calculateSettlement['amountHt'];
            $this->rateTva = $calculateSettlement['rateTva'];
            // Avoirs toujours validés lors de la création ...
            if($this->amount < 0) $this->validate();
        }
    }

    public function notify()
    {
        if($this->status == 0) {
            $this->status = 1;
        }
    }

    public function validate($cushion = 0, $grossUpRate = 0)
    {
        if($this->amount < 0 or ($this->mandataire->getDepot() - $cushion) >= (ceil($this->amount * (1+$grossUpRate)*100)/100)) {
            $this->validatedDate = Tools::DateTime('now');
            $this->status = 2;
            $this->mandataire->addTotalIncomeHt($this->amountHt);
        }
    }

    public function getId(){
        return $this->id;
    }

    public function getUnitAmount()
    {
        return $this->unitAmount;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getAmountHt()
    {
        return $this->amountHt;
    }

    public function getRateTva()
    {
        return $this->rateTva;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getAmountTva()
    {
        return ($this->amount - $this->amountHt);
    }

    public function getMandataire()
    {
        return $this->mandataire;
    }

    public function getPriority(){
        return $this->priority;
    }

    public function setPriority($priority){
        $this->priority = $priority;
    }

    public function getType(){
        return $this->type;
    }

    public function getStatus()
    {
        return $this->arrStatus[$this->status];
    }

    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    public function getValidatedDate()
    {
        return $this->validatedDate;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setInvoice(Invoice $invoice)
    {
        $this->invoice = $invoice;
        // Enregistrement de l'invoice pour le client
        $this->record->setInvoice($invoice);
    }

    public function getInvoice()
    {
        return $this->invoice;
    }

    public function setSettlementFee(Settlement $settlementFee)
    {
        $this->settlementFee = $settlementFee;
    }

    public function getSettlementFee()
    {
        return $this->settlementFee;
    }

    /**
     * Contract
     */

    public function getContract()
    {
        return $this->mandataire->getContract();
    }

    /**
     * Record
     */

    public function setRecord(Record $record) {
        $this->record = $record;
    }

    public function getRecord() {
        return $this->record;
    }

    /**
     * Program
     */

    public function getAvantage()
    {
        return $this->avantage;
    }

    public function getProgramWithPendingSettlementAbonnement()
    {
        return $this->programWithPendingSettlementAbonnement;
    }

    public function getAllAccountPointsHistory()
    {
        return $this->allAccountPointsHistory;
    }

    public function getProgram()
    {
        if($this->type == 'abonnement') {
            return $this->getProgramWithPendingSettlementAbonnement();            
        } elseif($this->type == 'points' && count($this->allAccountPointsHistory)) {
            return $this->allAccountPointsHistory[0]->getProgram();
        }
    }

}