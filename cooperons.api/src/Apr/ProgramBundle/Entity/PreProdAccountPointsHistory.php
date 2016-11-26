<?php

namespace Apr\ProgramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Apr\ProgramBundle\Repository\AccountPointsHistoryRepository")
 */

class PreProdAccountPointsHistory extends AccountPointsHistoryBase
{
    /**
     * En PreProd: cascade remove non nécessaire car remove déclenché au niveau ParticipatesTo ...
     *
     * @ORM\ManyToOne(targetEntity="PreProdParticipatesTo", inversedBy = "allAccountPointsHistory", cascade = {"persist"})
     * @ORM\JoinColumn(name="participates_to_id", referencedColumnName="id")
     */
    protected $participatesTo;

    /**
     * En PreProd: cascade remove: cascade remove non nécessaire car remove déclenché au niveau ParticipatesTo  ...
     *
     * @ORM\ManyToOne(targetEntity="PreProdAccountPointsHistory", cascade = {"persist"})
     * @ORM\JoinColumn(name="affiliate_history_id", referencedColumnName="id")
     */
    protected $affiliateHistory;

}