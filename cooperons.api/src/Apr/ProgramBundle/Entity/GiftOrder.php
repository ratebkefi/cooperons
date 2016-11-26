<?php

namespace Apr\ProgramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Apr\CoreBundle\Tools\Tools;

/**
 * @ORM\Entity
 * @ORM\Table(name="gift_orders")
 */
class GiftOrder
{

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Avantage", mappedBy="giftOrder", cascade = {"persist"})
     */
    private $allAvantages;

    /**
     * @ORM\Column(name="amount", type="float")
     *
     */
    private $amount = 0;

    /**
     * @ORM\Column(name="quarter", type="integer")
     */
    private $quarter;

    /**
     * @ORM\Column(name="year", type="integer")
     */
    private $year;

    /**
     * @ORM\Column(name="created_date", type="datetime", nullable = true)
     * @var DateTime
     */
    private $createdDate;

    /**
     * @ORM\Column(name="payment_date", type="datetime", nullable = true)
     * @var DateTime
     */
    private $paymentDate;

    /**
     * @var string
     */
    private $labelOperation;

    /**
     * @var string
     */
    private $fileName;


    public function __construct($membersToGift, $year, $quarter)
    {
        $this->createdDate = Tools::DateTime();
        $this->year = $year;
        $this->quarter = $quarter;
        // On utilise ArrayCollection pour pouvoir utiliser $this->allAvantages après flush ... Bizarre mais pas le choix ...
        $this->allAvantages = new ArrayCollection();
        $amount = 0;
        $description = "Chèque cadeau: " . $this->getLabelOperation();
        foreach ($membersToGift as $member) {
            $maxAvantageAmount = $member->calculateMaxAvantage($year, true);
            if ($maxAvantageAmount > 0) {
                $avantage = new Avantage($member, $maxAvantageAmount, $description);
                $avantage->setGiftOrder($this);
                $amount += $avantage->getAmount();
                $this->allAvantages[] = $avantage;
            }
        }

        $this->setAmount($amount);

    }

    public function confirm()
    {
        $this->paymentDate = Tools::DateTime();
        foreach ($this->allAvantages as $avantage) {
            $avantage->confirm();
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getQuarter()
    {
        return $this->quarter;
    }

    public function getYear()
    {
        return $this->year;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function getPaymentDate()
    {
        return $this->paymentDate;
    }

    public function getAllAvantages()
    {
        return $this->allAvantages;
    }

    public function getLabelOperation()
    {
        return Tools::getLabelOperationById($this->getQuarter()) . " " . $this->getYear();
    }

    public function setLabelOperation($labelOperation)
    {
        $this->labelOperation = $labelOperation;
    }

    public function getFileName()
    {
        return 'giftOrder_' . $this->id . '_' . Tools::getLabelOperationById($this->getQuarter()) . '_' . $this->getYear() . '.xls';
    }

    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }
}