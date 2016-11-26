<?php

namespace Apr\UserBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Apr\CoreBundle\Tools\Tools;
/**
 * @ORM\Entity (repositoryClass="Apr\UserBundle\Repository\UserRepository")
 * @ORM\Table(name="users")
 * @ExclusionPolicy("all")
 */
class User extends BaseUser implements UserInterface, AdvancedUserInterface, BaseUserInterface
{

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose
     */
    protected $id;
    
    /**
     *
     * @ORM\Column(name="status", type="string", length=255)
     * @Expose
     */
    protected $status = 'standby';

    /**
     *
     * @ORM\Column(name="mail_status", type="string", length=255)
     * @Expose
     */
    protected $mailStatus = 'standby';

    /**
     *
     * @ORM\Column(name="last_name",type="string", length=255,nullable = true)
     * @Expose
     */
    protected $lastName = NULL;

    /**
     *
     * @ORM\Column(name="first_name",type="string", length=255,nullable = true)
     * @Expose
     */
    protected $firstName = NULL;
    
    /**
     * @var Contact $contact
     *
     * @ORM\OneToOne(targetEntity="Contact", inversedBy="user", cascade={"persist", "merge"})
     * @ORM\JoinColumn(name="id_contact", referencedColumnName="id")
     * @Expose
     */
    private $contact;
    
    
    /**
     * @ORM\Column(name="created_date", type="datetime")
     * @var DateTime
     */
    protected $createdDate;
    
    /**
     * @var \Apr\ProgramBundle\Entity\Member $member
     * 
     * @ORM\OneToOne(targetEntity="\Apr\ProgramBundle\Entity\Member", mappedBy="user")
     * @Expose
     */
    protected $member=NULL;
    
    public function __construct()
    {
        parent::__construct();
        $this->createdDate = Tools::DateTime('now');
        $tokenGenerator = new \FOS\UserBundle\Util\TokenGenerator();
        $this->setConfirmationToken($tokenGenerator->generateToken());
        $this->setEnabled(true);
    }
    
    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getMailStatus()
    {
        return $this->mailStatus;
    }

    public function setMailStatus($mailStatus)
    {
        $this->mailStatus = $mailStatus;
        return $this;
    }

    
    
    public function setCreatedDate(\DateTime $createdDate)
    {
        $this->createdDate = $createdDate;
    }

    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    
    /**
     * Set salt
     * 
     * @param $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }
    
    
    public function setContact(Contact $contact)
    {
        $this->contact = $contact;
    }

    public function getContact()
    {
        return $this->contact;
    }
    
    public function getMember()
    {
        return $this->member;
    }
    
    public function setmember(\Apr\ProgramBundle\Entity\Member $member)
    {
        $this->member = $member;
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
}