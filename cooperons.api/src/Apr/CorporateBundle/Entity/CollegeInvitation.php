<?php

namespace Apr\CorporateBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Apr\UserBundle\Entity\InvitationBase;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\PreSerialize;

/**
 * @ORM\Entity(repositoryClass="Apr\UserBundle\Repository\InvitationRepository")
 * 
 */

class CollegeInvitation extends InvitationBase
{
    /**
     * @var \Apr\UserBundle\Entity\Token $tokenObject
     * @ORM\OneToOne(targetEntity="\Apr\UserBundle\Entity\Token", inversedBy="collegeInvitation", cascade = {"all"})
     * @ORM\JoinColumn(name="token", referencedColumnName="value")
     * @Exclude
     */
    protected $tokenObject;

    /**
     * @var College $college
     * @ORM\OneToOne(targetEntity="College", inversedBy="invitation", cascade = {"all"})
     * @ORM\JoinColumn(name="college_id", referencedColumnName="id", nullable = true)
     * @Exclude
     */
    private $college = null;

    public function __construct(College $college, $firstName, $lastName, $email)
    {
        $this->college = $college;
        parent::__construct($firstName, $lastName, $email);
    }

    public function getType() {
        return "CollegeInvitation";
    }

    public function setCollege($college) {
        $this->college = $college;
    }
    public function getCollege() {
        return $this->college;
    }

    public function getSponsorMember()
    {
        return $this->college->getMember();
    }
}