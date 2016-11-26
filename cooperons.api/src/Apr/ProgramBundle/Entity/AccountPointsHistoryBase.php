<?php

namespace Apr\ProgramBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Apr\CoreBundle\Tools\Tools;

/**
 * @ORM\Entity
 * @ORM\Table(name="account_points")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"prod" = "AccountPointsHistory", "preprod" = "PreProdAccountPointsHistory"})
 */

abstract class AccountPointsHistoryBase
{

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Program", cascade = {"persist"})
     * @ORM\JoinColumn(name="program_id", referencedColumnName="id")
     */
    private $program;

    /**
     * @ORM\Column(name="points", type="integer")
     * @Assert\NotBlank()
     */
    private $points;
    
    /**
     * @ORM\Column(name="is_multi", type="boolean")
     * @Assert\NotBlank()
     */
    private $isMulti = false;

    /**
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status = "standby";

    /**
     * @ORM\Column(name="created_date", type="datetime")
     * @var DateTime
     */
    private $createdDate;

    /**
     * Mandataire
     *
     * @ORM\ManyToOne(targetEntity="\Apr\MandataireBundle\Entity\Settlement", inversedBy="allAccountPointsHistory", cascade = {"persist"})
     * @ORM\JoinColumn(name="settlement_id", referencedColumnName="id")
     */
    private $settlement;

    /**
     * @ORM\ManyToOne(targetEntity="\Apr\MandataireBundle\Entity\Invoice", cascade = {"persist"})
     * @ORM\JoinColumn(name="invoice_id", referencedColumnName="id", nullable = true)
     */
    private $invoice;

    public function __construct($participatesTo, $points, $isMulti, $description, $type)
    {
        $this->createdDate = Tools::DateTime('now');
        $this->program = $participatesTo->getProgram();
        $this->participatesTo = $participatesTo;
        $this->points = $points;
        $this->isMulti = $isMulti;
        $this->description = $description;
        $this->type = $type;
    }

    public function confirm()
    {
        $this->status = 'confirmed';
        $this->participatesTo->addPoints($this->points, $this->isMulti);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getProgram()
    {
        return $this->program;
    }

    public function getParticipatesTo()
    {
        return $this->participatesTo;
    }

    public function setAffiliateHistory($affiliateHistory)
    {
        $this->affiliateHistory = $affiliateHistory;
    }

    public function getAffiliateHistory()
    {
        return $this->affiliateHistory;
    }

    public function getMember()
    {
        return $this->participatesTo->getMember();
    }

    public function getPoints()
    {
        return $this->points;
    }

    public function isMulti()
    {
        return $this->isMulti;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Mandataire
     */

    public function setSettlement(\Apr\MandataireBundle\Entity\Settlement $settlement)
    {
        $this->settlement = $settlement;
    }

    public function getSettlement()
    {
        return $this->settlement;
    }

    public function setInvoice(\Apr\MandataireBundle\Entity\Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function getInvoice()
    {
        return $this->invoice;
    }

}