<?php

namespace Apr\ContractBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Apr\Classes\Tools;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\PreSerialize;

/**
 * @ORM\Entity
 * @ORM\Table(name="habilitations")
 */

class Habilitation
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
     * @ORM\Column(name="confirm_date", type="datetime")
     * @var DateTime
     */
    private $confirmDate;

    /**
     * @var ArrayCollection $allLegalDocumentsAsOwner
     *
     * @ORM\OneToMany(targetEntity="LegalDocument", mappedBy="ownerHabilitation", cascade = {"persist"})
     * @Exclude
     */
    private $allLegalDocumentsAsOwner;

    /**
     * @var ArrayCollection $allLegalDocumentsAsClient
     *
     * @ORM\OneToMany(targetEntity="LegalDocument", mappedBy="clientHabilitation", cascade = {"persist"})
     * @Exclude
     */
    private $allLegalDocumentsAsClient;
    
    /**
     * @var Collaborator $collaborator
     * @ORM\ManyToOne(targetEntity="Collaborator", inversedBy="allHabilitations", cascade = {"persist"})
     * @ORM\JoinColumn(name="collaborator_id", referencedColumnName="id")
     * @Exclude
     */
    private $collaborator ;

    /**
     * @var Habilitation
     *
     * @ORM\OneToOne(targetEntity="Habilitation", inversedBy="newHabilitation", cascade = {"persist"})
     * @ORM\JoinColumn(name="old_habilitation_id", referencedColumnName="id", nullable=true)
     */
    private $oldHabilitation;

    /**
     * @var Habilitation
     *
     * @ORM\OneToOne(targetEntity="Habilitation", mappedBy="oldHabilitation", cascade = {"persist"})
     */
    private $newHabilitation;

    public function __construct(Collaborator $collaborator) {
        $this->createdDate = Tools::DateTime('now');
        $this->collaborator = $collaborator;
    }
    
    public function getId(){
        return $this->id;
    }

    public function getCollaborator(){
        return $this->collaborator;
    }

    public function setOldHabilitation($oldHabilitation) {
        $this->oldHabilitation = $oldHabilitation;
    }

    public function getOldHabilitation() {
        return $this->oldHabilitation;
    }

    public function clearNewHabilitation() {
        $this->newHabilitation = null;
    }

    public function getNewHabilitation() {
        return $this->newHabilitation;
    }

    public function confirm() {
        $this->confirmDate =Tools::DateTime('now');
        $this->collaborator->setHabilitation($this);
    }
    
    public function isActive(){
        return !is_null($this->newHabilitation);
    }

    public function getAllLegalDocuments()
    {
        return new ArrayCollection(array_merge((array) $this->allLegalDocumentsAsOwner->toArray(), (array) $this->allLegalDocumentsAsClient->toArray()));
    }
}