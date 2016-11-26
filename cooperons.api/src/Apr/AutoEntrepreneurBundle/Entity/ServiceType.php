<?php

namespace Apr\AutoEntrepreneurBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Apr\CoreBundle\Tools\Tools;

/**
 * @ORM\Entity
 * @ORM\Table(name="service_types")
 */

class ServiceType
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\NotBlank(message = "Description requise")
     * @ORM\Column(name="label", type="string", length=255)
     */
    private $label;

    /**
     * @ORM\Column(name="unit_label", type="string", length=255, nullable=true)
     */
    private $unitLabel;

    /**
     * @ORM\Column(name="unit_default_amount", type="float", nullable=true)
     */
    private $unitDefaultAmount;

    /**
     * @var \Apr\ContractBundle\Entity\LegalDocument
     *
     * @ORM\ManyToOne(targetEntity="\Apr\ContractBundle\Entity\LegalDocument", inversedBy="allServiceTypes", cascade = {"persist"})
     * @ORM\JoinColumn(name="legal_document_id", referencedColumnName="id")
     *
     */
    private $legalDocument;

    public function __construct(\Apr\ContractBundle\Entity\LegalDocument $legalDocument) {
        $this->createdDate = Tools::DateTime('now');
        $this->legalDocument = $legalDocument;
    }

    public function setLegalDocument($legalDocument){
        $this->legalDocument = $legalDocument;
    }

    public function getId(){
        return $this->id;
    }

    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    public function getLegalDocument()
    {
        return $this->legalDocument;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function getUnitLabel()
    {
        return $this->unitLabel;
    }

    public function setUnitLabel($unitLabel)
    {
        $this->unitLabel = $unitLabel;
    }

    public function getUnitDefaultAmount()
    {
        return $this->unitDefaultAmount;
    }

    public function setUnitDefaultAmount($unitDefaultAmount)
    {
        $this->unitDefaultAmount = $unitDefaultAmount;
    }


}