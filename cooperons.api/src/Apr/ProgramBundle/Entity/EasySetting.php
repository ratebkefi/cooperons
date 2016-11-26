<?php

namespace Apr\ProgramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Exclude;
use Apr\CoreBundle\Tools\Tools;

/**
 * @ORM\Entity
 * @ORM\Table(name="easy_settings")
 */
class EasySetting
{

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Program", cascade = {"persist"})
     * @ORM\JoinColumn(name="program_id", referencedColumnName="id")
     * @Exclude
     */
    private $program;

    private $programDesc =
        "Dans le cadre du Programme #PROGRAM_LABEL#, le Promoteur peut Vous attribuer une ou plusieurs nouvelles affaires (chacune une <b>\"Nouvelle Affaire\"</b>) que vous lui soumettez concernant un prospect que vous avez identifié (<b>\"le Prospect\"</b>).</br></br>
        Lorsqu'une Nouvelle Affaire est effectivement conclue entre le Promoteur et le Prospect, le Promoteur Vous informera du montant (hors taxe) définitif de la Nouvelle Affaire (le <b>\"Montant Maximum\"</b>). De même, le Promoteur Vous informera de chaque réglement effectué par le Prospect dans le cadre de la Nouvelle Affaire (chaque réglement un <b>\"Réglement Commissionné\"</b>).</br></br>
        Le Promoteur Vous attribuera des Points et MultiPoints à chaque Réglement Commissionné effectué par le Prospect, dont le montant (hors taxe) est proportionnel au montant dudit Réglement Commissionné, dans la limite, pour l'ensemble des Réglements Commissionés d'une même Nouvelle Affaire, du Montant Maximum.</br></br>
        Le Promoteur se réserve le droit d'annuler à tout moment une Nouvelle Affaire, quel que soit son stade d'avancement, notamment en cas de manque d'intérêt de la part du Prospect pour l'offre du Promoteur ou de non paiement par le Prospect d'une facture émise dans le cadre de ladite Nouvelle Affaire.";

    /**
     * @ORM\Column(name="simple_rate", type="integer")
     * @Assert\Range(
     *      min = 0,
     *      max = 20,
     *      minMessage = "Commission simple invalide",
     *      maxMessage = "Commission invalide"
     * )
     */
    private $simpleRate = 3;

    private $simpleDesc =
        "dont la valeur (au taux de 20 € pour 100 Points) correspond à #SIMPLE_RATE# % du montant de chaque Réglement Commissionné effectué dans le cadre d'une Nouvelle Affaire.";

    /**
     * @ORM\OneToOne(targetEntity="OperationCredit", cascade = {"persist"})
     * @ORM\JoinColumn(name="simple_operation_id", referencedColumnName="id")
     */
    private $simpleOperation;

    /**
     * @ORM\Column(name="multi_rate", type="integer")
     * @Assert\Range(
     *      min = 1,
     *      max = 20,
     *      minMessage = "Commission MultiPoints doit être non nulle",
     *      maxMessage = "Commission invalide"
     * )
     */
    private $multiRate = 3;

    private $multiDesc =
        "dont la valeur (au taux de 20 € pour 100 Points) correspond à #MULTI_RATE# % du montant de chaque Réglement Commissionné effectué dans le cadre d'une Nouvelle Affaire.";

    /**
     * @ORM\OneToOne(targetEntity="OperationCredit", cascade = {"persist"})
     * @ORM\JoinColumn(name="multi_operation_id", referencedColumnName="id")
     */
    private $multiOperation;

    /**
     * @ORM\ManyToOne(targetEntity="\Apr\CorporateBundle\Entity\UploadedFile", cascade = {"persist"})
     * @ORM\JoinColumn(name="uploaded_file_id", referencedColumnName="id")
     * @Assert\Valid
     */
    private $document;

    public function __construct($program)
    {
        $this->program = $program;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setProgram($program)
    {
        $this->program = $program;
    }

    public function getProgram()
    {
        return $this->program;
    }

    public function getSimpleRate()
    {
        return $this->simpleRate;
    }

    public function setSimpleRate($simpleRate)
    {
        $this->simpleRate = $simpleRate;
        $this->simpleOperation->setDescription($this->getDescription()['simple']);
    }

    public function getMultiRate()
    {
        return $this->multiRate;
    }

    public function setMultiRate($multiRate)
    {
        $this->multiRate = $multiRate;
        $this->multiOperation->setDescription($this->getDescription()['multi']);
    }

    public function setSimpleOperation($operation)
        // Utilisé pour clone ...
    {
        $this->simpleOperation = $operation;
    }

    public function getSimpleOperation()
    {
        return $this->simpleOperation;
    }

    public function setMultiOperation($operation)
        // Utilisé pour clone ...
    {
        $this->multiOperation = $operation;
    }

    public function getMultiOperation()
    {
        return $this->multiOperation;
    }

    public function addEasyOperations()
    {
        $description = $this->getDescription();
        $labelOperation = $this->getLabelOperation();

        $simpleOperation = new OperationCredit($this->program);
        $simpleOperation->setLabel($labelOperation['simple']);
        $simpleOperation->setDescription($description['simple']);
        $this->simpleOperation = $simpleOperation;

        $multiOperation = new OperationCredit($this->program);
        $multiOperation->setLabel($labelOperation['multi']);
        $multiOperation->setMulti(true);
        $multiOperation->setDescription($description['multi']);
        $this->multiOperation = $multiOperation;

        return array($simpleOperation, $multiOperation);
    }

    public function getLabelOperation() {
        return array(
            'simple' => 'Commission Simple',
            'multi' => 'Commission Multi-Niveau',
        );
    }
    public function getDescription() {
        return array(
            'program' => str_replace('#PROGRAM_LABEL#', $this->program->getLabel(), $this->programDesc),
            'simple' => str_replace('#SIMPLE_RATE#', $this->simpleRate, $this->simpleDesc),
            'multi' => str_replace('#MULTI_RATE#', $this->multiRate, $this->multiDesc),
        );
    }
    public function getDocument()
    {
        return $this->document;
    }

    public function setDocument(\Apr\CorporateBundle\Entity\UploadedFile $document)
    {
        $this->document = $document;
    }

}