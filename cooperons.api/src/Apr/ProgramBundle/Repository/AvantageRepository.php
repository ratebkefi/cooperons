<?php

namespace Apr\ProgramBundle\Repository;

use Doctrine\ORM\EntityRepository;

class AvantageRepository extends EntityRepository
{

    public function getAvantagesForAttestations($year, $corporate = null){
        $query= $this->createQueryBuilder('a');

        $query
            ->leftJoin('a.member', 'm')
            ->addOrderBy('m.id', 'ASC')
            ->leftJoin('a.corporate', 'c')
            ->addOrderBy('c.id', 'ASC')
            ->andWhere($query->expr()->isNotNull('a.giftOrder'))
            ->leftJoin('a.giftOrder', 'g')
            ->addOrderBy('g.year', 'ASC')
            ->addOrderBy('g.quarter', 'ASC');

        if(!is_null($corporate)) {
            // Recherche avec Corporate => Administrator ...
            $query
                ->andWhere($query->expr()->eq('a.corporate', ':corporate'))
                ->setParameter('corporate', $corporate);
        } else {
            $query
                ->andWhere($query->expr()->isNotNull('a.corporate'))
                ->andWhere($query->expr()->isNull('a.attestation'));
        }

        if(!is_null($year)) {
            $query
                // On récupère le passé au cas où ...
                ->andWhere($query->expr()->lte('g.year', ':year'))
                ->setParameter('year', $year);
        }

        return $query->getQuery()->getResult();
    }
}

?>
