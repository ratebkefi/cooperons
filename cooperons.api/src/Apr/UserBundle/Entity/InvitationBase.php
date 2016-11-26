<?php
namespace Apr\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use FOS\UserBundle\Util\TokenGenerator;
use Apr\CoreBundle\Tools\Tools;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\PreSerialize;

/**
 * @ORM\Entity
 * @ORM\Table(name="invitations")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *     "prod" = "Apr\ProgramBundle\Entity\Invitation",
 *     "preprod" = "Apr\ProgramBundle\Entity\PreProdInvitation",
 *     "clb" = "Apr\ContractBundle\Entity\CollaboratorInvitation",
 *     "ctr" = "Apr\ContractBundle\Entity\ContractInvitation",
 *     "clg" = "Apr\CorporateBundle\Entity\CollegeInvitation"
 * })
 */

abstract class InvitationBase
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="first_name" , type="string", length=255, nullable=true)
     */
    protected $firstName;
    
    /**
     * @ORM\Column(name="last_name" , type="string", length=255, nullable=true)
     */
    protected $lastName;
    
    /**
     * @ORM\Column(name="email" , type="string", length=255)
     */
    protected $email;
    
    /**
     * @ORM\Column(name="created_date", type="datetime")
     * @var DateTime
     */
    protected $createdDate;
    
     /**
     * @ORM\Column(name="date_validate", type="datetime")
     * @var DateTime
     */
    protected $dateValidate;

    /**
     * @var String
     */
    private $token;
    private $type;

    abstract protected function getType();
    abstract protected function getSponsorMember();

    public function __construct($firstName, $lastName, $email, $days = 30)
    {
        $this->createdDate = Tools::DateTime('now');
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->tokenObject = new Token();
        $this->refresh($days);
    }
    
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getFirstName()
    {
        return $this->firstName;
    }
    
    public function getLastName()
    {
        return $this->lastName;
    }
    
    public function getEmail()
    {
        return $this->email;
    }
    
    public function getToken()
    {
        return $this->tokenObject->getValue();
    }
    
    public function getCreatedDate()
    {
        return $this->createdDate;
    }
    
    public function refresh($days = 30) {
        $this->dateValidate = new \DateTime(date("Y-m-d", mktime(0, 0, 0, Tools::DateTime('m'), Tools::DateTime('d')+$days, Tools::DateTime("Y"))));
    }
    
    public function getDateValidate()
    {
        return $this->dateValidate;
    }

    /**
     * Set attributes before serialization
     * @PreSerialize
     */
    public function beforeSerialization(){
        $this->token = $this->getToken();
        $this->type = $this->getType();
    }

}