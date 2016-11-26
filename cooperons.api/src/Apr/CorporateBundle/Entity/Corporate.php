<?php

namespace Apr\CorporateBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Apr\CoreBundle\Tools\Tools;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\PreSerialize;


/**
 * @ORM\Entity(repositoryClass="Apr\CorporateBundle\Repository\CorporateRepository")
 * @ORM\Table(name="corporates")
 */

class Corporate
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Raison sociale requise")
     * @ORM\Column(name="raison_social", type="string", length=255, nullable=true)
     */
    private $raisonSocial;
    
    /**
     * @Assert\NotBlank(message="Adresse requise")
     * @ORM\Column(name="address", type="string", length=255, nullable = true)
     */
    private $address;
    
    /**
     * @ORM\Column(name="second_address", type="string", length=255, nullable = true)
     */
    private $secondAddress;

    /**
     * @Assert\NotBlank(message="Ville requise")
     * @ORM\Column(name="city", type="string", length=255, nullable = true)
     */
    private $city;

    /**
     * @Assert\NotBlank(message="Code postal requis")
     * @ORM\Column(name="postal_code", type="integer", nullable = true)
     */
    private $postalCode;

    /**
     * @var Country $country
     *
     * @ORM\ManyToOne(targetEntity="\Apr\CorporateBundle\Entity\Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     */

    private $country;

    /**
     * @ORM\Column(name="created_date", type="datetime")
     * @var \DateTime
     */
    private $createdDate;
    

    /**
     * @Assert\NotBlank(message="Téléphone requis")
     * @ORM\Column(name="phone", type="string", length=10, nullable = true)
     * 
     */
    private $phone;
    
    /**
     * @ORM\Column(name="tva_intracomm", type="string", length=100, nullable = true)
     * 
     */
    private $tvaIntracomm;

    // College ...

    /**
     * @var College $delegate
     * @ORM\OneToOne(targetEntity="College")
     * @ORM\JoinColumn(name="delegate_id", referencedColumnName="id", nullable = true)
     * @Exclude
     */
    private $delegate ;

    /**
     * @ORM\Column(name="count_colleges", type="integer")
     */
    private $countColleges = 0;

    /**
     * @var ArrayCollection $attestations
     *
     * @ORM\OneToMany(targetEntity="Attestation", mappedBy="corporate")
     * @Exclude
     */
    private $allAttestations;

    // Party

    /**
     * @var \Apr\ContractBundle\Entity\Party $party
     * @ORM\OneToOne(targetEntity="\Apr\ContractBundle\Entity\Party", cascade = {"persist"})
     * @ORM\JoinColumn(name="party_id", referencedColumnName="id", nullable = true)
     */
    private $party ;

    public function __construct() {
        $this->createdDate = Tools::DateTime('now');
        $this->party = new \Apr\ContractBundle\Entity\Party();
        // Activation par défaut du Party ...
        $this->party->setCanSettle(true);
        $this->party->setCorporate($this);
    }
    
    public function getId(){
        return $this->id;
    }

    // Utilisé pour Mise à Jour Corporate lors de la création avec Siren ...
    public function setId($id){
        $this->id = $id;
    }

    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    public function getRaisonSocial()
    {
        return $this->raisonSocial;
    }

    public function setRaisonSocial($raisonSocial)
    {
        $this->raisonSocial = $raisonSocial;
        return $this;
    }
    
    public function getSiren()
    {
        if(substr($this->tvaIntracomm, 0, 2) != 'FR') return null;
        return substr($this->tvaIntracomm, 3, 9);
    }

    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    public function setSecondAddress($secondAddress)
    {
        $this->secondAddress = $secondAddress;

        return $this;
    }

    public function getSecondAddress()
    {
        return $this->secondAddress;
    }

    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getPostalCode()
    {
        return $this->postalCode;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry(\Apr\CorporateBundle\Entity\Country $country)
    {
        $this->country = $country;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return string
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }
    
    /**
     * Set tvaIntracomm
     *
     * @param string $tvaIntracomm
     * @return string
     */
    public function setTvaIntracomm($tvaIntracomm)
    {
        $this->tvaIntracomm = $tvaIntracomm;

        return $this;
    }

    /**
     * Get tvaIntracomm
     *
     * @return string 
     */
    public function getTvaIntracomm()
    {
        return $this->tvaIntracomm;
    }
    
    /**
     * Validation: not UE
     */
    public function isNotUE()
    {
        return !$this->country->getUE();
    }

    public function getRateTva()
    {
        return $this->country->getRateTva();
    }

    /**
     * Get tvaApplicable
     *
     * @return boolean
     */
    public function getTvaApplicable()
    {
        return !($this->isNotUE() || ($this->getTvaIntracomm() && $this->getTvaIntracomm()!=''));
    }

    // College ...

    public function setDelegate($delegate)
    {
        $this->delegate = $delegate;
    }

    public function getDelegate()
    {
        return $this->delegate;
    }

    public function getAccordRef()
    {
        return 'ACC_'.$this->getSiren();
    }

    public function addCountColleges($increment)
    {
        $this->countColleges += $increment;
    }

    public function getCountColleges()
    {
        return $this->countColleges;
    }

    public function getAllAttestations()
    {
        return $this->allAttestations;
    }
    
    public function isAccordSigned() {
        return $this->party->getCpStimulationDocument() && $this->party->getCpStimulationDocument()->getStatus() == 'active';
    }

    // Contract ...

    public function getParty()
    {
        return $this->party;
    }

    public function getAdministrator()
    {
        return $this->party->getAdministrator();
    }

    public function getAddressDescription()
    {
        $desc = $this->address;
        if($this->secondAddress) $desc .= ", ".$this->secondAddress;
        $desc .= " à ".$this->city;
        $desc .= " (".$this->postalCode.")";
        return $desc;
    }

    public function getDescription()
    {
        $desc = "la société ".$this->raisonSocial;
        if($this->getSiren()) $desc .= " immatriculée au RCS sous le numéro SIREN ".$this->getSiren();
        $desc .= " dont le siège social est situé au ".$this->getAddressDescription();
        return $desc;
    }


}