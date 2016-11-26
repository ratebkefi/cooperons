<?php

namespace Apr\ContractBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Apr\CoreBundle\Tools\Tools;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\PreSerialize;

/**
 * @ORM\Entity
 * @ORM\Table(name="collaborators")
 */

class Collaborator
{

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var CollaboratorInvitation $invitation
     * @ORM\OneToOne(targetEntity="CollaboratorInvitation", mappedBy="collaborator", cascade = {"all"})
     */
    private $invitation;


    /**
     * @var \Apr\ProgramBundle\Entity\Member $member
     * @ORM\ManyToOne(targetEntity="\Apr\ProgramBundle\Entity\Member", cascade = {"persist"})
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id", nullable = true)
     */
    private $member;

    /**
     * @var Party $party
     * @ORM\ManyToOne(targetEntity="Party", cascade = {"persist"})
     * @ORM\JoinColumn(name="party_id", referencedColumnName="id", nullable = true)
     */
    private $party;

    /**
     * @var Habilitation $habilitation
     * @ORM\ManyToOne(targetEntity="Habilitation", cascade = {"persist"})
     * @ORM\JoinColumn(name="habilitation_id", referencedColumnName="id", nullable = true)
     */
    private $habilitation;

    /**
     * @ORM\Column(name="created_date", type="datetime")
     * @var \DateTime
     */
    private $createdDate;

    /**
     * @var ArrayCollection $allContractsAsOwner
     *
     * @ORM\OneToMany(targetEntity="\Apr\ContractBundle\Entity\Contract", mappedBy="ownerCollaborator", cascade = {"persist"})
     * @Exclude
     */
    private $allContractsAsOwner;

    /**
     * @var ArrayCollection $allContractsAsClient
     *
     * @ORM\OneToMany(targetEntity="\Apr\ContractBundle\Entity\Contract", mappedBy="clientCollaborator", cascade = {"persist"})
     * @Exclude
     */
    private $allContractsAsClient;

    /**
     * @var ArrayCollection $allHabilitations
     *
     * @ORM\OneToMany(targetEntity="Habilitation", mappedBy="collaborator", cascade = {"persist"})
     * @Exclude
     */
    private $allHabilitations;

    /**
     * @author Fondative <devteam@fondative.com>
     * @var boolean
     */
    private $isAdministrator;

    public function __construct($party, $member = null, $invitation = null) {
        $this->createdDate = Tools::DateTime('now');
        $this->member = $member;
        $this->party = $party;
        $this->invitation = $invitation;
    }

    public function getId(){
        return $this->id;
    }

    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    public function getMember()
    {
        return $this->member;
    }

    public function getParty()
    {
        return $this->party;
    }

    public function getCorporate()
    {
        return $this->party->getCorporate();
    }

    public function getInvitation()
    {
        return $this->invitation;
    }

    public function getAllContractsAsClient()
    {
        return $this->allContractsAsClient;
    }

    public function getAllContracts()
    {
        return new ArrayCollection(array_merge((array) $this->allContractsAsOwner->toArray(), (array) $this->allContractsAsClient->toArray()));
    }

    public function setHabilitation(Habilitation $habilitation)
    {
        $this->habilitation = $habilitation;
    }

    public function getHabilitation()
    {
        return $this->habilitation;
    }

    public function getAllHabilitations()
    {
        return $this->allHabilitations;
    }

    public function setInvitation($invitation) {
        // Mise à jour des 2 cotés de la relation nécessaire pour Doctrine ...
        if(!is_null($invitation)) {
            $invitation->setCollaborator($this);
        } elseif($this->invitation) {
            $this->invitation->setCollaborator(null);
        }
        $this->invitation = $invitation;
    }

    public function confirm($member) {
        $this->member = $member;
        $this->setInvitation(null);
    }
    
    public function isAdministrator() {
        return $this == $this->party->getAdministrator();
    }

    /**
     * $newCollaborator ne doit pas être null ...
     */
    public function transfer(Collaborator $newCollaborator) {
        if($this == $newCollaborator) return null;

        if($this->isAdministrator()) {
            $this->party->setAdministrator($newCollaborator);
        }

        return $newCollaborator;
    }

    /**
     * @author Fondative <devteam@fondative.com>
     * @PreSerialize
     */
    public function beforeSerialization(){
        $this->isAdministrator = $this->isAdministrator();
    }
}