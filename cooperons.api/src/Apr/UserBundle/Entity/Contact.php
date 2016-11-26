<?php

namespace Apr\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="contact")
 */
class Contact
{

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="phone", type="string", length=10, nullable = true)
     * 
     */
    private $phone;

    /**
     * @ORM\Column(name="address", type="string", length=255, nullable = true)
     * @Assert\NotBlank()
     */
    private $address;

    /**
     * @ORM\Column(name="second_address", type="string", length=255, nullable = true)
     */
    private $secondAddress;

    /**
     * @ORM\Column(name="city", type="string", length=255, nullable = true)
     * @Assert\NotBlank()
     */
    private $city;

    /**
     * @ORM\Column(name="postal_code", type="integer", nullable = true)
     * @Assert\NotBlank()
     */
    private $postalCode;

    /**
     * @var integer $num_departement
     * @ORM\ManyToOne(targetEntity="Departement")
     * @ORM\JoinColumn(name="num_departement", referencedColumnName="id")
     */
    private $num_departement;

    /**
     * @var User $user
     *
     * @ORM\OneToOne(targetEntity="User", mappedBy="contact", cascade={"persist", "merge"})
     * 
     * 
     */
    private $user;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
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
     * Set address
     *
     * @param string $address
     * @return Contact
     */
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

    /**
     * Set address
     *
     * @param string $address
     * @return Contact
     */
    public function setSecondAddress($secondAddress)
    {
        $this->secondAddress = $secondAddress;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getSecondAddress()
    {
        return $this->secondAddress;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Contact
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set postalCode
     *
     * @param string $postalCode
     * @return Contact
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get postalCode
     *
     * @return string 
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    public function setNumDepartement($num_departement)
    {
        $this->num_departement = $num_departement;
        return $this;
    }

    public function getNumDepartement()
    {
        return $this->num_departement;
    }

    /**
     * Set User
     * 
     * @param BUser $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get User
     * 
     * @return BUser $user
     */
    public function getUser()
    {
        return $this->user;
    }

    public function getAddressDescription()
    {
        $desc = $this->address;
        if($this->secondAddress) $desc .= ", ".$this->secondAddress;
        $desc .= " à ".$this->city;
        $desc .= " (".$this->postalCode.")";
        return $desc;
    }

}