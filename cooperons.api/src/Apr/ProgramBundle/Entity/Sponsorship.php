<?php

namespace Apr\ProgramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Apr\ProgramBundle\Repository\SponsorshipRepository")
 */

class Sponsorship extends SponsorshipBase
{
    /**
     * @var ParticipatesTo $sponsor
     *
     * @ORM\ManyToOne(targetEntity="ParticipatesTo")
     * @ORM\JoinColumn(name="king_id", referencedColumnName="id")
     */
    protected $king;

    /**
     * @var ParticipatesTo $sponsor
     *
     * @ORM\ManyToOne(targetEntity="ParticipatesTo")
     * @ORM\JoinColumn(name="sponsor_id", referencedColumnName="id")
     */
    protected $sponsor;

    /**
     * @var ParticipatesTo $affiliate
     *
     * @ORM\OneToOne(targetEntity="ParticipatesTo", inversedBy="sponsorship", cascade = {"persist"})
     * @ORM\JoinColumn(name="affiliate_id", referencedColumnName="id")
     */
    protected $affiliate;

}