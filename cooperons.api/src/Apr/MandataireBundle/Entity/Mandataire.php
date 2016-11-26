<?php
namespace Apr\MandataireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use \Apr\ContractBundle\Entity\Party;
use \Apr\ContractBundle\Entity\LegalDocument;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\PreSerialize;

use Apr\CoreBundle\Tools\Tools;

/**
 * @ORM\Entity
 * @ORM\Table(name="mandataire")
 */
class Mandataire
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \Apr\ContractBundle\Entity\Party $owner
     * @ORM\ManyToOne(targetEntity="\Apr\ContractBundle\Entity\Party", cascade = {"persist"})
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id") 
     * @Exclude
     */
    private $owner;

    /**
     * @ORM\Column(name="owner_label" , type="string", length=255)
     */
    private $ownerLabel;

    /**
     * @ORM\Column(name="owner_account_id" , type="integer")
     */
    private $ownerAccountId;

    /**
     * @ORM\Column(name="owner_account_ref" , type="string", length=5)
     */
    private $ownerAccountRef;

    /**
     * @ORM\Column(name="owner_income_ref" , type="string", length=5)
     */
    private $ownerIncomeRef;

    /**
     * @var \Apr\ContractBundle\Entity\Party $client
     * @ORM\ManyToOne(targetEntity="\Apr\ContractBundle\Entity\Party", cascade = {"persist"})
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id") 
     * @Exclude
     */
    private $client;

    /**
     * @ORM\Column(name="client_account_id" , type="integer")
     */
    private $clientAccountId;

    /**
     * @ORM\Column(name="client_label" , type="string", length=255)
     */
    private $clientLabel;

    /**
     * @ORM\Column(name="client_account_ref" , type="string", length=5)
     */
    private $clientAccountRef;

    /**
     * @ORM\Column(name="client_income_ref" , type="string", length=5)
     */
    private $clientIncomeRef;

    /**
     * Par défaut, possible de créer un premier Settlement
     * @ORM\Column(name="can_settle", type="boolean")
     */
    private $canSettle = true;

    /**
     * @ORM\Column(name="depot", type="float")
     */
    private $depot = 0;

    /**
     * @ORM\Column(name="min_depot", type="float")
     */
    private $minDepot = 0;

    /**
     * @ORM\Column(name="liquidation_date", type="datetime", nullable=true)
     * @var DateTime
     */
    private $liquidationDate;

    /**
     * @ORM\Column(name="total_income_ht", type="float")
     * @var float
     */
    private $totalIncomeHt = 0;

    /**
     * @ORM\Column(name="invoicing_frequency", type="integer")
     * @var integer
     */
    private $invoicingFrequency = 2;

    // Attention: bien mettre à jour aussi SettlementRepository ...
    private $arrInvoicingFrequency = array(
        0 => 'auto',
        1 => 'week',
        2 => 'month',
        3 => 'quarter'
    );

    /**
     * @var Invoice
     *
     * @ORM\OneToOne(targetEntity="Invoice", cascade = {"persist"})
     * @ORM\JoinColumn(name="last_invoice_id", referencedColumnName="id", nullable=true)
     * @Exclude
     */
    private $lastInvoice = null;

    /**
     * @ORM\OneToMany(targetEntity="Settlement", mappedBy="mandataire", cascade = {"persist"})
     * @ORM\OrderBy({"createdDate" = "DESC"})
     * @Exclude
     */
    private $allSettlements;

    /**
     * @ORM\OneToMany(targetEntity="Payment", mappedBy="mandataire", cascade = {"persist"})
     * @ORM\OrderBy({"createdDate" = "DESC"})
     * @Exclude
     */
    private $allPayments;

    /**
     * @ORM\OneToMany(targetEntity="Transfer", mappedBy="debitMandataire", cascade = {"persist"})
     * @ORM\OrderBy({"createdDate" = "DESC"})
     * @Exclude
     */
    private $allDebitedTransfers;

    /**
     * @ORM\OneToMany(targetEntity="Transfer", mappedBy="creditMandataire", cascade = {"persist"})
     * @ORM\OrderBy({"createdDate" = "DESC"})
     * @Exclude
     */
    private $allCreditedTransfers;

    /**
     * @ORM\OneToMany(targetEntity="Record", mappedBy="debitMandataire", cascade = {"persist"})
     * @ORM\OrderBy({"createdDate" = "DESC"})
     * @Exclude
     */
    private $allRecordsAsDebit;

    /**
     * @ORM\OneToMany(targetEntity="Record", mappedBy="creditMandataire", cascade = {"persist"})
     * @ORM\OrderBy({"createdDate" = "DESC"})
     * @Exclude
     */
    private $allRecordsAsCredit;

    /**
     * @var \Apr\ContractBundle\Entity\Contract
     *
     * @ORM\OneToOne(targetEntity="\Apr\ContractBundle\Entity\Contract", mappedBy="mandataire", cascade = {"persist"})
     * @Exclude
     */
    private $contract;

    /**
     * @var \Apr\ContractBundle\Entity\LegalDocument
     *
     * @ORM\OneToOne(targetEntity="\Apr\ContractBundle\Entity\LegalDocument", mappedBy="mandataire", cascade = {"persist"})
     * @Exclude
     */
    private $legalDocument;

    /**
     * @var Payment
     * @ORM\OneToOne(targetEntity="Payment", cascade = {"persist"})
     * @ORM\JoinColumn(name="stand_by_payment_out_id", referencedColumnName="id", nullable = true)
     * @Exclude
     */
    private $standByPaymentOut;

    /**
     * @var \Apr\ContractBundle\Entity\Party
     * Pointe sur le Party si le Mandataire est un Mandataire de Bank <-> Coopérons
     * @ORM\OneToOne(targetEntity="\Apr\ContractBundle\Entity\Party", mappedBy="mandataire", cascade = {"persist"})
     * @Exclude
     */
    private $party;
    
    /**
     * @author Fondative <dev devteam@fondative.com>
     * @var string
     */
    private $label;
    
    /**
     * @author Fondative <dev devteam@fondative.com>
     * @var string
     */
    private $shortLabel;
    
    /**
     * @author Fondative <dev devteam@fondative.com>
     * @var string
     */
    private $clientMember;
    

    public function __construct($legalDocument, Party $owner, Party $client, 
                                $ownerLabel, $clientLabel, $ownerAccountRef, $clientAccountRef, $ownerIncomeRef, $clientIncomeRef)
    {
        if(!is_null($legalDocument)) $this->setLegalDocument($legalDocument);
        $this->owner = $owner;
        $this->ownerLabel = $ownerLabel;
        $owner->addAccount();
        $this->ownerAccountId = $owner->getLastAccountId();
        $this->ownerAccountRef = $ownerAccountRef;
        $this->ownerIncomeRef = $ownerIncomeRef;
        $this->client = $client;
        $this->clientLabel = $clientLabel;
        $client->addAccount();
        $this->clientAccountId = $client->getLastAccountId();
        $this->clientAccountRef = $clientAccountRef;
        $this->clientIncomeRef = $clientIncomeRef;
    }

    public function __clone()
    {
        $this->depot = $this->totalIncomeHt = 0;
        $this->contract = $this->legalDocument = $this->standByPaymentOut = $this->liquidationDate = $this->lastInvoice = null;
    }

    public function getId(){
        return $this->id;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function getOwnerLabel()
    {
        return $this->ownerLabel;
    }

    public function getOwnerAccountRef()
    {
        return $this->ownerAccountRef;
    }

    public function getOwnerAccountId()
    {
        return $this->ownerAccountId;
    }

    public function getOwnerIncomeRef()
    {
        return $this->ownerIncomeRef;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getClientLabel()
    {
        return $this->clientLabel;
    }

    public function getClientAccountRef()
    {
        return $this->clientAccountRef;
    }

    public function getClientAccountId()
    {
        return $this->clientAccountId;
    }

    public function getClientIncomeRef()
    {
        return $this->clientIncomeRef;
    }

    public function getCanSettle() {
        return $this->canSettle;
    }

    public function notCanSettle() {
        return $this->canSettle = false;
    }

    public function updateCanSettle() {
        if(($this->depot < $this->minDepot && $this->canSettle)) $this->canSettle = false;
        if(($this->depot >= $this->minDepot && !$this->canSettle)) $this->canSettle = true;
    }

    public function getSide(Party $party) {
        if($this->client == $party) {
            return 'client';
        } elseif($this->owner == $party) {
            return 'owner';
        } else {
            return null;
        }
    }

    public function getCounterSide(Party $party) {
        if($this->client == $party) {
            return 'owner';
        } elseif($this->owner == $party) {
            return 'client';
        } else {
            return null;
        }
    }

    public function getCounterParty(Party $party) {
        if($this->client == $party) {
            return $this->owner;
        } elseif($this->owner == $party) {
            return $this->client;
        } else {
            return null;
        }
    }

    public function getParty()
    {
        return $this->party;
    }

    public function addDepotAmount($amount)
    {
        $this->depot += $amount;
        if($this->contract) $this->owner->addDepotsAmount($amount);
        $this->updateCanSettle();
    }

    public function getDepot()
    {
        return $this->depot;
    }

    public function addMinDepotAmount($amount)
    {
        $this->setMinDepot($this->minDepot+$amount);
    }

    public function setMinDepot($minDepot){
        $this->minDepot = $minDepot;
        $this->updateCanSettle();
    }

    public function getMinDepot() {
        return $this->minDepot;
    }

    public function calculateDepot($amount) {
        return ($this->getDepot() > ($amount+$this->getMinDepot()))?0:$this->getMinDepot()*2;
    }

    public function getFreeDepot() {
        return max(0,$this->depot - $this->minDepot);
    }

    public function liquidate()
    {
        $this->liquidationDate = Tools::DateTime('now');
    }

    public function reactivate()
    {
        $this->liquidationDate = null;
    }

    public function getLiquidationDate()
    {
        return $this->liquidationDate;
    }

    public function isRemovable() {
        return (count($this->allPayments) == 0) && (count($this->allSettlements) == 0) && (count($this->getAllRecords()) == 0);
    }

    public function getAllSettlements()
    {
        return $this->allSettlements;
    }

    public function addTotalIncomeHt($amountHt)
    {
        $this->totalIncomeHt += $amountHt;
        $this->owner->addTotalIncomeHt($amountHt);
    }

    public function getTotalIncomeHt()
    {
        return $this->totalIncomeHt;
    }

    public function getAllPayments()
    {
        return $this->allPayments;
    }

    public function getAllDebitedTransfers()
    {
        return $this->allDebitedTransfers;
    }

    public function getAllCreditedTransfers()
    {
        return $this->allCreditedTransfers;
    }

    public function getAllTransfers() {
        return new ArrayCollection(array_merge((array) $this->allDebitedTransfers->toArray(), (array) $this->allCreditedTransfers->toArray()));
    }

    /*
     * GetClientMember utilisé pour les emails ...
     */
    public function getClientMember()
    {
        if($this->contract) {
            // ClientCollaborator ...
            return $this->contract->getClientMember();
        } else {
            return $this->client->getMember();
        }
    }

    public function getOwnerMember()
    {
        if($this->contract) {
            // OwnerCollaborator ...
            return $this->contract->getOwnerMember();
        } else {
            return $this->owner->getMember();
        }
    }

    /**
     * Contract
     */
    
    public function clearLegalDocument() {
        $this->legalDocument = null;
    }
    
    public function setLegalDocument(LegalDocument $legalDocument) {
        $this->legalDocument = $legalDocument;
        $this->legalDocument->setMandataire($this);
        $this->contract = $legalDocument->getContract();
    }

    public function getLegalDocument() {
        return $this->legalDocument;
    }

    public function getContract() {
        return $this->contract;
    }

    public function isActive()
    {
        if($this->legalDocument) {
            return $this->legalDocument->getStatus() == 'active';
        } else {
            // Coopérons <-> AE/Corporate
            return $this->client->isActive();
        }
    }

    public function getLabel() {
        if($this->contract) {
            $result = "contrat entre ".$this->client->getLabel()." et ".$this->owner->getLabel();
        } else {
            // Compte Mandataire Coopérons ...
            $result = "contrat Coopérons ".$this->client->getTypeLabel();
        };

        return $result;
    }

    public function getShortLabel()
    {
        if($this->contract) {
            return 'Dépôt Contrat';
        } else {
            // Compte Mandataire Coopérons ...
            if($this->client->getAutoEntrepreneur()) {
                return 'Compte Mandataire';
            } else {
                return 'Compte Entreprise';
            }
        }
    }

    /**
     * Payment
     */
    public function setStandByPaymentOut($payment) {
        $this->standByPaymentOut = $payment;
    }

    public function getStandByPaymentOut() {
        return $this->standByPaymentOut;
    }

    /**
     * Invoice
     */

    public function setInvoicingFrequency($strFrequency) {
        $this->invoicingFrequency = array_search($strFrequency, $this->arrInvoicingFrequency);
    }

    public function getInvoicingFrequency() {
        return $this->arrInvoicingFrequency[$this->invoicingFrequency];
    }

    public function getCutOffDate() {
        return Tools::firstDayOf($this->getInvoicingFrequency());
    }

    public function getLastInvoice() {
        return $this->lastInvoice;
    }

    public function setLastInvoice(Invoice $lastInvoice) {
        $this->lastInvoice = $lastInvoice;
        $this->owner->setLastInvoice($lastInvoice);
    }

    public function getAllRecords() {
        return new ArrayCollection(array_merge((array) $this->allRecordsAsDebit->toArray(), (array) $this->allRecordsAsCredit->toArray()));
    }
   
    /**
     * @author Fondative <devteam@fondative.com>
     * @PreSerialize
     */
    public function beforeSerialization(){
        $this->label = $this->getLabel();
        $this->shortLabel = $this->getShortLabel();
        $this->clientLabel = $this->client->getLabel();
        $this->ownerLabel = $this->owner->getLabel();
        $this->clientMember = $this->getClientMember();
    }
}