<?php

namespace Apr\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\PreSerialize;

/**
 * @ORM\Entity
 * @ORM\Table(name="tokens")
 */
class Token
{

    /**
     * @ORM\Column(type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $value;

    /**
     * @var \Apr\ProgramBundle\Entity\Invitation
     *
     * @ORM\OneToOne(targetEntity="\Apr\ProgramBundle\Entity\Invitation", mappedBy="tokenObject", cascade = {"persist"})
     * @Exclude
     */
    private $programInvitation = null;

    /**
     * @var \Apr\ProgramBundle\Entity\ParticipatesTo
     *
     * @ORM\OneToOne(targetEntity="\Apr\ProgramBundle\Entity\ParticipatesTo", mappedBy="tokenObject", cascade = {"persist"})
     * @Exclude
     */
    private $participatesTo = null;

    /**
     * @var \Apr\ProgramBundle\Entity\PreProdParticipatesTo
     *
     * @ORM\OneToOne(targetEntity="\Apr\ProgramBundle\Entity\PreProdParticipatesTo", mappedBy="tokenObject", cascade = {"persist"})
     * @Exclude
     */
    private $preProdParticipatesTo = null;

    /**
     * @var \Apr\ProgramBundle\Entity\PreProdInvitation
     *
     * @ORM\OneToOne(targetEntity="\Apr\ProgramBundle\Entity\PreProdInvitation", mappedBy="tokenObject", cascade = {"persist"})
     * @Exclude
     */
    private $programPreProdInvitation = null;

    /**
     * @var \Apr\CorporateBundle\Entity\CollegeInvitation
     *
     * @ORM\OneToOne(targetEntity="\Apr\CorporateBundle\Entity\CollegeInvitation", mappedBy="tokenObject", cascade = {"persist"})
     * @Exclude
     */
    private $collegeInvitation = null;

    /**
     * @var \Apr\ContractBundle\Entity\CollaboratorInvitation
     *
     * @ORM\OneToOne(targetEntity="\Apr\ContractBundle\Entity\CollaboratorInvitation", mappedBy="tokenObject", cascade = {"persist"})
     * @Exclude
     */
    private $collaboratorInvitation = null;

    /**
     * @var \Apr\ContractBundle\Entity\ContractInvitation
     *
     * @ORM\OneToOne(targetEntity="\Apr\ContractBundle\Entity\ContractInvitation", mappedBy="tokenObject", cascade = {"persist"})
     * @Exclude
     */
    private $contractInvitation = null;

    private $type;
    private $object;
    private $isInvitation;
    private $isParticipatesTo;
    private $isPreProd;
    private $hasProgram;

    /**
     * Get value
     *
     * @return string
     */
    
    public function getValue()
    {
        return $this->value;
    }

    public function getType() {
        if($this->programInvitation) return 'programInvitation';
        if($this->programPreProdInvitation) return 'programPreProdInvitation';
        if($this->participatesTo) return 'participatesTo';
        if($this->preProdParticipatesTo) return 'preProdParticipatesTo';
        if($this->collegeInvitation) return 'collegeInvitation';
        if($this->collaboratorInvitation) return 'collaboratorInvitation';
        if($this->contractInvitation) return 'contractInvitation';
    }

    public function isInvitation() {
        return strpos($this->getType(), 'Invitation') != 0;
    }

    public function isParticipatesTo() {
        return in_array($this->getType(), array('participatesTo', 'preProdParticipatesTo'));
    }

    public function isPreProd() {
        return strpos($this->getType(), 'PreProd') != 0;
    }

    public function hasProgram() {
        return $this->isParticipatesTo() or (strpos($this->getType(), 'program') != 0);
    }

    public function getObject() {
        $type = $this->getType();
        return $this->$type;
    }

    /**
     * Set attributes before serialization
     * @PreSerialize
     */
    public function beforeSerialization(){
        $this->type = $this->getType();
        $this->object = $this->getObject();
        $this->isInvitation = $this->isInvitation();
        $this->isParticipatesTo = $this->isParticipatesTo();
        $this->isPreProd = $this->isPreProd(); 
        $this->hasProgram = $this->hasProgram();
    }
}