<?php
namespace Apr\ProgramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Apr\CoreBundle\Tools\Tools;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\PreSerialize;

/**
 * @ORM\Entity(repositoryClass="Apr\ProgramBundle\Repository\ProgramRepository")
 * @UniqueEntity(
 *      fields = {"label"},
 *      message = "Intitulé existant")
 * @ORM\Table(name="programs")
 */
class Program
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="label" , type="string", length=255, unique=true)
     * @Assert\Regex(pattern="/^[a-zA-Z0-9 ]*$/", message = "Label invalide (caractères alphanumériques)")
     * @Assert\NotBlank(message = "Intitulé requis")
     */
    private $label;
    
    /**
     * @ORM\Column(name="description" , type="text", nullable=true)
     *
     */
    private $description;

    /**
     * @var ArrayCollection $operationCredit
     *
     * @ORM\OneToMany(targetEntity="OperationCredit", mappedBy="program", cascade = {"persist"})
     * @Assert\Valid
     * @Exclude
     */
    private $allOperations;
    
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Invitation", mappedBy="program")
     * @Exclude
     */
    private $invitations;
    
    /**
     * @var ArrayCollection $mails
     *
     * @ORM\OneToMany(targetEntity="MailInvitation", mappedBy="program", cascade = {"persist"})
     * @Exclude
     */
    private $allMailInvitations;
    
    /**
     * @ORM\Column(name="status", type="string")
     */
    protected $status='standby';
    
    /**
     * @ORM\Column(name="created_date", type="datetime")
     * @var \DateTime
     */
    protected $createdDate;
    
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="ParticipatesTo", mappedBy="program")
     * @Exclude
     */
    private $allParticipatesTo;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="PreProdParticipatesTo", mappedBy="program")
     * @Exclude
     */
    private $allPreProdParticipatesTo;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Sponsorship", mappedBy="program")
     * @Exclude
     */
    private $allSponsorships;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="PreProdSponsorship", mappedBy="program")
     * @Exclude
     */
    private $allPreProdSponsorships;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AccountPointsHistory", mappedBy="program")
     * @ORM\OrderBy({"id" = "DESC"})
     * @Exclude
     */
    private $allAccountHistoryPoints;

    /**
     * Classé par ordre décroissant de date - pour ne pas avoir de problème de Foreign Key lors de la suppression ...
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="PreProdAccountPointsHistory", mappedBy="program")
     * @ORM\OrderBy({"id" = "DESC"})
     * @Exclude
     */
    private $allPreProdAccountHistoryPoints;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Journal", mappedBy="program")
     * @Exclude
     */
    private $allJournals;

    /**
     * @ORM\Column(name="api_key", type="string", length=255, nullable=true)
     */
    private $apiKey;
    
    /**
     * @var \Apr\CorporateBundle\Entity\UploadedFile
     * @ORM\ManyToOne(targetEntity="\Apr\CorporateBundle\Entity\UploadedFile", cascade = {"persist", "remove"})
     * @ORM\JoinColumn(name="uploaded_file_id", referencedColumnName="id")
     * @Assert\Valid
     */
    private $image;

    /**
     * @ORM\Column(name="date_validate", type="datetime", nullable=true)
     * @var DateTime
     */
    protected $dateValidate = null;

    /**
     * @ORM\Column(name="date_expiration", type="datetime",nullable= true)
     * @var DateTime
     */
    private $dateExpiration = null;

    /**
     * @ORM\Column(name="sender_email", type="string", length=100, nullable=true)
     * 
     */
    private $senderEmail;

    /**
     * @ORM\Column(name="sender_name", type="string", length=100, nullable=true)
     *
     */
    private $senderName;

    /**
     * @ORM\Column(name="landing_url", type="string", length=100,  nullable=true)
     *
     */
    private $landingUrl;

    /**
     * @var \Apr\CorporateBundle\Entity\Corporate $corporate
     * @ORM\ManyToOne(targetEntity="\Apr\CorporateBundle\Entity\Corporate", cascade = {"persist"})
     * @ORM\JoinColumn(name="corporate_id", referencedColumnName="id", nullable=true)
     * @Exclude
     */
    private $corporate = null;

    /**
     * @var \Apr\ContractBundle\Entity\Collaborator $collaborator
     * @ORM\ManyToOne(targetEntity="\Apr\ContractBundle\Entity\Collaborator", cascade = {"persist"})
     * @ORM\JoinColumn(name="collaborator_id", referencedColumnName="id", nullable=true)
     * @Exclude
     */
    private $collaborator = null;

    /**
     * @var \Apr\MandataireBundle\Entity\Settlement $settlementAbonnement
     * @ORM\OneToOne(targetEntity="\Apr\MandataireBundle\Entity\Settlement", cascade = {"persist"})
     * @ORM\JoinColumn(name="settlement_abonnement_id", referencedColumnName="id", nullable=true)
     * @Exclude
     */
    private $settlementAbonnement = null;

    /**
     * @var \Apr\MandataireBundle\Entity\Settlement $pendingSettlementAbonnement
     * @ORM\OneToOne(targetEntity="\Apr\MandataireBundle\Entity\Settlement", cascade = {"persist"})
     * @ORM\JoinColumn(name="pending_settlement_abonnement_id", referencedColumnName="id", nullable=true)
     * @Exclude
     */
    private $pendingSettlementAbonnement = null;

    /**
     * Easy
     */

    /**
     * @var EasySetting
     * @ORM\OneToOne(targetEntity="EasySetting", cascade = {"persist"})
     * @ORM\JoinColumn(name="easy_setting_id", referencedColumnName="id")
     * @Exclude
     */
    private $easySetting;

    /**
     * @var Program
     *
     * @ORM\OneToOne(targetEntity="Program", inversedBy="newProgram", cascade = {"persist"})
     * @ORM\JoinColumn(name="old_program_id", referencedColumnName="id", nullable=true)
     */
    private $oldProgram;

    /**
     * @var Program
     *
     * @ORM\OneToOne(targetEntity="Program", mappedBy="oldProgram", cascade = {"persist"})
     */
    private $newProgram;

    /**
     * @author Fondative <dev devteam@fondative.com>
     * @var boolean not mapped in database
     */
    private $isEasy;

    public function __construct() {
        $this->createdDate = Tools::DateTime('now');
        $this->generateApiKey();
    }

    public function __clone() {
        $this->createdDate = Tools::DateTime('now');
        $this->label .=" (Copie)";
        $this->status = 'standby';
        $this->generateApiKey();
        $this->settlementAbonnement = $this->dateExpiration = $this->dateValidate = null;
    }

    public function cloneFinish(Program $oldProgram) {
        $this->oldProgram = $oldProgram;
        // Operations
        $oldOperations = $oldProgram->allOperations;
        $this->allOperations = new ArrayCollection();
        foreach($oldOperations as $operation) {
            $newOperation = clone $operation;
            $newOperation->setProgram($this);
            $this->allOperations[] = $newOperation;
        }

        // Easy
        if($oldProgram->isEasy()) {
            $newEasySetting = clone $oldProgram->getEasySetting();
            $newEasySetting->setProgram($this);
            $this->easySetting = $newEasySetting;
            $labelOperation = $newEasySetting->getLabelOperation();
            foreach($this->allOperations as $operation) {
                $key = array_search($operation->getLabel(), $labelOperation);
                $methodName = "set".ucfirst($key)."Operation";
                $this->easySetting->$methodName($operation);
            }
        }

        // MailInvitations
        $oldMails = $oldProgram->allMailInvitations;
        $this->allMailInvitations = new ArrayCollection();
        foreach($oldMails as $mail) {
            $newMail = clone $mail;
            $newMail->setProgram($this);
            $this->allMailInvitations[] = $newMail;
        }
    }
    
    public function getId() {
        return $this->id;
    }

    public function getLabel() {
        return $this->label;
    }

    public function getFileName() {
        return str_replace(' ', '_',$this->label);
    }

    public function setLabel($label) {
        $this->label = $label;
        return $this;
    }
    
    public function getDescription() {
        return $this->description;
    }
    
    public function setDescription($description) {
        $this->description = $description;
    }


    public function addOperation(OperationCredit $operation) {
        $this->allOperations[] = $operation;
        $operation->setProgram($this);
    }

    public function getAllOperations() {
        return $this->allOperations;
    }

    public function getAllMailInvitations() {
        return $this->allMailInvitations;
    }
    
    public function getInvitations() {
        return $this->invitations;
    }
    
    public function getCreatedDate() {
        return $this->createdDate;
    }

    public function getAllParticipatesTo() {
        if ($this->status == 'prod' or $this->status == 'standby') {
            // standby => en cas de réactivation ... on a conservé les parrainages passés ...
            return $this->allParticipatesTo;
        } elseif ($this->status == 'preprod') {
            return $this->allPreProdParticipatesTo;
        }
    }

    public function getAllSponsorships() {
        if ($this->status == 'prod' or $this->status == 'standby') {
            // standby => en cas de réactivation ... on a conservé les parrainages passés ...
            return $this->allSponsorships;
        } elseif ($this->status == 'preprod') {
            return $this->allPreProdSponsorships;
        }
    }

    public function getAllAccountHistoryPoints() {
        if ($this->status == 'prod' or $this->status == 'standby') {
            // standby => en cas de réactivation ... on a conservé les parrainages passés ...
            return $this->allAccountHistoryPoints;
        } elseif ($this->status == 'preprod') {
            return $this->allPreProdAccountHistoryPoints;
        }
    }

    public function getAllJournals() {
        return $this->allJournals;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }
    
    public function getImage() {
        return $this->image;
    }

    public function setImage($image) {
        $this->image = $image;
    }
    
    /**
     * Generates an API Key
     */
    public function generateApiKey() {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $apikey     = '';
        for ($i = 0; $i < 64; $i++) {
            $apikey .= $characters[rand(0, strlen($characters) - 1)];
        }
        $apikey = base64_encode(sha1(uniqid('ue' . rand(rand(), rand())) . $apikey));
        $apikey = rtrim(strtr($apikey, '+/', '-_'), '=');
        $this->apiKey = $apikey;
    }
    
    public function getApiKey() {
        return $this->apiKey;
    }

    public function setDateValidate(\DateTime $dateValidate) {
        $this->dateValidate = $dateValidate;
        return $this;
    }

    public function getDateValidate() {
        return $this->dateValidate;
    }

    public function getLandingUrl() {
        return $this->landingUrl;
    }
   
    public function setLandingUrl($landingUrl) {
        $this->landingUrl = $landingUrl;
    }
    
    
    public function setSenderEmail($senderEmail) {
        $this->senderEmail = $senderEmail;
    }

    public function getSenderEmail() {
        return $this->senderEmail;
    }

    public function setSenderName($senderName) {
        $this->senderName = $senderName;
    }

    public function getSenderName() {
        return $this->senderName;
    }

    public function getOldProgram() {
        return $this->oldProgram;
    }

    public function getNewProgram() {
        return $this->newProgram;
    }

    /**
     * Easy
     */

    public function isEasy() {
        return !is_null($this->easySetting);
    }

    public function initEasy() {
        $this->easySetting = new EasySetting($this);
        $description = $this->easySetting->getDescription();
        $this->description = $description['program'];

        $this->allOperations = new ArrayCollection();

        foreach($this->easySetting->addEasyOperations() as $operation) {
            $this->allOperations[] = $operation;
        }
    }

    public function setEasySetting(EasySetting $easySetting) {
        // Utilisé pour clone ...
        $this->easySetting = $easySetting;
        $easySetting->setProgram($this);
    }

    public function getEasySetting() {
        return $this->easySetting;
    }

    public function clearEasySetting() {
        $this->easySetting = null;
    }

    public function setCollaborator(\Apr\ContractBundle\Entity\Collaborator $collaborator) {
        $this->collaborator = $collaborator;
        $this->corporate = $collaborator->getParty()->getCorporate();
    }

    public function getCollaborator() {
        return $this->collaborator;
    }

    public function getCorporate() {
        return $this->corporate;
    }

    /**
     * Abonnement
     */

    public function getDateExpiration() {
        if($this->getOldProgram()) {
            return $this->getOldProgram()->getDateExpiration();
        } else {
            return $this->dateExpiration;
        }
    }

    public function getNewDateExpiration($refreshDateExpiration = false) {
        $dateExpiration = $this->getDateExpiration();
        if(is_null($dateExpiration) or $refreshDateExpiration) $dateExpiration = Tools::DateTime();
        $newDateExpiration = new \DateTime(date("Y-m-d", mktime(0, 0, 0, (int) $dateExpiration->format("m") + 12, $dateExpiration->format("d"), $dateExpiration->format("Y"))));
        return $newDateExpiration;
    }

    public function setNewDateExpiration() {
        $refreshDateExpiration = (!is_null($this->getNewProgram()));
        $this->dateExpiration = $this->getNewDateExpiration($refreshDateExpiration);
    }

    public function isExpired() {
        return $this->getDateExpiration() < Tools::DateTime();
    }

    public function setPendingSettlementAbonnement(\Apr\MandataireBundle\Entity\Settlement $settlement) {
        $this->pendingSettlementAbonnement = $settlement;
    }

    public function confirmSettlementAbonnement() {
        $this->settlementAbonnement = $this->pendingSettlementAbonnement;
        $this->pendingSettlementAbonnement = null;
    }

    public function getPendingSettlementAbonnement() {
        return $this->pendingSettlementAbonnement;
    }

    public function getSettlementAbonnement() {
        if($this->getOldProgram()) {
            return $this->getOldProgram()->getSettlementAbonnement();
        } else {
            return $this->settlementAbonnement;
        }
    }

    /**
     * Termination
     */

    public function cancel() {
        $this->pendingSettlementAbonnement = $this->settlementAbonnement = $this->dateExpiration = $this->dateValidate = null;
        $this->status = 'cancel';
    }

    public function reactivate() {
        $this->status = 'standby';
    }

    /**
     * Set attributes before serialization
     *
     * @author Fondative <dev devteam@fondative.com>
     * @PreSerialize
     */
    public function beforeSerialization(){
        $this->isEasy = $this->isEasy();
    }



}