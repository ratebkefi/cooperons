<?php

namespace Apr\ProgramBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
/**
 * MemberRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MemberRepository extends EntityRepository
{

    /**
     * Check if Member already exists with email: if User, checks also in User ...
     * @param array $params search parameters for grid
     * @return Array.
     */
    public function retrieveByEmail($email) {

        //Begin build query
        $query = $this->createQueryBuilder('m');

        if (is_string($email) && !empty($email)) {
            $query->leftJoin('m.user','s');
            $query->where('m.email = :email');
            $query->orWhere('s.email = :email');
            $query->setParameter('email', $email);
        }
        // Get result
        $result = $query->getQuery()->getResult();
        if($result) return $result[0];
    }

    /**
     * Check if Member already exists with email: if User, checks also in User ...
     * @param array $params search parameters for grid
     * @return Array.
     */
    public function getMembersWithGiftsPending() {

        //Begin build query
        $query = $this->createQueryBuilder('m');

        $query
            ->leftJoin('m.lastAvantage','a')
            ->andWhere($query->expr()->eq('m.isPreProd', ':isPreProd'))
            ->setParameter('isPreProd', false)
            ->andWhere($query->expr()->isNotNull('m.user'))
            ->leftJoin('m.autoEntrepreneur','ae')
            ->andWhere($query->expr()->isNull('ae.id'))
            ->andWhere($query->expr()->gt('m.remainingPoints', ':minPoints'))
            ->setParameter('minPoints', 0);


        // Get result
        return $query->getQuery()->getResult();
    }

}

?>