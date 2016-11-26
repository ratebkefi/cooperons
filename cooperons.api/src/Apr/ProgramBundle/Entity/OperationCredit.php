<?php
namespace Apr\ProgramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation\Exclude;
use Apr\CoreBundle\Tools\Tools;
/**
 * @ORM\Entity(repositoryClass="Apr\ProgramBundle\Repository\OperationCreditRepository")
 * @UniqueEntity(
 *      fields = {"label", "program"},
 *      message = "Intitulé existant")
 * @ORM\Table(name="operations")
 */

class OperationCredit
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var Program
     *
     * @ORM\ManyToOne(targetEntity="Program", inversedBy="allOperations", cascade = {"persist"})
     * @ORM\JoinColumn(name="program_id", referencedColumnName="id")
     * @Exclude
     */
    private $program;
    
    /**
     * @ORM\Column(name="label" , type="string", length=255)
     * @Assert\NotBlank(message = "Intitulé requis")
     * @Assert\NotEqualTo(
     *     value = "__multi",
     *     message = "reserved"
     * )
     */
    private $label;
    
    /**
     * @ORM\Column(name="description" , type="text", nullable=true)
     *
     */
    private $description;
    
    /**
     * @ORM\Column(name="default_amount" , type="float", nullable=true)
     *
     */
    private $defaultAmount = null;
    
    
    /**
     * @ORM\Column(name="is_multi" , type="boolean")
     *
     */
    private $isMulti = false;
    
    /**
     * @ORM\Column(name="created_date", type="datetime")
     * @var DateTime
     */
    protected $createdDate;
    
    public function __construct(Program $program)
    {
        $this->createdDate = Tools::DateTime();
        $this->program = $program;
    }

    public function getProgram()
    {
        return $this->program;
    }

    // Utilisé pour clone Program ...
    public function setProgram($program)
    {
        $this->program = $program;
    }

    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    public function getId(){
        return $this->id;
    }
    
    public function getLabel(){
        return $this->label;
    }
    
    public function setLabel($label){
        $this->label = $label;
    }
    
    public function getDescription(){
        return $this->description;
    }
    
    public function setDescription($description){
        $this->description = $description;
    }
    
    public function getDefaultAmount(){
        return $this->defaultAmount;
    }
    
    public function setDefaultAmount($defaultAmount){
        $this->defaultAmount = $defaultAmount;
    }
    
    public function isMulti(){
        return $this->isMulti;
    }
    
    public function setMulti($isMulti){
        $this->isMulti = $isMulti;
    }
    
}