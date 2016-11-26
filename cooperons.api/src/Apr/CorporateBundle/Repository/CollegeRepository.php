<?php

namespace Apr\CorporateBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CollegeRepository extends EntityRepository
{

    public function getAllCollegesByCorporate($corporate, $excludedCollege = null, $includeStandBy = false){
        $query= $this->createQueryBuilder('c');

        if(!$includeStandBy) {
            // Par défaut - n'affiche que confirmés - sauf pour le delegate / administrator ...
            $query
                ->andWhere($query->expr()->isNotNull('c.lastConfirmDate'));
        }

        $query
            ->andWhere($query->expr()->isNull('c.leaveDate'))
            ->andWhere($query->expr()->eq('c.corporate', ':corporate'))
            ->setParameter('corporate', $corporate);


        if(!is_null($excludedCollege)) {
            $query
                ->andWhere($query->expr()->neq('c.id', ':excludedId'))
                ->setParameter('excludedId', $excludedCollege->getId());
        }

        return $query->getQuery()->getResult();
    }

}

?>
