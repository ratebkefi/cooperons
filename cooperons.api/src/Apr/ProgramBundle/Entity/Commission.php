<?php

namespace Apr\ProgramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */

class Commission extends CommissionBase
{
    /**
     * @ORM\ManyToOne(targetEntity="Affair", inversedBy="allCommissions", cascade = {"persist"})
     * @ORM\JoinColumn(name="affair_id", referencedColumnName="id")
     */
    protected $affair;

}