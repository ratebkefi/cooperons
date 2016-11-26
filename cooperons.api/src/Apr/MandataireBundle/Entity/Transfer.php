<?php

namespace Apr\MandataireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Apr\CoreBundle\Tools\Tools;

/**
 * @ORM\Entity
 * @ORM\Table(name="transfers")
 */
class Transfer
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
    private $createdDate;

    /**
     * @var Mandataire
     *
     * @ORM\ManyToOne(targetEntity="Mandataire", inversedBy="allDebitedTransfers", cascade = {"persist"})
     * @ORM\JoinColumn(name="debit_mandataire_id", referencedColumnName="id")
     * 
     */
    private $debitMandataire;

    /**
     * @var Mandataire
     *
     * @ORM\ManyToOne(targetEntity="Mandataire", inversedBy="allCreditedTransfers", cascade = {"persist"})
     * @ORM\JoinColumn(name="credit_mandataire_id", referencedColumnName="id")
     *
     */
    private $creditMandataire;

    /**
     * @ORM\Column(name="amount", type="float", nullable=true)
     */
    private $amount;
    
    /**
     * @var Record
     *
     * @ORM\OneToOne(targetEntity="Record", inversedBy="transfer", cascade = {"persist"})
     * @ORM\JoinColumn(name="record_id", referencedColumnName="id")
     *
     */
    private $record;

    public function __construct(Mandataire $debitMandataire, Mandataire $creditMandataire, $amount)
    {
        $this->createdDate = Tools::DateTime();
        $this->debitMandataire = $debitMandataire;
        $this->creditMandataire = $creditMandataire;
        $this->amount = $amount;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDebitMandataire()
    {
        return $this->debitMandataire;
    }

    public function getCreditMandataire()
    {
        return $this->creditMandataire;
    }

    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    public function getAmount()
    {
        return $this->amount;
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

}