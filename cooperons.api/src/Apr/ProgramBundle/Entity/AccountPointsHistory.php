<?php

namespace Apr\ProgramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Apr\ProgramBundle\Repository\AccountPointsHistoryRepository")
 */

class AccountPointsHistory extends AccountPointsHistoryBase
{
    /**
     * @ORM\ManyToOne(targetEntity="ParticipatesTo", inversedBy = "allAccountPointsHistory", cascade = {"persist"})
     * @ORM\JoinColumn(name="participates_to_id", referencedColumnName="id")
     */
    protected $participatesTo;

    /**
     * @ORM\ManyToOne(targetEntity="AccountPointsHistory", cascade = {"persist"})
     * @ORM\JoinColumn(name="affiliate_history_id", referencedColumnName="id")
     */
    protected $affiliateHistory;

}
