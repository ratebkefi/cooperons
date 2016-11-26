<?php

namespace Apr\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="departement")
 */
class Departement
{

    /**
     * @ORM\Column(name="id", type="string", length=255)
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\Column(name="num_region", type="integer")
     * 
     */
    private $num_region;

    /**
     * @ORM\Column(name="label_departement", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $label_departement;

    /**
     * @ORM\Column(name="limitrophe", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $limitrophe;

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
     * Set label_departement
     *
     * @param string $label_departement
     * @return Departement
     */
    public function setLabelDepartement($label_departement)
    {
        $this->label_departement = $label_departement;

        return $this;
    }

    /**
     * Get label_departement
     *
     * @return string 
     */
    public function getLabelDepartement()
    {
        return $this->label_departement;
    }

    /**
     * Set num_region
     *
     * @param integer $num_region
     * @return Departement
     */
    public function setNumRegion($num_region)
    {
        $this->num_region = $num_region;

        return $this;
    }

    /**
     * Get num_region
     *
     * @return integer 
     */
    public function getNumRegion()
    {
        return $this->num_region;
    }

    /**
     * Set limitrophe
     *
     * @param string $limitrophe
     * @return Departement
     */
    public function setLimitrophe($limitrophe)
    {
        $this->limitrophe = $limitrophe;

        return $this;
    }

    /**
     * Get limitrophe
     *
     * @return string 
     */
    public function getLimitrophe()
    {
        return $this->limitrophe;
    }


    /**
     * Set id
     *
     * @param string $id
     * @return Departement
     */
    public function setId($id)
    {
        $this->id = $id;
    
        return $this;
    }
}