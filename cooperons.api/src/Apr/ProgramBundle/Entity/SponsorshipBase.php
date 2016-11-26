<?php

namespace Apr\ProgramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Apr\UserBundle\Entity\User;
use Apr\CoreBundle\Tools\Tools;

/**
 * @ORM\Table(name="sponsorship")
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"prod" = "Sponsorship", "preprod" = "PreProdSponsorship"})
 */

abstract class SponsorshipBase
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var Program $program
     *
     * @ORM\ManyToOne(targetEntity="Program")
     * @ORM\JoinColumn(name="program_id", referencedColumnName="id")
     */
    private $program;

    /**
     * @var $upline
     *
     * @ORM\Column(name="upline" , type="text")
     */
    private $upline;

    /**
     * @ORM\Column(name="created_date", type="datetime")
     * @var DateTime
     */
    protected $createdDate;

    public function __construct($program, $sponsor, $affiliate)
    {
        $this->createdDate = Tools::DateTime('now');
        $this->program = $program;
        $this->king = $this->sponsor = $sponsor;
        $this->affiliate = $affiliate;
        $this->affiliate->setSponsorship($this);
        $this->sponsor->incrementCountAffiliates();
        $this->upline = "#".$sponsor->getMemberProgramId()."#";
        $this->updateKing();
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getProgram()
    {
        return $this->program;
    }

    public function getSponsor()
    {
        return $this->sponsor;
    }

    public function getAffiliate()
    {
        return $this->affiliate;
    }

    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    public function getMember()
    {
        return $this->affiliate->getMember();
    }
    
    public function getUpline()
    {
        // Format de l'upline commence et finit par # pour permettre la recherche au format #Id# ...
        return explode("#", substr($this->upline,1,-1));
    }

    /**
     *  Prend en entrée le $king après mise à jour et modifie $this
     */
    public function updateKing()
    {
        $king = $this->king->getKing();
        if($this->king != $king) {
            $this->king = $king;
            $upline = $this->getUpline();
            $kingUpline = $king->getUpline();
            $offsetKingUpline = array($this->king->getMemberProgramId());
            // Initialisation: l'upline commence par le MemberProgramId du nouveau King
            foreach($kingUpline as $id) array_push($offsetKingUpline, $id);
            foreach($offsetKingUpline as $id) array_push($upline, $id);
            $this->upline = "#".implode("#", $upline)."#";
        }
    }

    public function getKing()
    {
        return $this->king;
    }

}