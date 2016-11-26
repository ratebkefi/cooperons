<?php

namespace Apr\ProgramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use FOS\UserBundle\Util\TokenGenerator;
use Apr\CoreBundle\Tools\Tools;
use \Apr\UserBundle\Entity\Token;
use JMS\Serializer\Annotation\PreSerialize;

/**
 * @ORM\Table(name="participates_to")
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"prod" = "ParticipatesTo", "preprod" = "PreProdParticipatesTo"})
 */

abstract class ParticipatesToBase
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
    protected $createdDate;

    /**
     * Fondative <dev devteam@fondative.com> : moved from ParticipatesTo to be serialized when returning response
     * @ORM\ManyToOne(targetEntity="Program", inversedBy="allParticipatesTo", cascade = {"persist"})
     * @ORM\JoinColumn(name="program_id", referencedColumnName="id")
     */
    protected $program;
    
    /**
     * @ORM\Column(name="member_program_id", type="integer")
     *
     */
    private $memberProgramId;

    /**
     * @ORM\Column(name="total_points", type="integer")
     *
     */
    private $totalPoints = 0;

    /**
     * @ORM\Column(name="total_multipoints", type="integer")
     *
     */
    private $totalMultiPoints = 0;

    /**
     * @ORM\Column(name="count_affiliates", type="integer")
     *
     */
    private $countAffiliates = 0;
    
    /**
     * @author Fondative <dev devteam@fondative.com>
     * @var boolean
     */
    private $hasParrain;

    /**
     * @author Fondative <dev devteam@fondative.com>
     * @var boolean
     */
    private $hasMailInvitation;

    /**
     * @var String
     */
    private $token;

    public function __construct(Program $program, $memberProgramId)
    {
        $this->createdDate = Tools::DateTime();
        $this->program = $program;
        $this->memberProgramId = $memberProgramId;
        $this->tokenObject = new Token();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setMember(Member $member)
    {
        $this->member = $member;
    }

    /**
     * @return Member
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * @return Program
     */
    public function getProgram()
    {
        return $this->program;
    }
    
    public function getMemberProgramId()
    {
        return $this->memberProgramId;
    }

    public function getToken()
    {
        return $this->tokenObject->getValue();
    }

    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Get MailInvitations
     *
     * @return MailInvitation
     */
    public function getMailInvitation()
    {
        return $this->mailInvitation;
    }

    /**
     * Set MailInvitation
     */
    public function setMailInvitation($mailInvitation)
    {
        $this->mailInvitation = $mailInvitation;
    }
    
    public function setSponsorship($sponsorship)
    {
        $this->sponsorship = $sponsorship;
    }
    
    /**
     * @return Sponsorship
     */
    public function getSponsorship()
    {
        return $this->sponsorship;
    }

    public function getAllAccountPointsHistory()
    {
        return $this->allAccountPointsHistory;
    }


    /**
     * @return ParticipatesTo
     */
    public function getSponsor()
    {
        return ($this->sponsorship)? $this->sponsorship->getSponsor():null;
    }

    public function getUpline()
    {
        return ($this->sponsorship)? $this->sponsorship->getUpline():array();
    }

    /**
     * @return ParticipatesTo
     */
    public function getKing()
    {
        // Si pas de sponsorship - c'est moi le King !
        return ($this->sponsorship)? $this->sponsorship->getKing():$this;
    }

    public function  incrementCountAffiliates() {
        $this->countAffiliates ++;
    }

    public function  getAllAffiliates() {
        $allSponsorships = $this->allSponsorshipsAsSponsor;
        $allAffiliates = array();
        foreach($allSponsorships as $sponsorship) {
            array_push($allAffiliates, $sponsorship->getAffiliate());
        }
        return $allAffiliates;
    }

    public function  getCountAffiliates() {
        return $this->countAffiliates;
    }

    public function addPoints($amount, $isMulti = false)
    {
        $this->totalPoints += $amount;
        if($isMulti) $this->totalMultiPoints += $amount;
        $this->member->addPoints($amount);
        return $this->getTotal();
    }

    public function getTotal()
    {
        return array('points' => $this->totalPoints, 'multiPoints' => $this->totalMultiPoints);
    }

    // Easy

    public function getAllAffairs()
    {
        return $this->allAffairs;
    }

    /**
     * @return mixed
     */
    public function getTotalPoints()
    {
        return $this->totalPoints;
    }

    /**
     * @param mixed $totalPoints
     */
    public function setTotalPoints($totalPoints)
    {
        $this->totalPoints = $totalPoints;
    }

    /**
     * @return mixed
     */
    public function getTotalMultiPoints()
    {
        return $this->totalMultiPoints;
    }

    /**
     * @param mixed $totalMultiPoints
     */
    public function setTotalMultiPoints($totalMultiPoints)
    {
        $this->totalMultiPoints = $totalMultiPoints;
    }

    /**
     * @return boolean
     */
    public function hasParrain()
    {
        return $this->getSponsor() ? true : false;
    }
    
     /**
     * Set attributes before serialization
     *
     * @author Fondative <dev devteam@fondative.com>
     * @PreSerialize
     */
    public function beforeSerialization(){
        $this->hasParrain = $this->hasParrain();
        $this->hasMailInvitation = $this->mailInvitation !== null;
        $this->token = $this->getToken();
    }

}
