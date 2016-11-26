<?php

namespace Apr\AutoEntrepreneurBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Apr\CoreBundle\Tools\Tools;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\PreSerialize;

/**
 * @ORM\Entity(repositoryClass="Apr\AutoEntrepreneurBundle\Repository\AutoEntrepreneurRepository")
 * @ORM\Table(name="auto_entrepreneurs")
 * @UniqueEntity(fields="externalEmail", message="Email Existant")
 */

class AutoEntrepreneur
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
     * @ORM\Column(name="activation_date", type="datetime", nullable=true)
     * @var DateTime
     */
    private $activationDate;

    /**
     * @ORM\Column(name="IBAN", type="string", length=255, nullable=true)
     */
    private $IBAN;
    
    /**
     * @ORM\Column(name="BIC", type="string", length=255, nullable=true)
     */
    private $BIC;

    /**
     * @Assert\NotBlank(message="SIRET requis")
     * @ORM\Column(name="SIRET", type="string", length=255)
     */
    private $SIRET;

    /**
     * @Assert\NotBlank(message="Nom Externe requis")
     * @ORM\Column(name="external_lastName", type="string", length=255)
     */
    private $externalLastName;

    /**
     * @Assert\NotBlank(message="Prénom Externe requis")
     * @ORM\Column(name="external_firstName", type="string", length=255)
     */
    private $externalFirstName;

    /**
     * @Assert\NotBlank(message="Email Externe requis")
     * @Assert\Email(message="Email invalide")
     * @ORM\Column(name="external_email", type="string", length=255)
     */
    private $externalEmail;

    /**
     * @Assert\NotBlank(message="Nouveau mot de passe requis")
     * @ORM\Column(name="external_password", type="string", length=255)
     */
    private $externalPassword;

    /**
     * @Assert\NotBlank(message="Ancien mot de passe requis")
     * @ORM\Column(name="external_oldPassword", type="string", length=255)
     */
    private $externalOldPassword;

    /**
     * @var QuarterlyTaxation $lastQuarterlyDeclarationDate
     *
     * @ORM\OneToOne(targetEntity="QuarterlyTaxation")
     * @ORM\JoinColumn(name="last_quarterly_taxation_id", referencedColumnName="id", nullable=true)
     */
    private $lastQuarterlyTaxation;

    /**
     * @var DateTime $lastQuarterlyTaxation
     *
     * @ORM\Column(name="last_quarterly_declaration_date", type="datetime", nullable=true)
     */
    private $lastQuarterlyDeclarationDate;

    /**
     * @var \Apr\ProgramBundle\Entity\Member $member
     *
     * @ORM\OneToOne(targetEntity="\Apr\ProgramBundle\Entity\Member", inversedBy="autoEntrepreneur")
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id")
     */
    private $member;

    /**
     * @var \Apr\ContractBundle\Entity\Party $party
     *
     * @ORM\OneToOne(targetEntity="\Apr\ContractBundle\Entity\Party", inversedBy="autoEntrepreneur", cascade = {"persist"})
     * @ORM\JoinColumn(name="party_id", referencedColumnName="id")
     * @Exclude
     */
    private $party;

    /**
     * @ORM\Column(name="cancel_date", type="datetime", nullable=true)
     * @var DateTime
     */
    private $cancelDate;

    private $label;

    public function __construct() {
        $this->createdDate = Tools::DateTime('now');
        $this->party = new \Apr\ContractBundle\Entity\Party();
        $this->party->setAutoEntrepreneur($this);
    }
    
    public function getId() {
        return $this->id;
    }

    public function getCreatedDate() {
        return $this->createdDate;
    }

    public function getActivationDate() {
        return $this->activationDate;
    }

    public function getIBAN() {
        return $this->IBAN;
    }

    public function setIBAN($IBAN) {
        $this->IBAN = $IBAN;
    }

    public function getBIC() {
        return $this->BIC;
    }

    public function setBIC($BIC) {
        $this->BIC = $BIC;
    }

    public function getSIRET() {
        return $this->SIRET;
    }

    public function setSIRET($SIRET) {
        $this->SIRET = $SIRET;
    }

    public function getExternalLastName() {
        return $this->externalLastName;
    }

    public function setExternalLastName($externalLastName) {
        $this->externalLastName = $externalLastName;
    }

    public function getExternalFirstName() {
        return $this->externalFirstName;
    }

    public function setExternalFirstName($externalFirstName) {
        $this->externalFirstName = $externalFirstName;
    }

    public function getExternalEmail() {
        return $this->externalEmail;
    }

    public function setExternalEmail($externalEmail) {
        $this->externalEmail = $externalEmail;
    }

    public function getExternalPassword() {
        return $this->externalPassword;
    }

    public function setExternalPassword($externalPassword) {
        $this->externalPassword = $externalPassword;
    }

    public function getExternalOldPassword() {
        return $this->externalOldPassword;
    }

    public function setExternalOldPassword($externalOldPassword) {
        $this->externalOldPassword = $externalOldPassword;
    }

    public function setLastQuarterlyTaxation(QuarterlyTaxation $quarterlyTaxation) {
        $this->lastQuarterlyTaxation = $quarterlyTaxation;
    }

    public function getLastQuarterlyTaxation() {
        return $this->lastQuarterlyTaxation;
    }

    public function setLastQuarterlyDeclarationDate() {
        $this->lastQuarterlyDeclarationDate = Tools::DateTime('now');;
    }

    public function getLastQuarterlyDeclarationDate() {
        return $this->lastQuarterlyDeclarationDate;
    }

    public function getMember() {
        return $this->member;
    }

    public function setMember($member) {
        $this->member = $member;
    }

    public function getParty() {
        return $this->party;
    }

    public function getAdministrator() {
        return $this->party->getAdministrator();
    }

    public function getMandataire() {
        return $this->party->getMandataire();
    }

    public function getLabel() {
        return $this->member->getFirstName().' '.$this->member->getLastName();
    }

    public function activate() {
        $this->activationDate = Tools::DateTime('now');
        $this->party->setCanSettle(true);
    }

    public function getDescription() {
        $desc = "l'Auto-Entrepreneur ".$this->member->getDescription().", ";
        if($this->SIRET) {
            $desc .= "immatriculé au RCS sous le numéro SIRET ".$this->SIRET;
        } else {
            $desc .= "en cours d'immatriculation au RCS.".$this->SIRET;
        }
        return $desc;
    }

    public function getCancelDate() {
        return $this->cancelDate;
    }

    public function getStatus() {
        if(is_null($this->activationDate)) {
            return 'standby';
        } else {
            return is_null($this->cancelDate)?'active':'cancel';
        }
    }

    /**
     * Set attributes before serialization
     * @PreSerialize
     */
    public function beforeSerialization(){
        $this->label = $this->getLabel();
    }
}