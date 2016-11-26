<?php
namespace Apr\ProgramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Apr\CoreBundle\Tools\Tools;
/**
 * @ORM\Entity
 * @ORM\Table(name="commissions")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"prod" = "Commission", "preprod" = "PreProdCommission"})
 */
abstract class CommissionBase
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(name="base" , type="float", nullable=true)
     *
     */
    private $base;

    /**
     * @ORM\Column(name="created_date", type="datetime")
     * @var DateTime
     */
    private $createdDate;
    
    public function __construct($affair, $base)
    {
        $this->createdDate = Tools::DateTime('now');
        $this->affair = $affair;
        $this->base = $base;
        $this->affair->finish($this);
    }
    
    public function getId(){
        return $this->id;
    }
    
    public function getAffair(){
        return $this->affair;
    }
    
    public function getBase(){
        return $this->base;
    }
    
    public function getCreatedDate()
    {
        return $this->createdDate;
    }
}
