<?php

namespace Apr\CorporateBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CorporateRepository extends EntityRepository
{

    public function getAllCorporates($activeOnly = false, $tvaIntracomm = null, $administratorMember = null){
        $query= $this->createQueryBuilder('c');

        if(!is_null($tvaIntracomm)) {
            $query
                ->andWhere($query->expr()->eq('c.tvaIntracomm', ':tvaIntracomm'))
                ->setParameter('tvaIntracomm', $tvaIntracomm)
                ->setMaxResults(1);
        }

        if(!is_null($administratorMember)) {
            $query
                ->leftJoin('c.party','p')
                ->leftJoin('p.administrator','a')
                ->andWhere($query->expr()->eq('a.member', ':member'))
                ->setParameter('member', $administratorMember);
        } elseif($activeOnly) {
            $query
                ->leftJoin('c.party','p')
                ->andWhere($query->expr()->isNotNull('p.administrator'));
        }

        if(!is_null($tvaIntracomm)) {
            return $query->getQuery()->getOneOrNullResult();
        } else {
            return $query->getQuery()->getResult();
        }
    }
    
    public function getCorporateBySiren($siren, $activeOnly) {
        $query = $this->createQueryBuilder('c');
        $query
            ->andWhere($query->expr()->like('c.tvaIntracomm)', ':tvaIntracomm'))
            ->setParameter('tvaIntracomm', 'FR'.$siren);

        if($activeOnly) {
            $query
                ->leftJoin('c.party','p')
                ->andWhere($query->expr()->isNotNull('p.administrator'));
        }
        
        return $query->getQuery()->getOneOrNullResult();
    }
}

?>
