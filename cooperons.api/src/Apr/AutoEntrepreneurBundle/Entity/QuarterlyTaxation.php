<?php

namespace Apr\AutoEntrepreneurBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\PreSerialize;
use Apr\CoreBundle\Tools\Tools;

/**
 * @ORM\Entity
 * @ORM\Table(name="quarterly_taxations")
 */

class QuarterlyTaxation
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var AutoEntrepreneur
     *
     * @ORM\ManyToOne(targetEntity="AutoEntrepreneur", cascade = {"persist"})
     * @ORM\JoinColumn(name="auto_entrepreneur_id", referencedColumnName="id")
     *
     */
    private $autoEntrepreneur;

    /**
     * @ORM\Column(name="total_income_ht", type="float")
     * @var float
     */
    private $totalIncomeHt = 0;

    /**
     * @ORM\Column(name="amount", type="float")
     * @var float
     */
    private $amount = 0;

    /**
     * @var \Apr\MandataireBundle\Entity\Payment
     *
     * @ORM\OneToOne(targetEntity="\Apr\MandataireBundle\Entity\Payment", cascade = {"persist"}, inversedBy="quarterlyTaxation")
     * @ORM\JoinColumn(name="payment_id", referencedColumnName="id", nullable = true)
     */
    private $payment;

    /**
     * @ORM\Column(name="cut_off_date", type="datetime")
     * @var DateTime
     */
    private $cutOffDate;

    private $defaultQuarterlyTaxationRate = 25;

    public function __construct(AutoEntrepreneur $autoEntrepreneur, $totalIncomeHt) {
        $this->autoEntrepreneur = $autoEntrepreneur;
        $this->totalIncomeHt = $totalIncomeHt;
        $this->amount = ceil($totalIncomeHt * $this->defaultQuarterlyTaxationRate/100);
        $this->cutOffDate = Tools::firstDayOf('quarter');
    }
    
    public function getId(){
        return $this->id;
    }

    public function getAutoEntrepreneur()
    {
        return $this->autoEntrepreneur;
    }

    public function getPayment()
    {
        return $this->payment;
    }

    public function getCutOffDate()
    {
        return $this->cutOffDate;
    }

    public function getTotalIncomeHt()
    {
        return $this->totalIncomeHt;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function confirm(\Apr\MandataireBundle\Entity\Payment $payment) {
        $this->payment = $payment;
        $this->amount = -$payment->getAmount();
        $payment->setQuarterlyTaxation($this);
        $this->autoEntrepreneur->setLastQuarterlyTaxation($this);
        $this->autoEntrepreneur->setLastQuarterlyDeclarationDate();
    }
}