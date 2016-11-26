<?php

namespace Apr\ProgramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation\Exclude;
use Apr\CoreBundle\Tools\Tools;

/**
 * @ORM\Entity
 * @ORM\Table(name="journal")
 */

class Journal
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Program $program
     * @ORM\ManyToOne(targetEntity="\Apr\ProgramBundle\Entity\Program", inversedBy="allJournals")
     * @ORM\JoinColumn(name="program_id", referencedColumnName="id")
     * @Exclude
     */
    protected $program ;
    
    /**
     * @ORM\Column(name="url", type="string", length=255)
     * 
     */
    private $url;

    /**
     * @ORM\Column(name="method", type="string", length=10)
     */
    private $method;
    
    /**
     * @ORM\Column(name="parameters", type="string", length=255)
     */
    private $parameters;

    /**
     * @ORM\Column(name="created_date",type="datetime")
     * */
    protected $createdDate;
    
    public function __construct() {
        $this->createdDate = Tools::DateTime();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    
    public function setProgram(\Apr\ProgramBundle\Entity\Program  $program)
    {
        $this->program = $program;

        return $this;
    }

    
    public function getProgram()
    {
        return $this->program;
    }

    

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }
    
    public function getMethod()
    {
        return $this->method;
    }
    
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }
    
    public function getParameters()
    {
        return $this->parameters;
    }
    
    public function getCreatedDate()
    {
        return $this->createdDate;
    }
}