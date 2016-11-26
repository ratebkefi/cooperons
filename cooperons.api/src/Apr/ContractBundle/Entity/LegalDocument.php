<?php

namespace Apr\ContractBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use \Apr\MandataireBundle\Entity\Mandataire;
use Apr\Classes\Tools;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\PreSerialize;


/**
 * @ORM\Entity
 * @ORM\Table(name="legal_documents")
 */

class LegalDocument
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="created_date", type="datetime")
     * @var DateTime
     */
    private $createdDate;

    /**
     * @var Contract $contract
     * @ORM\ManyToOne(targetEntity="Contract", cascade = {"persist"})
     * @ORM\JoinColumn(name="contract_id", referencedColumnName="id")
     * @Exclude
     */
    private $contract;

    /**
     * @var Party $ownerHabilitation
     * @ORM\ManyToOne(targetEntity="Habilitation", inversedBy="allLegalDocumentsAsOwner", cascade = {"persist"})
     * @ORM\JoinColumn(name="owner_habilitation_id", referencedColumnName="id")
     */
    private $ownerHabilitation;

    /**
     * @var Party $clientHabilitation
     * @ORM\ManyToOne(targetEntity="Habilitation", inversedBy="allLegalDocumentsAsClient", cascade = {"persist"})
     * @ORM\JoinColumn(name="client_habilitation_id", referencedColumnName="id")
     */
    private $clientHabilitation;

    /**
     * @ORM\Column(name="publish_date", type="datetime", nullable = true)
     * @var DateTime
     */
    private $publishDate;

    /**
     * @ORM\Column(name="agree_date", type="datetime", nullable = true)
     * @var DateTime
     */
    private $agreeDate;

    /**
     * @ORM\Column(name="cancel_date", type="datetime", nullable = true)
     * @var DateTime
     */
    private $cancelDate;

    /**
     * @ORM\Column(name="suspension_date", type="datetime", nullable=true)
     * @var DateTime
     */
    private $suspensionDate;

    /**
     * @var LegalDocument
     *
     * @ORM\OneToOne(targetEntity="LegalDocument", inversedBy="newLegalDocument", cascade = {"persist"})
     * @ORM\JoinColumn(name="old_legal_document_id", referencedColumnName="id", nullable=true)
     */
    private $oldLegalDocument;

    /**
     * @var LegalDocument
     *
     * @ORM\OneToOne(targetEntity="LegalDocument", mappedBy="oldLegalDocument", cascade = {"persist"})
     */
    private $newLegalDocument;

    /**
     * @var string
     * @ORM\Column(name="owner_label" , type="string", length=255)
     */
    private $ownerLabel;

    /**
     * @var string
     * @ORM\Column(name="client_label" , type="string", length=255)
     */
    private $clientLabel;

    /**
     * @ORM\ManyToOne(targetEntity="\Apr\CorporateBundle\Entity\UploadedFile", cascade = {"persist"})
     * @ORM\JoinColumn(name="uploaded_file_id", referencedColumnName="id")
     * @Assert\Valid
     */
    private $document;

    // Mandataire ...

    /**
     * @var \Apr\MandataireBundle\Entity\Mandataire
     *
     * @ORM\OneToOne(targetEntity="\Apr\MandataireBundle\Entity\Mandataire", inversedBy="legalDocument", cascade = {"all"})
     * @ORM\JoinColumn(name="mandataire_id", referencedColumnName="id", nullable = true)
     * @Exclude
     */
    private $mandataire;

    // AutoEntrepreneur ...

    /**
     * @ORM\OneToMany(targetEntity="\Apr\AutoEntrepreneurBundle\Entity\ServiceType", mappedBy="legalDocument", cascade = {"all"})
     *
     */
    private $allServiceTypes;

    // Recruitment ...

    /**
     *
     * @var \Apr\AffairBundle\Entity\RecruitmentSettings
     *
     * @ORM\OneToOne(targetEntity="\Apr\AffairBundle\Entity\RecruitmentSettings", mappedBy="legalDocument", cascade = {"all"})
     * @Exclude
     */
    private $recruitmentSettings;

    public function __construct(Contract $contract, $ownerLabel = null) {
        $this->createdDate = Tools::DateTime('now');
        $this->contract = $contract;
        $contract->addLegalDocument($this);
        $this->ownerLabel = $ownerLabel;
    }

    public function __clone()
    {
        $this->createdDate = Tools::DateTime('now');
        $this->publishDate = $this->agreeDate = $this->suspensionDate = $this->agreeDate = null;
    }

    public function cloneFinish(LegalDocument $oldLegalDocument) {
        $this->oldLegalDocument = $oldLegalDocument;

        // ServiceTypes
        $oldServiceTypes = $oldLegalDocument->getAllServiceTypes();
        $this->allServiceTypes = new ArrayCollection();
        foreach($oldServiceTypes as $serviceType) {
            $newServiceType = clone $serviceType;
            $newServiceType->setLegalDocument($this);
            $this->allServiceTypes[] = $newServiceType;
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getCreatedDate() {
        return $this->createdDate;
    }

    public function getContract() {
        return $this->contract;
    }

    public function getOwner() {
        return $this->contract->getOwner();
    }

    public function getClient() {
        return $this->contract->getClient();
    }

    public function getDocument() {
        return $this->document;
    }

    public function setDocument(\Apr\CorporateBundle\Entity\UploadedFile $document) {
        $this->document = $document;
    }

    public function getOwnerLabel()
    {
        return $this->ownerLabel;
    }

    public function setOwnerLabel($ownerLabel)
    {
        $this->ownerLabel = $ownerLabel;
    }

    public function getClientLabel()
    {
        return $this->ownerLabel;
    }

    public function setClientLabel($clientLabel)
    {
        $this->clientLabel = $clientLabel;
    }

    public function setOldLegalDocument($oldLegalDocument) {
        $this->oldLegalDocument = $oldLegalDocument;
    }

    public function getOldLegalDocument() {
        return $this->oldLegalDocument;
    }

    public function clearNewLegalDocument() {
        $this->newLegalDocument = null;
    }

    public function getNewLegalDocument() {
        return $this->newLegalDocument;
    }

    /**
     * Status
     */

    public function getStatus()
    {
        if(!is_null($this->cancelDate)) {
            return 'cancel';
        } elseif(is_null($this->getOwner()) or is_null($this->getClient())) {
            return 'standby';
        } elseif(is_null($this->publishDate)) {
            return 'waitForPublish';
        } elseif(is_null($this->agreeDate)) {
            return 'waitForAgree';
        } else {
            return 'active';
        }
    }

    public function publish(Habilitation $ownerHabilitation)
    {
        $this->publishDate = Tools::DateTime('now');
        $this->ownerHabilitation = $ownerHabilitation;
    }

    public function agree(Habilitation $clientHabilitation)
    {
        $this->agreeDate = Tools::DateTime('now');
        $this->clientHabilitation = $clientHabilitation;
    }

    /**
     * Termination
     */

    public function canTerminate() {
        $canTerminate = (count($this->contract->getAllActiveRecruitmentsOfCorpContract()) == 0);
        return $canTerminate;
    }

    public function isRemovable() {
        return $this->getStatus() == 'standby';
    }

    public function terminate()
    {

    }

    public function reactivate()
    {

    }

    /**
     * Mandataire
     */

    public function setMandataire(\Apr\MandataireBundle\Entity\Mandataire $mandataire) {
        $this->mandataire = $mandataire;
        $this->contract->setMandataire($mandataire);
    }

    public function getMandataire() {
        return $this->mandataire;
    }

    public function postLiquidate() {
        $this->mandataire->clearLegalDocument();
        $this->mandataire = null;
    }

    // AutoEntrepreneur ...

    public function addServiceType(\Apr\AutoEntrepreneurBundle\Entity\ServiceType $serviceType)
    {
        $this->allServiceTypes[] = $serviceType;
        $serviceType->setLegalDocument($this);
    }

    public function clearAllServiceTypes() {
        $this->allServiceTypes = array();
    }

    public function getAllServiceTypes() {
        return $this->allServiceTypes;
    }

    // Recruitment ...

    public function setRecruitmentSettings(\Apr\AffairBundle\Entity\RecruitmentSettings $recruitmentSettings) {
        $recruitmentSettings->setLegalDocument($this);
        $this->recruitmentSettings = $recruitmentSettings;
    }

    public function clearRecruitmentSettings() {
        $recruitmentSettings = $this->recruitmentSettings;
        $recruitmentSettings->clearLegalDocument();
        $this->recruitmentSettings = null;
        return $recruitmentSettings;
    }

    public function getRecruitmentSettings() {
        return $this->recruitmentSettings;
    }
    
}