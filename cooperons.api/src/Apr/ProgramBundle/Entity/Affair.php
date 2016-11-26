<?php

namespace Apr\ProgramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;

/**
 * @ORM\Entity
 */

class Affair extends AffairBase
{
    /**
     * @ORM\ManyToOne(targetEntity="ParticipatesTo")
     * @ORM\JoinColumn(name="participates_to_id", referencedColumnName="id")
     * @Exclude
     */
    protected $participatesTo;

    /**
     * @ORM\OneToMany(targetEntity="Commission", mappedBy="affair", cascade = {"persist", "remove"})
     * @Exclude
     */
    protected $allCommissions;

}