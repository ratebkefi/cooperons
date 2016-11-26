<?php

namespace Apr\ContractBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Apr\CoreBundle\Tools\Tools;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\PreSerialize;


/**
 * @ORM\Entity(repositoryClass="Apr\ContractBundle\Repository\ContractRepository")
 * @ORM\Table(name="contracts")
 */

class Contract
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     * @ORM\Column(name="type", type="integer")
     */
    private $type = 0;

    // A copier dans Manager & Repository
    /**
     * @Exclude
     */
    private $arrTypes = array(
        0 => 'default',
        1 => 'affair'
    );

    /**
     * @ORM\Column(name="created_date", type="datetime")
     * @var DateTime
     */
    private $createdDate;

    /**
     * @var Party $client
     * @ORM\ManyToOne(targetEntity="Party", cascade = {"persist"})
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     * @Exclude
     */
    private $client;

    /**
     * @var Party $owner
     * @ORM\ManyToOne(targetEntity="Party", cascade = {"persist"})
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     * @Exclude
     */
    private $owner;

    /**
     * @var \Apr\MandataireBundle\Entity\Mandataire
     *
     * @ORM\OneToOne(targetEntity="\Apr\MandataireBundle\Entity\Mandataire", inversedBy="contract", cascade = {"all"})
     * @ORM\JoinColumn(name="mandataire_id", referencedColumnName="id", nullable = true)
     * @Exclude
     */
    private $mandataire;

    /**
     * @var Collaborator $ownerCollaborator
     *
     * @ORM\ManyToOne(targetEntity="Collaborator", inversedBy="allContractsAsOwner", cascade = {"persist"})
     * @ORM\JoinColumn(name="owner_collaborator_id", referencedColumnName="id", nullable = true)
     * @Exclude
     */
    private $ownerCollaborator;

    /**
     * @var Collaborator $clientCollaborator
     *
     * @ORM\ManyToOne(targetEntity="Collaborator", inversedBy="allContractsAsClient", cascade = {"persist"})
     * @ORM\JoinColumn(name="client_collaborator_id", referencedColumnName="id", nullable = true)
     * @Exclude
     */
    private $clientCollaborator;

    /**
     * @ORM\Column(name="is_created_by_owner", type="boolean")
     * @var boolean
     */
    private $isCreatedByOwner = true;

    /**
     * @var \Apr\ContractBundle\Entity\ContractInvitation $invitation
     * @ORM\OneToOne(targetEntity="\Apr\ContractBundle\Entity\ContractInvitation", mappedBy="contract", cascade = {"all"})
     */
    private $invitation = null;

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
     * @var ArrayCollection $allLegalDocuments
     *
     * @ORM\OneToMany(targetEntity="LegalDocument", mappedBy="contract")
     * @Exclude
     */
    private $allLegalDocuments;

    // Recruitment ...

    /**
     * @var \Apr\AffairBundle\Entity\Recruitment
     *
     * @ORM\OneToOne(targetEntity="\Apr\AffairBundle\Entity\Recruitment", mappedBy="recruiteeCorpContract", cascade = {"persist"})
     * @Exclude
     */
    private $recruitment;

    /**
     * @ORM\OneToMany(targetEntity="\Apr\AffairBundle\Entity\Recruitment", mappedBy="recruiterCorpContract", cascade = {"persist"})
     * @Exclude
     */
    private $allRecruitmentsOfCorpContract;
    
    private $ownerLabel;
    private $clientMember;
    private $ownerMember;
    private $allActiveRecruitments;
    private $clientLabel;
    private $status;
    private $depot;
    private $minDepot;
    private $isRemovable;
    private $clientCollaboratorId;
    private $ownerCollaboratorId;

    public function __construct($owner = null, $client = null, $isCreatedByOwner = true, $type = 'default') {
        $this->createdDate = Tools::DateTime('now');
        $this->type = array_search($type, $this->arrTypes);
        $this->isCreatedByOwner = $isCreatedByOwner;
        $this->owner = $owner;
        $this->client = $client;

        if(!is_null($owner) && !is_null($client)) {
            if($owner->getType() == 'autoEntrepreneur' && $client->getType() == 'autoEntrepreneur') {
                $this->recruitmentSettings = new \Apr\AffairBundle\Entity\RecruitmentSettings($this);
            }
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getCreatedDate() {
        return $this->createdDate;
    }

    public function getOwner() {
        return $this->owner;
    }

    public function getClient() {
        return $this->client;
    }

    public function isCreatedByOwner() {
        return $this->isCreatedByOwner;
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

    public function getAllLegalDocuments() {
        return $this->allLegalDocuments;
    }

    public function addLegalDocument(LegalDocument $legalDocument) {
        $this->allLegalDocuments[] = $legalDocument;
    }

    /**
     * Mandataire
     */

    public function setMandataire(\Apr\MandataireBundle\Entity\Mandataire $mandataire) {
        $this->mandataire = $mandataire;
    }

    public function getMandataire() {
        return $this->mandataire;
    }

    public function getDepot() {
        return $this->mandataire?$this->mandataire->getDepot():null;
    }

    public function getMinDepot() {
        return $this->mandataire?$this->mandataire->getMinDepot():null;
    }

    /**
     * Invitation
     */

    /**
     * @return ContractInvitation
     */
    public function getInvitation() {
        return $this->invitation;
    }

    public function setInvitation($invitation, $typeInvitation) {
        // Mise à jour des 2 cotés de la relation nécessaire pour Doctrine ...
        if(!is_null($invitation)) {
            $invitation->setContract($this, $typeInvitation);
        } elseif($this->invitation) {
            $this->invitation->setContract();
        }
        $this->invitation = $invitation;
    }

    public function clearInvitation() {
        if($this->invitation) {
            // Mise à jour des 2 cotés de la relation nécessaire pour Doctrine ...
            $this->invitation->setContract();
            $this->invitation = null;
        }
    }

    /**
     * Collaborator
     */

    public function getOwnerCollaborator() {
        return $this->ownerCollaborator;
    }

    public function getClientCollaborator() {
        return $this->clientCollaborator;
    }

    public function setOwnerCollaborator($collaborator) {
        $test = true;
        if(!is_null($collaborator)) {
            if($test && $this->invitation) {
                $test = ($collaborator == $this->invitation->getCollaborator());
            }
            if($test) $test = ($collaborator->getParty() == $this->owner);
        }
        if($test) $this->ownerCollaborator = $collaborator;
    }

    public function setClientCollaborator($collaborator) {
        $test = true;
        if(!is_null($collaborator)) {
            if($test && $this->invitation) {
                $test = ($collaborator == $this->invitation->getCollaborator());
            }
            if($test) $test = ($collaborator->getParty() == $this->client);
        }
        if($test) $this->clientCollaborator = $collaborator;
    }

    public function getAuthorizedParty($member) {
        if (is_null($member)) {
            return null;
        } elseif ($this->invitation) {
            return $this->invitation->getCollaborator()->getMember() == $member;
        } elseif ($this->clientCollaborator->getMember() == $member) {
            return $this->getClient();
        } elseif($this->ownerCollaborator->getMember() == $member) {
            return $this->getOwner();
        } else {
            return null;
        }
    }

    public function getCollaboratorSide(Collaborator $collaborator) {
        // Par défaut, pour contrats ou clientCollaborator même Corporate que ownerCollaborator (cf. contracts Programs Coopérons ...) => return 'client'
        $party = $collaborator->getParty();
        $side = null;
        if(!is_null($this->clientCollaborator) && $this->clientCollaborator->getParty() == $party) {
            $side = "client";
        } elseif(!is_null($this->ownerCollaborator) && $this->ownerCollaborator->getParty() == $party) {
            $side = "owner";
        }
        return $side;
    }

    public function getRelevantCollaborator(Party $party) {
        $side = $this->getSide($party);
        if (!is_null($side)) {
            $property = $side."Collaborator";
            return $this->$property;
        }
    }

    public function getOwnerMember(){
        if(!is_null($this->getOwner())) {
            return $this->getOwner()->getMember($this->ownerCollaborator);
        }
    }

    public function getClientMember(){
        if(!is_null($this->getClient())) {
            return $this->getClient()->getMember($this->clientCollaborator);
        }
    }

    public function transfer($collaborator, $beforeCancelCorporate = null){
        $side = is_null($beforeCancelCorporate)?$this->getCollaboratorSide($collaborator):$this->getCollaboratorSide($beforeCancelCorporate->getParty()->getAdministrator());
        if(!is_null($side)) {
            $property = $side."Collaborator";
            $this->$property = $collaborator;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Type
     */

    public function getType() {
        return $this->arrTypes[$this->type];
    }
    
    public function getFilter(Party $myParty)
    {
        $side = $this->getSide($myParty);
        if($side) {
            return $this->getType().':'.$side;
        } else {
            return null;
        }
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
        } elseif(!count($this->allLegalDocuments)) {
            return 'empty';
        } else {
            return 'active';
        }
    }
    
    public function isActive() {
        return $this->getStatus() == 'active';
    }

    public function isCancel() {
        return $this->getStatus() == 'cancel';
    }

    /**
     * Termination
     */

    public function isRemovable() {
        foreach ($this->allLegalDocuments as $legalDocument) {
            if(!$legalDocument->isRemovable()) return false;
        }
        return is_null($this->recruitment) && (is_null($this->mandataire) or $this->mandataire->isRemovable());
    }

    public function terminate()
    {
        $status = $this->getStatus();
        if($status == 'standby') {
            $this->clearInvitation();
        } else {
            // Recruitment
            if($this->recruitment && in_array($status, array('waitForPublish', 'waitForAgree'))) {
                $this->recruitment->setRecruiteeCorpContract(null);
                $this->recruitment = null;
            }
            $this->cancelDate = Tools::DateTime('now');
        }
    }

    public function reactivate()
    {
        if($this->getStatus() == 'cancel') {
            $this->cancelDate = null;
            $this->mandataire->reactivate();
        }
    }

    public function beforeCancelCorporate($corporate) {
        $this->transfer(null, $corporate);
    }

    /**
     * Suspension
     */

    public function isSuspendable() {
        /*
         * Vérifier ici si on est obligé de suspendre un programme API ? (return false...)
         */
        return true;
    }

    public function suspend() {
        $this->suspensionDate = Tools::DateTime('now');
    }

    public function resume() {
        $this->suspensionDate = null;
    }

    public function getSuspensionDate() {
        return $this->suspensionDate;
    }


    // Recruitment ...

    public function getRecruitmentSettings() {
        return ($this->type == 'affair' && count($this->allLegalDocuments)) ? $this->allLegalDocuments[0]->getRecruitmentSettings():null;
    }

    public function getRecruitment() {
        return $this->recruitment;
    }

    public function getAllActiveRecruitmentsOfCorpContract() {
        $allRecruitments = (array) $this->allRecruitmentsOfCorpContract->toArray();
        foreach($allRecruitments as $key => $recruitment) {
            if($recruitment->isExpired()) unset($allRecruitments[$key]);
        }
        return new ArrayCollection($allRecruitments);
    }

    public function getAllActiveRecruitments() {
        if($this->getStatus() != 'standby') {
            if($this->getOwner()->getAutoEntrepreneur()) {
                if($this->recruitmentSettings) {
                    return $this->recruitmentSettings->getAllActiveRecruitments();
                } elseif($this->getClient()->getCorporate()) {
                    return $this->getAllActiveRecruitmentsOfCorpContract();
                } else {
                    return array();
                }
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    /**
     * Set attributes before serialization
     *
     * @author Fondative <dev devteam@fondative.com>
     * @PreSerialize
     */
    public function beforeSerialization(){
        $this->status = $this->getStatus();
        $this->depot = $this->getDepot();
        $this->minDepot = $this->getMinDepot();
        $this->isRemovable = $this->isRemovable();
        $this->clientCollaboratorId =$this->getClientCollaborator()? $this->getClientCollaborator()->getId():null;
        $this->ownerCollaboratorId = $this->getOwnerCollaborator()?$this->getOwnerCollaborator()->getId():null;
        $this->ownerLabel = $this->getOwner() ? $this->getOwner()->getLabel() : null;
        $this->clientLabel = $this->getClient() ? $this->getClient()->getLabel() : null;
        $this->clientMember = $this->getClientMember();
        $this->ownerMember = $this->getOwnerMember();
        $this->allActiveRecruitments = $this->getAllActiveRecruitments();
    }
}