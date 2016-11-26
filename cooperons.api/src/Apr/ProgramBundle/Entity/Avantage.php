<?php

namespace Apr\ProgramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Apr\CoreBundle\Tools\Tools;

/**
 * @ORM\Entity(repositoryClass="Apr\ProgramBundle\Repository\AvantageRepository")
 * @ORM\Table(name="avantages")
 */
class Avantage
{

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Member", inversedBy="allAvantages")
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id")
     */
    private $member;

    /**
     * @ORM\Column(name="amount", type="float")
     * 
     */
    private $amount;

    /**
     * @ORM\Column(name="description", type="string", length=255)
     * 
     */
    private $description;

    /**
     * @ORM\Column(name="created_date", type="datetime", nullable = true)
     * @var DateTime
     */
    private $createdDate;

    /**
     * @ORM\ManyToOne(targetEntity="GiftOrder", inversedBy="allAvantages")
     * @ORM\JoinColumn(name="gift_order_id", referencedColumnName="id", nullable=true)
     */
    private $giftOrder;

    /**
     * @ORM\Column(name="cumulated_total" , type="float")
     */
    private $cumulatedTotal = 0;

    /**
     * @ORM\Column(name="cumulated_year" , type="float")
     */
    private $cumulatedYear = 0;

    /**
     * @ORM\ManyToOne(targetEntity="\Apr\CorporateBundle\Entity\Corporate")
     * @ORM\JoinColumn(name="corporate_id", referencedColumnName="id", nullable=true)
     */
    private $corporate;

    /**
     * @ORM\ManyToOne(targetEntity="\Apr\CorporateBundle\Entity\Attestation", cascade = {"persist"})
     * @ORM\JoinColumn(name="attestation_id", referencedColumnName="id", nullable=true)
     */
    private $attestation;

    /**
     * @ORM\OneToOne(targetEntity="\Apr\MandataireBundle\Entity\Settlement", cascade = {"persist"}, inversedBy="avantage")
     * @ORM\JoinColumn(name="settlement_id", referencedColumnName="id", nullable=true)
     */
    private $settlement;

    public function __construct(Member $member, $amount, $description, $settlement = null)
    {
        $this->createdDate = Tools::DateTime();
        $this->member = $member;
        $this->description = $description;

        $this->amount = min($amount, $member->getValuePoints());

        if(!$member->isAutoEntrepreneur()) {
            if(!is_null($member->getCollege())) $this->corporate = $member->getCollege()->getCorporate();
        } else {
            $this->settlement = $settlement;
            $this->confirm();
        }

    }

    public function confirm()
    {
        $lastAvantage = $this->member->getLastAvantage();

        $lastCumulatedTotal = $lastCumulatedYear = $lastYear = 0;

        if(!is_null($lastAvantage)) {
            $lastCumulatedTotal = $lastAvantage->cumulatedTotal;
            $lastYear = $lastAvantage->cumulatedYear;
        }

        $this->cumulatedTotal = $lastCumulatedTotal + $this->amount;
        if($this->getYear() == $lastYear) {
            $this->cumulatedYear = $lastCumulatedYear + $this->amount;
        } else {
            $this->cumulatedYear = $this->amount;
        }

        $this->member->setLastAvantage($this);

    }

    public function getId()
    {
        return $this->id;
    }

    public function getQuarter()
    {
        if($this->giftOrder) {
            return $this->giftOrder->getQuarter();
        } else {
            return Tools::getQuarter($this->createdDate);
        }
    }

    public function getYear()
    {
        if($this->giftOrder) {
            return $this->giftOrder->getYear();
        } else {
            return $this->createdDate->format('Y');
        }
    }

    public function getMember()
    {
        return $this->member;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getCumulatedTotal()
    {
        return $this->cumulatedTotal;
    }

    public function getCumulatedYear()
    {
        return $this->cumulatedYear;
    }

    public function getType()
    {
        return is_null($this->settlement)?'gift':'credit';
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getGiftOrder()
    {
        return $this->giftOrder;
    }

    public function setGiftOrder(GiftOrder $giftOrder)
    {
        $this->giftOrder = $giftOrder;
    }

    public function getPaymentDate()
    {
        if($this->giftOrder) {
            return $this->giftOrder->getPaymentDate();
        } else {
            return $this->createdDate;
        }
    }
    
    public function getCorporate()
    {
        return $this->corporate;
    }

    public function getAttestation()
    {
        return $this->attestation;
    }

    public function setAttestation($attestation)
    {
        $this->attestation = $attestation;
    }

    public function getSettlement()
    {
        return $this->settlement;
    }

}