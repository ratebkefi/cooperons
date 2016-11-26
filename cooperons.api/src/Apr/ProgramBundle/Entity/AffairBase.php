<?php
namespace Apr\ProgramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\PreSerialize;
use Apr\CoreBundle\Tools\Tools;
/**
 * @ORM\Entity
 * @ORM\Table(name="affairs")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"prod" = "Affair", "preprod" = "PreProdAffair"})
 * @UniqueEntity(
 *      fields = {"label", "program"},
 *      message = "Intitulé existant")
 */
abstract class AffairBase
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var Program $program
     * @ORM\ManyToOne(targetEntity="Program", inversedBy="allAffairs", cascade = {"persist"})
     * @ORM\JoinColumn(name="program_id", referencedColumnName="id")
     * @Exclude
     */
    private $program ;
    
    /**
     * @Assert\NotBlank(message = "Intitulé requis")
     * @ORM\Column(name="label" , type="string", length=100)
     */
    private $label;
    
    /**
     * @ORM\Column(name="amount" , type="float", nullable=true)
     *
     */
    private $amount;

    /**
     * @ORM\Column(name="simple_rate", type="integer")
     */
    private $simpleRate;

    /**
     * @ORM\Column(name="multi_rate", type="integer")
     */
    private $multiRate;

    /**
     * @ORM\Column(name="created_date", type="datetime")
     * @var DateTime
     */
    private $createdDate;

    /**
     * @ORM\Column(name="closing_date", type="datetime", nullable=true)
     * @var DateTime
     */
    private $closingDate;

    /**
     * @ORM\Column(name="finish_date", type="datetime", nullable=true)
     * @var DateTime
     */
    private $finishDate;

    /**
     * @ORM\Column(name="cancellation_date", type="datetime", nullable=true)
     * @var DateTime
     */
    private $cancellationDate;

    /**
     * @author Fondative <dev devteam@fondative.com>
     * @var string
     */
    private $status;

    /**
     * @author Fondative <dev devteam@fondative.com>
     * @var string
     */
    private $labelStatus;

    /**
     * @ORM\Column(name="cancel_msg" , type="text", nullable=true)
     *
     */
    private $cancelMsg;
    
    /**
     * @author Fondative <dev devteam@fondative.com>
     * @var float
     */
    private $remains;

    public function __construct(Program $program, $participatesTo)
    {
        $this->createdDate = Tools::DateTime('now');
        $this->program = $program;
        $this->participatesTo = $participatesTo;
        // Recopie simpleRate et multiRate en cas de modification du Program ...
        $this->simpleRate = $program->getEasySetting()->getSimpleRate();
        $this->multiRate = $program->getEasySetting()->getMultiRate();
    }
    
    public function getId(){
        return $this->id;
    }
    
    public function getParticipatesTo(){
        return $this->participatesTo;
    }
    
    public function getProgram(){
        return $this->program;
    }
    
    public function getLabel(){
        return $this->label;
    }

    public function getSimpleRate(){
        return $this->simpleRate;
    }

    public function getMultiRate(){
        return $this->multiRate;
    }

    public function setLabel($label){
        $this->label = $label;
    }
    
    public function getAmount(){
        return $this->amount;
    }
    
    public function setAmount($amount){
        $this->amount = $amount;
    }

    public function addCommission($amount){
        $amount = min($amount, $this->getRemains());
        $commission = ($this->program->getStatus() == 'preprod')?new PreProdCommission($this, $amount):new Commission($this, $amount);
        $this->allCommissions[] = $commission;
        return $commission;
    }

    public function getAllCommissions(){
        return $this->allCommissions;
    }

    public function getRemains($target = 0){
        $result = $this->amount;
        foreach($this->allCommissions as $commission) {
            $result += -$commission->getBase();
        }
        return max(0,$result - $target);
    }

    public function close()
    {
        $this->closingDate = Tools::DateTime('now');
    }

    public function finish($commission)
    {
        if($this->getStatus() == 'payable' && !$this->getRemains($commission->getBase())) {
            $this->finishDate = Tools::DateTime('now');
        }
    }

    public function cancel($message)
    {
        $this->cancellationDate = Tools::DateTime('now');
        $this->cancelMsg = $message;
        if(!is_null($this->amount) && $this->getRemains()) $this->amount += - $this->getRemains();
    }

    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    public function getClosingDate()
    {
        return $this->closingDate;
    }

    public function getCancellationDate()
    {
        return $this->cancellationDate;
    }
    public function getCancelMsg(){
        return $this->cancelMsg;
    }
    
    public function getStatus(){
        if(!is_null($this->cancellationDate)) {
            return 'cancel';
        } elseif(is_null($this->amount)) {
            return 'approach';
        } elseif(is_null($this->closingDate)) {
            return 'negotiation';
        } elseif(is_null($this->finishDate)) {
            return 'payable';
        } else {
            return 'paid';
        }
    }

    public function getLabelStatus() {
        $arrayLabels = array(
            'cancel' => 'Annulée',
            'approach' => 'En Approche',
            'negotiation' => 'En Négociation',
            'payable' => 'Réglements',
            'paid' => 'Cloturé'
        );
        return $arrayLabels[$this->getStatus()];
    }
    
    /**
     * Set attributes before serialization
     *
     * @author Fondative <dev devteam@fondative.com>
     * @PreSerialize
     */
    public function beforeSerialization(){
        $this->status = $this->getStatus();
        $this->remains = $this->getRemains();
        $this->labelStatus = $this->getLabelStatus();
    }

}
