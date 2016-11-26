<?php

namespace Apr\MandataireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Apr\CoreBundle\Tools\Tools;
use JMS\Serializer\Annotation\Exclude;

/**
 * @ORM\Entity(repositoryClass="Apr\MandataireBundle\Repository\PaymentRepository")
 * @ORM\Table(name="payment")
 */
class Payment
{

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var Mandataire
     *
     * @ORM\ManyToOne(targetEntity="Mandataire", inversedBy="allPayments", cascade = {"persist"})
     * @ORM\JoinColumn(name="mandataire_id", referencedColumnName="id")
     * 
     */
    private $mandataire;

    /**
     * @ORM\Column(name="mode", type="string", length=50)
     */
    private $mode;
    
    /**
     * @ORM\Column(name="amount", type="float", nullable=true)
     */
    private $amount;
    
    /**
     * @ORM\Column(name="status", type="integer")
     * 
     */
    private $status = 0;

    private $arrStatus = array(0 => "standby", 1 => "payed");

    /**
     * @ORM\Column(name="auth_code", type="string", length=255, nullable=true)
     *
     */
    private $authCode;

    /**
     * @ORM\Column(name="created_date", type="datetime")
     * @var DateTime
     */
    private $createdDate;

    /**
     * @var Record
     *
     * @ORM\ManyToOne(targetEntity="Record", inversedBy="allPayments", cascade = {"persist"})
     * @ORM\JoinColumn(name="record_id", referencedColumnName="id")
     * @Exclude
     *
     */
    private $record;

    /**
     * @var \Apr\AutoEntrepreneurBundle\Entity\QuarterlyTaxation $quarterlyTaxation
     * @ORM\OneToOne(targetEntity="\Apr\AutoEntrepreneurBundle\Entity\QuarterlyTaxation", mappedBy="payment")
     */
    private $quarterlyTaxation ;

    public function __construct(Mandataire $mandataire, $amount, $mode, $authCode = null)
    {
        $this->createdDate = Tools::DateTime();
        $this->mandataire = $mandataire;
        $this->amount = $amount;
        $this->mode = $mode;
        $this->authCode = $authCode;
    }

    public function validate()
    {
        if($this->amount > 0 or (($this->mandataire->getFreeDepot() + $this->amount) >= 0) or $this->mandataire->getLiquidationDate()) {
            $this->status = 1;
            if($this->mandataire->getStandByPaymentOut() == $this) $this->mandataire->setStandByPaymentOut(null);
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getMandataire()
    {
        return $this->mandataire;
    }

    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    public function getMode()
    {
        return $this->mode;
    }

    public function getAmount()
    {
        return $this->amount;
    }
    
    public function getStatus()
    {
        return $this->arrStatus[$this->status];
    }

    /**
     * Record
     */

    public function setRecord(Record $record) {
        $this->record = $record;
    }

    public function getRecord() {
        return $this->record;
    }

    /**
     * AutoEntrepreneur: QuarterlyTaxation
     */

    public function setQuarterlyTaxation($quarterlyTaxation)
    {
        $this->quarterlyTaxation = $quarterlyTaxation;
    }

    public function getQuarterlyTaxation()
    {
        return $this->quarterlyTaxation;
    }

}