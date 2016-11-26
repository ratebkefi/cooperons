<?php

namespace Apr\ContractBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Apr\CoreBundle\Tools\Tools;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\PreSerialize;

/**
 * @ORM\Entity(repositoryClass="Apr\ContractBundle\Repository\PartyRepository")
 * @ORM\Table(name="parties")
 */

class Party
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \Apr\AutoEntrepreneurBundle\Entity\AutoEntrepreneur
     *
     * @ORM\OneToOne(targetEntity="\Apr\AutoEntrepreneurBundle\Entity\AutoEntrepreneur", mappedBy="party", cascade = {"persist"}) 
     */
    private $autoEntrepreneur = null;

    /**
     * @var \Apr\CorporateBundle\Entity\Corporate
     *
     * @ORM\OneToOne(targetEntity="\Apr\CorporateBundle\Entity\Corporate", mappedBy="party", cascade = {"persist"})
     *
     */
    private $corporate = null;

    /**
     * @var ArrayCollection $allContractsAsOwner
     *
     * @ORM\OneToMany(targetEntity="Contract", mappedBy="owner", cascade = {"persist"})
     * @Exclude
     */
    private $allContractsAsOwner;

    /**
     * @var ArrayCollection $allContractsAsClient
     *
     * @ORM\OneToMany(targetEntity="Contract", mappedBy="client", cascade = {"persist"})
     * @Exclude
     */
    private $allContractsAsClient;

    /* Contracts Coopérons */

    /**
     * @var Contract $coopContract
     * @ORM\OneToOne(targetEntity="Contract", cascade = {"persist"})
     * @ORM\JoinColumn(name="coop_contract_id", referencedColumnName="id", nullable=true)
     *
     */
    private $coopContract;

    /**
     * @var LegalDocument $cguDocument
     * @ORM\ManyToOne(targetEntity="LegalDocument", cascade = {"persist"})
     * @ORM\JoinColumn(name="cgu_document_id", referencedColumnName="id")
     * @Exclude
     */
    private $cguDocument;

    /**
     * @var LegalDocument $cgvDocument
     * @ORM\ManyToOne(targetEntity="LegalDocument", cascade = {"persist"})
     * @ORM\JoinColumn(name="cgv_document_id", referencedColumnName="id")
     * @Exclude
     */
    private $cgvDocument;

    /**
     * @var LegalDocument $cpContractsDocument
     * @ORM\ManyToOne(targetEntity="LegalDocument", cascade = {"persist"})
     * @ORM\JoinColumn(name="cp_contracts_document_id", referencedColumnName="id")
     * @Exclude
     */
    private $cpContractsDocument;

    /**
     * @var LegalDocument $cpCorpAEDocument
     * @ORM\ManyToOne(targetEntity="LegalDocument", cascade = {"persist"})
     * @ORM\JoinColumn(name="cp_corp_ae_document_id", referencedColumnName="id")
     * @Exclude
     */
    private $cpCorpAEDocument;

    /**
     * @var LegalDocument $cpMandataireAEDocument
     * @ORM\ManyToOne(targetEntity="LegalDocument", cascade = {"persist"})
     * @ORM\JoinColumn(name="cp_mandataire_ae_document_id", referencedColumnName="id")
     * @Exclude
     */
    private $cpMandataireAEDocument;

    /**
     * @var LegalDocument $cpPromoteurDocument
     * @ORM\ManyToOne(targetEntity="LegalDocument", cascade = {"persist"})
     * @ORM\JoinColumn(name="cp_promoteur_document_id", referencedColumnName="id")
     * @Exclude
     */
    private $cpPromoteurDocument;

    /**
     * @var LegalDocument $cpStimulationDocument
     * @ORM\ManyToOne(targetEntity="LegalDocument", cascade = {"persist"})
     * @ORM\JoinColumn(name="cp_stimulation_document_id", referencedColumnName="id")
     * @Exclude
     */
    private $cpStimulationDocument;

    private $arrStatus = array(
        'cgu',
        'cgv',
        'cpContracts',
        'cpCorpAE',
        'cpMandataireAE',
        'cpPromoteur',
        'cpStimulation'
    );

    /**
     *
     * @ORM\Column(name="is_cooperons", type="boolean")
     */
    private $isCooperons = false;

    /* Collaborators */

    /**
     * @var Collaborator $collaborator
     * @ORM\OneToOne(targetEntity="Collaborator", cascade = {"persist"})
     * @ORM\JoinColumn(name="administrator_id", referencedColumnName="id")
     */
    private $administrator ;

    /**
     * @var ArrayCollection $allCollaborators
     *
     * @ORM\OneToMany(targetEntity="Collaborator", mappedBy="party")
     * @Exclude
     */
    private $allCollaborators;

    /* Mandataire */

    /**
     *
     * IndexAccountId: 1 (correspond à Compte 100)
     *
     * @ORM\Column(name="last_account_id", type="integer")
     */
    private $lastAccountId = 1;

    /**
     * Mandataire: Coopérons <-> Party (null pour Party == Cooperons)
     * @var \Apr\MandataireBundle\Entity\Mandataire
     * @ORM\OneToOne(targetEntity="\Apr\MandataireBundle\Entity\Mandataire", inversedBy="party", cascade = {"persist"})
     * @ORM\JoinColumn(name="mandataire_id", referencedColumnName="id", nullable=true)
     *
     */
    private $mandataire;

    /**
     * @ORM\Column(name="can_settle", type="boolean")
     */
    private $canSettle = false;

    /**
     *
     * Balance: Compte 100 ...
     *
     * @ORM\Column(name="balance", type="float")
     */
    private $balance = 0;

    /**
     * @ORM\Column(name="total_income_ht", type="float")
     * @var float
     */
    private $totalIncomeHt = 0;

    /**
     * @ORM\Column(name="total_depots", type="float")
     * @var float
     */
    private $totalDepots = 0;

    /**
     * @ORM\Column(name="provision_rate", type="float", nullable = true)
     */
    private $provisionRate = null;

    /**
     * @var \Apr\MandataireBundle\Entity\Invoice
     *
     * @ORM\OneToOne(targetEntity="\Apr\MandataireBundle\Entity\Invoice", cascade = {"persist"})
     * @ORM\JoinColumn(name="last_invoice_id", referencedColumnName="id", nullable=true)
     * @Exclude
     */
    private $lastInvoice = null;

    /**
     * @author Fondative <devteam@fondative.com>
     * @var String
     */
    private $label;
    private $local;
    private $type;
    private $canTerminate;
    private $status;

    public function __construct() {
    }
    
    public function getId(){
        return $this->id;
    }

    public function isCooperons(){
        return $this->isCooperons;
    }

    public function setAutoEntrepreneur($autoEntrepreneur){
        $this->autoEntrepreneur = $autoEntrepreneur;
        $this->provisionRate = 30;
    }

    public function getAutoEntrepreneur(){
        return $this->autoEntrepreneur;
    }

    public function setCorporate($corporate){
        $this->corporate = $corporate;
    }

    public function getCorporate(){
        return $this->corporate;
    }

    public function getType(){
        if(!is_null($this->autoEntrepreneur)) {
            return 'autoEntrepreneur';
        } elseif(!is_null($this->corporate)) {
            return 'corporate';
        } elseif(is_null($this->mandataire)) {
            return 'bank';
        }
    }

    public function getTypeLabel(){
        if(!is_null($this->autoEntrepreneur)) {
            return 'Auto-Entrepreneur';
        } elseif(!is_null($this->corporate)) {
            return 'Entreprise';
        } elseif(is_null($this->mandataire)) {
            return 'Banque';
        }
    }

    public function isActive(){
        return !is_null($this->administrator);
    }

    public function getMember($collaborator = null){
        if(!is_null($collaborator)) {
            return $collaborator->getMember();
        } elseif(!is_null($this->administrator)) {
            return $this->administrator->getMember();
        } else {
            return null;
        }
    }
    
    public function isAuthorized($member) {
        return ($this->getMember() == $member);
    }

    public function getDescription(){
        if(!is_null($this->autoEntrepreneur)) {
            return $this->autoEntrepreneur->getDescription();
        } elseif(!is_null($this->corporate)) {
            return $this->corporate->getDescription();
        } elseif(is_null($this->mandataire)) {
            return 'Banque';
        }
    }

    public function getLabel(){
        if(!is_null($this->autoEntrepreneur)) {
            return $this->autoEntrepreneur->getLabel();
        } elseif(!is_null($this->corporate)) {
            return $this->corporate->getRaisonSocial();
        } elseif(is_null($this->mandataire)) {
            return 'Banque';
        }
    }

    // ToDo: Normalisation Corporate/Contact ... country ...
    public function getLocal(){
        $businessEntity = null;
        if(!is_null($this->autoEntrepreneur)) {
            $businessEntity = $this->getMember()->getUser()->getContact();
        } elseif(!is_null($this->corporate)) {
            $businessEntity = $this->corporate;
        }

        if(!is_null($businessEntity)) return array(
            'address' => $businessEntity->getAddress(),
            'secondAddress' => $businessEntity->getSecondAddress(),
            'city' => $businessEntity->getCity(),
            'postalCode' => $businessEntity->getPostalCode(),
            'country' => !is_null($this->corporate)?$businessEntity->getCountry():null,
        );
    }

    public function getAllContracts()
    {
        return new ArrayCollection(array_merge((array) $this->allContractsAsOwner->toArray(), (array) $this->allContractsAsClient->toArray()));
    }
    
    /* Coopérons Contracts */

    public function canTerminate() {
        // Tous les collaborateurs doivent être supprimés
        if(count($this->getAllCollaborators())>1) {
            return false;
        } else {
            foreach($this->getAllContracts() as $contract) {
                // Tous les contrats doivent être résiliés
                if(!$contract->isCancel()) return false;
            }
        }
        return true;
    }

    public function getCoopContract() {
        return $this->coopContract;
    }

    public function getStatus() {
        $result = array('Contracts' => array());
        foreach ($this->arrStatus as $type) {
            $result['Contracts'][$type] = (!is_null($this->getCoopLegalDocument($type)) || $this->isCooperons);
        }
        return $result;
    }

    public function setCoopLegalDocument($type, $legalDocument) {
        $property = $type."Document";
        $this->$property = $legalDocument;
        if($legalDocument && !$this->coopContract) $this->coopContract = $legalDocument->getContract();
    }

    public function getCoopLegalDocument($type) {
        $property = $type."Document";
        return $this->$property;
    }

    public function getCguDocument() {
        return $this->cguDocument;
    }

    public function getCgvDocument() {
        return $this->cgvDocument;
    }

    public function getCpContractsDocument() {
        return $this->cpContractsDocument;
    }

    public function getCpCorpAEDocument() {
        return $this->cpCorpAEDocument;
    }

    public function getCpMandataireAEDocument() {
        return $this->cpMandataireAEDocument;
    }

    public function getCpPromoteurDocument() {
        return $this->cpPromoteurDocument;
    }

    public function getCpStimulationDocument() {
        return $this->cpStimulationDocument;
    }

    /* Collaborators */

    // Nécessite $administrator not null ...
    public function setAdministrator(Collaborator $administrator) {
        $this->administrator = $administrator;
    }

    public function getAdministrator() {
        return $this->administrator;
    }

    public function getAllCollaborators() {
        return $this->allCollaborators;
    }

    /* Mandataire */

    public function getCanSettle() {
        return $this->canSettle;
    }

    public function setCanSettle($canSettle) {
        $this->canSettle = $canSettle;
    }

    public function addBalance($amount) {
        $this->balance += $amount;
    }

    public function getBalance() {
        return $this->balance;
    }

    public function setMandataire($mandataireCooperons) {
        $this->mandataire = $mandataireCooperons;
    }

    public function getMandataire() {
        return $this->mandataire;
    }

    public function addAccount() {
        $this->lastAccountId += 1;
    }

    public function getLastAccountId() {
        return $this->lastAccountId;
    }

    public function getProvisionRate() {
        return $this->provisionRate;
    }

    public function setProvisionRate($provisionRate) {
        $this->provisionRate = $provisionRate;
    }

    public function addTotalIncomeHt($amountHt) {
        $this->totalIncomeHt += $amountHt;
    }

    public function getTotalIncomeHt() {
        return $this->totalIncomeHt;
    }

    public function addDepotsAmount($amount) {
        $this->totalDepots += $amount;
    }

    public function getTotalDepots() {
        return $this->totalDepots;
    }

    public function getLastInvoice() {
        return $this->lastInvoice;
    }

    public function setLastInvoice(\Apr\MandataireBundle\Entity\Invoice $lastInvoice) {
        $this->lastInvoice = $lastInvoice;
    }
    
    /**
     * @author Fondative <devteam@fondative.com>
     * @PreSerialize
     */
    public function beforeSerialization(){
        $this->local = $this->getLocal();
        $this->label = $this->getLabel();
        $this->type = $this->getType();
        $this->canTerminate = $this->canTerminate();
        $this->status = $this->getStatus();
    }
}