<?php

namespace Apr\ProgramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Apr\UserBundle\Entity\InvitationBase;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\PreSerialize;

/**
 * @ORM\Entity(repositoryClass="Apr\UserBundle\Repository\InvitationRepository")
 */

class Invitation extends InvitationBase
{
    /**
     * @var \Apr\UserBundle\Entity\Token $tokenObject
     * @ORM\OneToOne(targetEntity="\Apr\UserBundle\Entity\Token", inversedBy="programInvitation", cascade = {"all"})
     * @ORM\JoinColumn(name="token", referencedColumnName="value")
     * @Exclude
     */
    protected $tokenObject;

    /**
     * @var Program $program
     * @ORM\ManyToOne(targetEntity="Program", inversedBy="invitations")
     * @ORM\JoinColumn(name="program_id", referencedColumnName="id")
     * @Exclude
     */
    protected $program;

    /**
     * @var Member $member
     *
     * @ORM\ManyToOne(targetEntity="ParticipatesTo", inversedBy = "allInvitations")
     * @ORM\JoinColumn(name="sponsor_id", referencedColumnName="id")
     * @Exclude
     */
    protected $sponsor;

    public function __construct(ParticipatesTo $sponsor, $firstName, $lastName, $email)
    {
        $this->program = $sponsor->getProgram();
        $this->sponsor = $sponsor;
        parent::__construct($firstName, $lastName, $email);
    }

    public function getType() {
        return "Invitation";
    }

    public function getProgram()
    {
        return $this->program;
    }

    public function getSponsor()
    {
        return $this->sponsor;
    }

    public function getSponsorMember()
    {
        return $this->sponsor->getMember();
    }
}