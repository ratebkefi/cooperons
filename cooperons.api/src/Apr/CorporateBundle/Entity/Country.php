<?php

namespace Apr\CorporateBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="Apr\CorporateBundle\Repository\CountryRepository")
 * @ORM\Table(name="country")
 */
class Country
{

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="code", type="string", length=10)
     * 
     */
    private $code;

    /**
     * @ORM\Column(name="label", type="string", length=255)
     */
    private $label;

    /**
     * @ORM\Column(name="ue", type="boolean")
     */
    private $ue=false;

    /**
     * @ORM\Column(name="rate_tva", type="float", nullable=true)
     */
    private $rateTva;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    
    public function getLabel()
    {
        return $this->label;
    }

    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setUE($boolean)
    {
        $this->ue = $boolean;

        return $this;
    }

    public function getUE()
    {
        return $this->ue;
    }

    public function isFR()
    {
        return $this->code == 'fr';
    }

    public function setRateTva($rateTva)
    {
        $this->rateTva = $rateTva;

        return $this;
    }

    public function getRateTva()
    {
        return $this->rateTva;
    }



}