<?php

namespace Apr\ProgramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;

/**
 * @ORM\Entity(repositoryClass="Apr\ProgramBundle\Repository\ParticipatesToRepository")
 */
class ParticipatesTo extends ParticipatesToBase {

    /**
     * @var \Apr\UserBundle\Entity\Token $tokenObject
     * @ORM\OneToOne(targetEntity="\Apr\UserBundle\Entity\Token", inversedBy="participatesTo", cascade = {"all"})
     * @ORM\JoinColumn(name="token", referencedColumnName="value")
     * @Exclude
     */
    protected $tokenObject;

    /**
     * @ORM\ManyToOne(targetEntity="Member", inversedBy="allParticipatesTo", cascade = {"persist"})
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id")
     */
    protected $member;

    /**
     * @var MailInvitation $mailInvitation
     *
     * @ORM\ManyToOne(targetEntity="MailInvitation", inversedBy="allParticipatesTo", cascade = {"persist"})
     * @ORM\JoinColumn(name="mail_invitation_id", referencedColumnName="id", nullable=true)
     * @Exclude
     */
    protected $mailInvitation;

    /**
     * @ORM\OneToMany(targetEntity="Sponsorship", mappedBy="sponsor")
     * @Exclude
     */
    protected $allSponsorshipsAsSponsor;

    /**
     * @ORM\OneToOne(targetEntity="Sponsorship", cascade = {"persist"})
     * @ORM\JoinColumn(name="sponsorship_id", referencedColumnName="id", nullable=true)
     * @Exclude
     */
    protected $sponsorship;

    /**
     * @ORM\OneToMany(targetEntity="PreProdInvitation", mappedBy="sponsor")
     * @Exclude
     */
    protected $allInvitations;

    /**
     * @ORM\OneToMany(targetEntity="AccountPointsHistory", mappedBy="participatesTo")
     * @Exclude
     */
    protected $allAccountPointsHistory;

    // Easy

    /**
     * @ORM\OneToMany(targetEntity="Affair", mappedBy="participatesTo")
     * @Exclude
     */
    protected $allAffairs;
}
