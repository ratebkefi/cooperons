<?php

namespace Apr\ProgramBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * SponsorshipRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SponsorshipRepository extends EntityRepository
{

    public function getSponsorship($program, $sponsor, $affiliate)
    {
        return $this->findOneBy(array('program' => $program, 'sponsor' => $sponsor, 'affiliate' => $affiliate));
    }


    public function getAllSponsorships($member){
        $query= $this->createQueryBuilder('s');
        $query
            ->leftJoin('s.sponsor','p')
            ->andWhere($query->expr()->eq('p.member', ':member'))
            ->setParameter('member', $member);

        return $query->getQuery()->getResult();
    }


}

?>
