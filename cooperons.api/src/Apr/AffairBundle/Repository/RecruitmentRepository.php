<?php

namespace Apr\AffairBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Apr\ContractBundle\Entity\Party;

class RecruitmentRepository extends EntityRepository
{

    public function getAllRecruitments(Party $recruiteeParty, Party $clientParty){
        $query = $this->createQueryBuilder('r');

        $query
            ->leftJoin('r.recruiterCorpContract','rer')
            ->andWhere($query->expr()->eq('rer.client', ':clientParty'))
            ->setParameter('clientParty', $clientParty)

            ->leftJoin('r.recruiteeCorpContract','ree')
            ->andWhere($query->expr()->eq('ree.owner', ':recruiteeParty'))
            ->setParameter('recruiteeParty', $recruiteeParty)

            ->andWhere($query->expr()->eq('r.isExpired', ':isExpired'))
            ->setParameter('isExpired', false);

        return $query->getQuery()->getResult();
    }

    public function getOldRecruitment($contract) {
        $owner = $contract->getOwner() ;
        $client = $contract->getClient();

        if(!is_null($owner) && !is_null($client)) {
            $query= $this->createQueryBuilder('r');
            $query
                ->leftJoin('r.recruiterCorpContract','corp')
                ->andWhere($query->expr()->eq('corp.client', ':client'))
                ->setParameter('client', $client)
                ->leftJoin('r.recruitmentSettings','settings')
                ->leftJoin('settings.legalDocument','legalDocument')
                ->leftJoin('legalDocument.contract','recrut')
                ->andWhere($query->expr()->eq('recrut.client', ':owner'))
                ->setParameter('owner', $owner)
                ->andWhere($query->expr()->eq('r.isExpired', ':isExpired'))
                ->setParameter('isExpired', false);

            $result = $query->getQuery()->getResult();
            if(count($result)) return $result[0];
        }

    }
}

?>
