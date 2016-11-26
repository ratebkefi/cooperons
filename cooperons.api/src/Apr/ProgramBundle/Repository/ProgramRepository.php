<?php

namespace Apr\ProgramBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ProgramRepository extends EntityRepository
{

    /**
     * @return boolean.
     */
    public function countActiveProgramsAsAdministrator($member){
        $query= $this->createQueryBuilder('p');
        $query
            ->select('count(p.id)')
            ->leftJoin('p.corporate','cp')
            ->leftJoin('cl.corporate','cp')
            ->leftJoin('cp.party','pt')
            ->leftJoin('pt.administrator','a')
            ->andWhere('a.member = :member')
            ->andWhere('p.status != :standby')
            ->setParameter('member', $member)
            ->setParameter('standby', 'standby');

        return $query->getQuery()->getSingleScalarResult();
    }
}

?>
