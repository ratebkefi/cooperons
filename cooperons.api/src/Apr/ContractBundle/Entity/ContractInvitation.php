<?php

namespace Apr\ContractBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Apr\UserBundle\Entity\InvitationBase;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\PreSerialize;

/**
 * @ORM\Entity(repositoryClass="Apr\UserBundle\Repository\InvitationRepository")
 */

class ContractInvitation extends InvitationBase
{
    /**
     * @var \Apr\UserBundle\Entity\Token $tokenObject
     * @ORM\OneToOne(targetEntity="\Apr\UserBundle\Entity\Token", inversedBy="contractInvitation", cascade = {"all"})
     * @ORM\JoinColumn(name="token", referencedColumnName="value")
     * @Exclude
     */
    protected $tokenObject;

    /**
     * @ORM\Column(name="infos", type="string", length=255, nullable=true)
     */
    private $infos;

    /**
     * @var Collaborator $collaborator
     * @ORM\OneToOne(targetEntity="Collaborator", cascade = {"persist"})
     * @ORM\JoinColumn(name="collaborator_id", referencedColumnName="id", nullable = true)
     * @Exclude
     */
    private $collaborator = null;

    /**
     * @var Contract $contract
     * @ORM\OneToOne(targetEntity="Contract", inversedBy="invitation", cascade = {"all"})
     * @ORM\JoinColumn(name="contract_id", referencedColumnName="id", nullable = true)
     * @Exclude
     */
    private $contract = null;

    public function __construct(Collaborator $collaborator, $typeInvitation, $firstName, $lastName, $email)
    {
        $arr = explode(":", $typeInvitation);
        $isCreatedByOwner = ($arr[1] == 'owner');
        $this->contract = new Contract(null, null, $isCreatedByOwner, $arr[0]);

        $this->collaborator = $collaborator;
        $this->infos = $typeInvitation;

        parent::__construct($firstName, $lastName, $email);
    }

    public function getType() {
        return "ContractInvitation";
    }

    public function getSponsorMember() {
        return $this->collaborator->getMember();
    }

    public function getInfos() {
        return $this->infos;
    }

    public function setCollaborator($collaborator) {
        $this->collaborator = $collaborator;
    }
    public function getCollaborator() {
        return $this->collaborator;
    }

    public function setContract($contract = null, $typeInvitation = null) {
        $this->contract = $contract;
        $this->infos = $typeInvitation;
    }
    
    public function getContract() {
        return $this->contract;
    }
}