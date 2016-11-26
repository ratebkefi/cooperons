<?php
namespace Apr\CorporateBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Apr\CoreBundle\Tools\Tools;

/**
 * @ORM\Entity
 * @ORM\Table(name="attestations")
 */
class Attestation
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var Member $member
     * @ORM\ManyToOne(targetEntity="\Apr\ProgramBundle\Entity\Member", inversedBy="allAttestations")
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id")
     */
    private $member ;

    /**
     * @var Program $program
     * @ORM\ManyToOne(targetEntity="Corporate", inversedBy="allAttestations")
     * @ORM\JoinColumn(name="corporate_id", referencedColumnName="id")
     */
    private $corporate ;

    /**
     * @ORM\OneToMany(targetEntity="\Apr\ProgramBundle\Entity\Avantage", mappedBy="attestation", cascade = {"persist"})
     */
    private $allAvantages;

    /**
     * @ORM\Column(name="year" , type="integer")
     *
     */
    private $year;

    /**
     * @ORM\Column(name="quarter", type="integer")
     */
    private $quarter;

    /**
     * @ORM\Column(name="total_avantage", type="float")
     */
    private $totalAvantage = 0 ;

    /**
     * @ORM\Column(name="smic", type="float")
     */
    private $smic = 1457.52 ;

    /**
     * @ORM\Column(name="cotisation", type="float")
     */
    private $cotisation = 0 ;

    /**
     * @ORM\Column(name="created_date", type="datetime")
     * @var DateTime
     */
    protected $createdDate;

    public function __construct($year, $quarter, \Apr\ProgramBundle\Entity\Member $member, Corporate $corporate)
    {
        $this->createdDate = Tools::DateTime('now');
        // On utilise ArrayCollection pour pouvoir utiliser $this->allAvantages après flush ... Bizarre mais pas le choix ...
        $this->allAvantages = new ArrayCollection();
        $this->year = $year;
        $this->quarter = $quarter;
        $this->member = $member;
        $this->corporate = $corporate;
    }

    public function getId(){
        return $this->id;
    }

    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    public function getYear(){
        return $this->year;
    }

    public function getQuarter(){
        return $this->quarter;
    }

    public function getMember(){
        return $this->member;
    }

    public function getCorporate(){
        return $this->corporate;
    }

    public function addAvantage(\Apr\ProgramBundle\Entity\Avantage $avantage)
    {
        $this->allAvantages[] = $avantage;
        $this->totalAvantage += $avantage->getAmount();
        $avantage->setAttestation($this);
        $this->cotisation = max(0,round(20/100*($this->totalAvantage-10/100*$this->smic),2));
    }

    public function getTotalAvantage(){
        return $this->totalAvantage;
    }

    public function getCotisation(){
        return $this->cotisation;
    }

    public function getLabelOperation()
    {
        return Tools::getLabelOperationById($this->getQuarter())." ".$this->getYear();
    }

    public function buildRef($year, \Apr\ProgramBundle\Entity\Member $member, Corporate $corporate){
        return "ATT".$year."_".$corporate->getSiren()."_".$member->getEmail();
    }

    public function getRef()
    {
        return $this->buildRef($this->year, $this->member, $this->corporate);
    }

    public function getSmic(){
        return $this->smic;
    }

}