<?php

namespace Apr\ContractBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Apr\UserBundle\Entity\InvitationBase;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\PreSerialize;

/**
 * @ORM\Entity(repositoryClass="Apr\UserBundle\Repository\InvitationRepository")
 */

class CollaboratorInvitation extends InvitationBase
{
    /**
     * @var \Apr\UserBundle\Entity\Token $tokenObject
     * @ORM\OneToOne(targetEntity="\Apr\UserBundle\Entity\Token", inversedBy="collaboratorInvitation", cascade = {"all"})
     * @ORM\JoinColumn(name="token", referencedColumnName="value")
     * @Exclude
     */
    protected $tokenObject;

    /**
     * @var Collaborator $collaborator
     * @ORM\OneToOne(targetEntity="Collaborator", inversedBy="invitation", cascade = {"all"})
     * @ORM\JoinColumn(name="collaborator_id", referencedColumnName="id", nullable = true)
     * @Exclude
     */
    private $collaborator = null;

    public function __construct(Collaborator $collaborator, $firstName, $lastName, $email)
    {
        $this->collaborator = $collaborator;
        parent::__construct($firstName, $lastName, $email);
    }

    public function getType() {
        return "CollaboratorInvitation";
    }

    public function setCollaborator($collaborator) {
        $this->collaborator = $collaborator;
    }
    
    public function getCollaborator() {
        return $this->collaborator;
    }

    public function getSponsorMember() {
        return $this->collaborator->getParty()->getAdministrator()->getMember();
    }

}