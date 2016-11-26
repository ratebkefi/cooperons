<?php

namespace Apr\ProgramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */

class PreProdAffair extends AffairBase
{
    /**
     * @ORM\ManyToOne(targetEntity="PreProdParticipatesTo")
     * @ORM\JoinColumn(name="participates_to_id", referencedColumnName="id")
     */
    protected $participatesTo;

    /**
     * @ORM\OneToMany(targetEntity="PreProdCommission", mappedBy="affair", cascade = {"persist", "remove"})
     */
    protected $allCommissions;

}