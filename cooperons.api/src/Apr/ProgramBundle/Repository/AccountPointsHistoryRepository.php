<?php

namespace Apr\ProgramBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * AccountPointsHistoryRepository
 *
 */
class AccountPointsHistoryRepository extends EntityRepository
{

    public function getAccountPointsHistory($member = null, $program = null, $isMulti = null){
        $query= $this->createQueryBuilder('a');

        if(!is_null($member)) {
            $query
                ->leftJoin('a.participatesTo', 'p')
                ->andWhere($query->expr()->eq('p.member', ':member'))
                ->setParameter('member', $member);
        } else {
            // Pour l'affichage de l'historique des commandes de Points ...
            $query
                ->leftJoin('a.settlement', 's')
                ->addOrderBy('s.id', 'DESC');
        }
        if(!is_null($program)) {
            $query
                ->andWhere($query->expr()->eq('a.program', ':program'))
                ->setParameter('program', $program);
        }
        if(!is_null($isMulti)) {
            $query
                ->andWhere($query->expr()->eq('a.isMulti', ':isMulti'))
                ->setParameter('isMulti', $isMulti);
        }

        return $query->getQuery()->getResult();
    }
}

?>
