<?php

namespace Apr\ProgramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Apr\ProgramBundle\Repository\SponsorshipRepository")
 */

class PreProdSponsorship extends SponsorshipBase
{
    /**
     * @var PreProdParticipatesTo $sponsor
     *
     * @ORM\ManyToOne(targetEntity="PreProdParticipatesTo" )
     * @ORM\JoinColumn(name="king_id", referencedColumnName="id")
     */
    protected $king;

    /**
     * @var PreProdParticipatesTo $sponsor
     *
     * @ORM\ManyToOne(targetEntity="PreProdParticipatesTo")
     * @ORM\JoinColumn(name="sponsor_id", referencedColumnName="id")
     */
    protected $sponsor;

    /**
     * @var PreProdParticipatesTo $affiliate
     *
     * @ORM\OneToOne(targetEntity="PreProdParticipatesTo", inversedBy="sponsorship", cascade = {"persist"})
     * @ORM\JoinColumn(name="affiliate_id", referencedColumnName="id")
     */
    protected $affiliate;

}