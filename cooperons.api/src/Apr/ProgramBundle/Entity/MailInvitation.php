<?php
namespace Apr\ProgramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation\Exclude;
use Apr\CoreBundle\Tools\Tools;

/**
 * @ORM\Entity
 * @ORM\Table(name="mail_invitations")
 */

class MailInvitation
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(name="code", type="string", length=100)
     * 
     */
    private $codeMail = 'default';
    
    /**
     * @var Program $program
     *
     * @ORM\ManyToOne(targetEntity="Program", inversedBy="allMailInvitations", cascade = {"persist"})
     * @ORM\JoinColumn(name="program_id", referencedColumnName="id")
     * @Exclude
     * 
     */
    private $program;
    
    /**
     * @ORM\Column(name="subject", type="string", length=255, nullable=true)
     * 
     */
    private $subject;
    
    /**
     * @ORM\Column(name="content", type="text", nullable=true)
     * 
     */
     private $content;
     
      /**
     * @ORM\Column(name="header", type="text", nullable=true)
     * 
     */
     private $header;
     
      /**
     * @ORM\Column(name="footer", type="text", nullable=true)
     * 
     */
     private $footer;

    /**
     * @ORM\Column(name="signature", type="text", nullable=true)
     *
     */
    private $signature;

    /**
     * @ORM\Column(name="created_date", type="datetime")
     * @var DateTime
     */
    protected $createdDate;

    /**
     * @ORM\OneToMany(targetEntity="ParticipatesTo", mappedBy="mailInvitation")
     * @Exclude
     */
    private $allParticipatesTo;

    /**
     * @ORM\OneToMany(targetEntity="PreProdParticipatesTo", mappedBy="mailInvitation")
     * @Exclude
     */
    private $allPreProdParticipatesTo;

    public function __construct($program)
    {
        $this->createdDate = Tools::DateTime();
        $this->program = $program;
    }
    
    public function getId()
    {
        return $this->id;
    }

    // Utilisé pour clone Program ...
    public function setProgram($program)
    {
        $this->program = $program;
    }

    public function getProgram()
    {
        return $this->program;
    }

    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    public function getSubject()
    {
        return $this->subject;
    }
    
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }
    
    public function getContent()
    {
        return $this->content;
    }
    
    public function setContent($content)
    {
        $this->content = $content;
    }
    
    public function getCodeMail()
    {
        return $this->codeMail;
    }
    
    public function setCodeMail($codeMail)
    {
        $this->codeMail = $codeMail;
    }
    
    public function getHeader()
    {
        return $this->header;
    }
    
    public function setHeader($header)
    {
        $this->header = $header;
    }
    
    public function getFooter()
    {
        return $this->footer;
    }
    
    public function setFooter($footer)
    {
        $this->footer = $footer;
    }

    public function getSignature()
    {
        return $this->signature;
    }

    public function setSignature($signature)
    {
        $this->signature = $signature;
    }

    public function getAllParticipatesTo()
    {
        $status = $this->getProgram()->getStatus();
        if ($status == 'prod') {
            return $this->allParticipatesTo;
        } elseif ($status == 'preprod') {
            return $this->allPreProdParticipatesTo;
        }
    }

}