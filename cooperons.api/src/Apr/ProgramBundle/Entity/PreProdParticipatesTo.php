<?php

namespace Apr\ProgramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;


/**
 * @ORM\Entity(repositoryClass="Apr\ProgramBundle\Repository\ParticipatesToRepository")
 */

class PreProdParticipatesTo extends ParticipatesToBase
{
    /**
     * @var \Apr\UserBundle\Entity\Token $tokenObject
     * @ORM\OneToOne(targetEntity="\Apr\UserBundle\Entity\Token", inversedBy="preProdParticipatesTo", cascade = {"all"})
     * @ORM\JoinColumn(name="token", referencedColumnName="value")
     * @Exclude
     */
    protected $tokenObject;

    /**
     * @ORM\ManyToOne(targetEntity="Member", inversedBy="allPreProdParticipatesTo", cascade = {"persist"})
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id")
     */
    protected $member;

    /**
     * @ORM\ManyToOne(targetEntity="Program", inversedBy="allPreProdParticipatesTo", cascade = {"persist"})
     * @ORM\JoinColumn(name="program_id", referencedColumnName="id")
     * @Exclude
     */
    protected $program;

    /**
     * @var MailInvitation $mailInvitation
     *
     * @ORM\ManyToOne(targetEntity="MailInvitation", inversedBy="allPreProdParticipatesTo", cascade = {"persist"})
     * @ORM\JoinColumn(name="mail_invitation_id", referencedColumnName="id", nullable=true)
     * @Exclude
     */

    protected $mailInvitation;

    /**
     * @ORM\OneToMany(targetEntity="PreProdSponsorship", mappedBy="sponsor")
     * @Exclude
     */
    protected $allSponsorshipsAsSponsor;

    /**
     * @ORM\OneToOne(targetEntity="PreProdSponsorship", cascade = {"persist"})
     * @ORM\JoinColumn(name="sponsorship_id", referencedColumnName="id", nullable=true)
     * @Exclude
     */
    protected $sponsorship;

    /**
     *
     * En PreProd: suppression des invitations en cascade ...
     *
     * @ORM\OneToMany(targetEntity="PreProdInvitation", mappedBy="sponsor", cascade = {"remove"})
     * @Exclude
     */
    protected $allInvitations;

    /**
     * @ORM\OneToMany(targetEntity="PreProdAccountPointsHistory", mappedBy="participatesTo")
     * @Exclude
     */
    protected $allAccountPointsHistory;

    // Easy

    /**
     * @ORM\OneToMany(targetEntity="PreProdAffair", mappedBy="participatesTo")
     * @Exclude
     */
    protected $allAffairs;

}