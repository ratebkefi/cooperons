<?php

namespace Apr\ProgramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */

class PreProdCommission extends CommissionBase
{
    /**
     * @ORM\ManyToOne(targetEntity="PreProdAffair", inversedBy="allCommissions", cascade = {"persist"})
     * @ORM\JoinColumn(name="affair_id", referencedColumnName="id")
     */
    protected $affair;

}