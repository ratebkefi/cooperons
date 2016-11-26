<?php

namespace Apr\MandataireBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PaymentRepository extends EntityRepository
{


    public function checkCBPayment($mandataire, $authCode){

        $arr = explode("#", $authCode, 2);
        $timestamp = $arr[0];

        $query= $this->createQueryBuilder('p');
        $query
            ->select('count(p.id)')
            ->andWhere('p.mandataire = :mandataire')
            ->andWhere('p.mode = :mode')
            ->andWhere(
                $query->expr()->like('p.authCode', ':authCode')
            )
            ->setParameter('mandataire', $mandataire)
            ->setParameter('mode', 'CB')
            ->setParameter('authCode', $timestamp."#%");

        $result = $query->getQuery()->getSingleScalarResult();

        return $result == 0;
    }

}

?>
