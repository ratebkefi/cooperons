<?php

namespace Apr\ContractBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PartyRepository extends EntityRepository
{
    public function getAllParties(){
        $query = $this->createQueryBuilder('p');
        $query
            ->leftJoin('p.autoEntrepreneur', 'a')
            ->leftJoin('p.corporate', 'c')
            ->leftJoin('p.mandataire', 'm')
            ->andWhere($query->expr()->orX(
                $query->expr()->isNotNull('a.id'),
                $query->expr()->isNotNull('c.id')
            ))
            ->andWhere($query->expr()->gt('m.depot', 0))
            ->andWhere($query->expr()->isNull('m.liquidationDate'));

        return $query->getQuery()->getResult();
    }

}

?>
