<?php
namespace Apr\ProgramBundle\Entity;

// ToDo: PreProdMember / MemberBase ? Pas évident car User->Member (ou alors passer par inversed_by Member->User ...)
// ToDo: Réorganiser méthodes User <-> Member (Ex: Auto-Entrepreneur, Collaborator => User ???)

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\PreSerialize;
use Apr\UserBundle\Entity\User;
use Apr\CoreBundle\Tools\Tools;
/**
 * @ORM\Entity(repositoryClass="Apr\ProgramBundle\Repository\MemberRepository")
 * @ORM\Table(name="members")
 */
class Member
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @Assert\NotBlank()
     * @ORM\Column(name="firstName" , type="string", length=255,nullable=true)
     */
    private $firstName;
    
    /**
     * @Assert\NotBlank()
     * @ORM\Column(name="lastName" , type="string", length=255,nullable=true)
     */
    private $lastName;
    
    /**
     * @Assert\NotBlank()
     * @Assert\Email(
     *     message = "Adresse email invalide",
     * )
     * @ORM\Column(name="email" , type="string", length=255)
     */
    private $email;

    /**
     * @ORM\OneToOne(targetEntity="Apr\UserBundle\Entity\User", cascade = {"all"})
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id", nullable=true)
     * @Exclude
     * 
     */
    private $user = null;

    /**
     * @ORM\Column(name="cgu_member", type="boolean")
     *
     */
    private $hasAcceptedCGUMember = false;

    /**
     * @ORM\Column(name="is_preprod", type="boolean")
     *
     */
    private $isPreProd = false;

    /**
     * @ORM\OneToMany(targetEntity="ParticipatesTo", mappedBy="member")
     * @Exclude
     */
    private $allParticipatesTo;

    /**
     * @ORM\OneToMany(targetEntity="PreProdParticipatesTo", mappedBy="member")
     * @Exclude
     */
    private $allPreProdParticipatesTo;

    /**
     * @var ArrayCollection $allCollaborators
     *
     * @ORM\OneToMany(targetEntity="\Apr\ContractBundle\Entity\Collaborator", mappedBy="member")
     * @Exclude
     */
    private $allCollaborators;
    
    /**
     * @ORM\Column(name="created_date", type="datetime")
     * @var DateTime
     */
    private $createdDate;

    /**
     * @ORM\Column(name="remaining_points" , type="integer")
     */
    private $remainingPoints = 0;

    /**
     * @ORM\OneToOne(targetEntity="Avantage")
     * @ORM\JoinColumn(name="last_avantage_id", referencedColumnName="id", nullable=true)
     *
     */
    protected $lastAvantage = null;

    /**
     * @ORM\OneToMany(targetEntity="Avantage", mappedBy="member")
     * @Exclude
     */
    private $allAvantages;

    /**
     * @ORM\ManyToOne(targetEntity="\Apr\CorporateBundle\Entity\College")
     * @ORM\JoinColumn(name="college_id", referencedColumnName="id", nullable=true)
     * @Exclude
     */
    private $college;

    /**
     * @var ArrayCollection $allAttestations
     *
     * @ORM\OneToMany(targetEntity="\Apr\CorporateBundle\Entity\Attestation", mappedBy="member")
     * @Exclude
     */
    private $allAttestations;

    /**
     * @var ArrayCollection $allColleges
     *
     * @ORM\OneToMany(targetEntity="\Apr\CorporateBundle\Entity\College", mappedBy="member")
     * @Exclude
     */
    private $allColleges;

    private $valuePoint = 0.2;


    // AutoEntrepreneur

    /**
     * @ORM\OneToOne(targetEntity="\Apr\AutoEntrepreneurBundle\Entity\AutoEntrepreneur", mappedBy="member")
     * @Exclude
     */
    private $autoEntrepreneur;

    private $isAutoEntrepreneur;
    private $isUser;
    private $maxAvantage;
    private $label;

    public function __construct()
    {
        $this->createdDate = Tools::DateTime();
    }
    
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getFirstName()
    {
        if($this->user) {
            return $this->user->getFirstName();
        } else {
            return $this->firstName;
        }
    }
    
    public function setFirstName($firstName)
    {
        if($this->user) {
            $this->user->setFirstName($firstName);
        } else {
            $this->firstName = $firstName;
        }
    }
    
    public function getLastName()
    {
        if($this->user) {
            return $this->user->getLastName();
        } else {
            return $this->lastName;
        }
    }
    
    public function setLastName($lastName)
    {
        if($this->user) {
            $this->user->setLastName($lastName);
        } else {
            $this->lastName = $lastName;
        }
    }

    public function getLabel()
    {
        return $this->getFirstName()." ".$this->getLastName();
    }

    public function getDescription()
    {
        $desc = $this->getLabel();
        if($this->user) {
            $desc .= " domicilié(e) à ".$this->user->getContact()->getAddressDescription();
        }
        return $desc;
    }

    public function getEmail()
    {
        if($this->user) {
            return $this->user->getEmail();
        } else {
            return $this->email;
        }
    }
    
    public function setEmail($email)
    {
        if($this->user) {
            $this->user->setEmail($email);
        } else {
            $this->email = $email;
        }
    }
    
    public function getUser()
    {
        return $this->user;
    }
    
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    // Utilisé lors de la suppression des comptes orphelins en passage en production ...

    public function unsetUser()
    {
        $this->user = null;
    }

    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    public function isPreProd()
    {
        return $this->isPreProd;
    }

    public function restrictPreProd()
    {
        $this->isPreProd = true;
    }

    public function hasAcceptedCGUMember()
    {
        return $this->hasAcceptedCGUMember;
    }

    public function acceptCGUMember()
    {
        return $this->hasAcceptedCGUMember = true;
    }

    public function isAutoEntrepreneur()
    {
        return !is_null($this->autoEntrepreneur);
    }

    public function getAutoEntrepreneur()
    {
        return $this->autoEntrepreneur;
    }

    public function setAutoEntrepreneur(\Apr\AutoEntrepreneurBundle\Entity\AutoEntrepreneur $autoEntrepreneur)
    {
        $this->autoEntrepreneur = $autoEntrepreneur;
    }

    public function getAllParticipatesTo($status = null)
    {
        if(is_null($status)) {
            return new ArrayCollection(array_merge((array) $this->allParticipatesTo->toArray(), (array) $this->allPreProdParticipatesTo->toArray()));
        } elseif ($status == 'prod') {
            return $this->allParticipatesTo;
        } elseif ($status == 'preprod') {
            return $this->allPreProdParticipatesTo;
        }
    }

    public function getAllCollaborators()
    {
        return $this->allCollaborators;
    }

    public function getAllParties()
    {
        $allParties = array();
        $allPartyIds = array();
        foreach($this->allColleges as $college) {
            $party = $college->getCorporate()->getParty();
            if(!is_null($party)) {
                $idParty = $party->getId();
                if(!in_array($idParty, $allPartyIds)) {
                    array_push($allPartyIds, $idParty);
                    array_push($allParties, $party);
                }
            }
        }

        foreach($this->allCollaborators as $collaborator) {
            $party = $collaborator->getParty();
            $idParty = $party->getId();
            if(!in_array($idParty, $allPartyIds)) {
                array_push($allPartyIds, $idParty);
                array_push($allParties, $party);
            }
        }

        return new ArrayCollection($allParties);
    }

    public function addPoints($amount)
    {
        if($amount > 0) $this->remainingPoints += $amount;
    }

    public function getRemainingPoints()
    {
        return $this->remainingPoints;
    }

    public function getTotal()
    {
        $points = $multiPoints = 0;

        foreach($this->getAllParticipatesTo('prod') as $participatesTo) {
            $total = $participatesTo->getTotal();
            $points += $total['points'];
            $multiPoints += $total['multiPoints'];
        }

        return array('points' => $points, 'multiPoints' => $multiPoints);
    }

    public function getValuePoints($points = null)
    {
        if(is_null($points)) $points = $this->remainingPoints;
        return $points * $this->valuePoint;
    }

    public function convertToPoints($amount, $ceil = false)
    {
        // Ceil: pour la conversion de la DEPENSE des Points (chèques cadeau / réduction de frais de gestion)
        // Floor: pour la conversion du GAIN de Points
        return $ceil?ceil($amount / $this->valuePoint):floor($amount / $this->valuePoint);
    }

    // Avantage, College ...

    public function calculateMaxAvantage($year = null, $forGift = false) {
        $valueRemainingPoints = $this->getValuePoints();
        if(is_null($year)) $year = Tools::DateTime("Y");

        if(!$this->isAutoEntrepreneur()) {
            if(!is_null($this->college) && $this->college->isUnlocked($forGift)) {
                if($this->remainingPoints <= 2500) {
                    // Moins de 2500 Points: 140 € max
                    $cap = 140;
                } else {
                    // Plus de 2500 Points: 1000 € max
                    $cap = 1000;
                }
            } else {
                // Si pas membre d'un collège: 140 € max
                if(!is_null($this->lastAvantage) && $this->lastAvantage->getYear() == $year) {
                    $cumulatedYear = $this->lastAvantage->getCumulatedYear();
                } else {
                    $cumulatedYear = 0;
                }
                $cap = 140 - $cumulatedYear;
            }
        } else {
            $cap = $valueRemainingPoints;
        }

        return min($valueRemainingPoints, $cap);
    }

    public function setLastAvantage(Avantage $lastAvantage)
    {
        $this->lastAvantage = $lastAvantage;
        $this->remainingPoints += - $this->convertToPoints($lastAvantage->getAmount(), true);
    }

    public function getLastAvantage()
    {
        return $this->lastAvantage;
    }

    public function getTotalAvantage()
    {
        if(is_null($this->lastAvantage)) {
            return 0;
        } else {
            return $this->lastAvantage->getCumulatedTotal();
        }
    }

    public function getAllAvantages()
    {
        return $this->allAvantages;
    }

    public function getCollege()
    {
        return $this->college;
    }

    public function setCollege($college)
    {
        $this->college = $college;
    }

    public function getAllColleges()
    {
        return $this->allColleges;
    }

    public function getAllAttestations()
    {
        return $this->allAttestations;
    }

     /**
     * Set attributes before serialization
     *
     * @author Fondative <dev devteam@fondative.com>
     * @PreSerialize
     */
    public function beforeSerialization(){
        $this->maxAvantage = $this->calculateMaxAvantage();
        $this->isUser = $this->user !== null;
        $this->isAutoEntrepreneur = $this->isAutoEntrepreneur();
        $this->firstName = $this->getFirstName();
        $this->lastName = $this->getLastName();
        $this->email = $this->getEmail();
        $this->label = $this->getLabel();
    }

}