<?php

namespace Apr\ContractBundle\Repository;

use Doctrine\ORM\EntityRepository;
use \Apr\ContractBundle\Entity\Party;

class ContractRepository extends EntityRepository
{
    // A copier dans Entity/Contract & Manager
    private $arrTypes = array(
        0 => 'default',
        1 => 'affair'
    );
    
    private function buildQuery($typeContract) {
        $query = $this->createQueryBuilder('c');
        $query
            ->andWhere($query->expr()->eq('c.type', ':typeContract'))
            ->setParameter('typeContract', array_search($typeContract, $this->arrTypes));
        
        return $query;        
    }

    public function getAllContracts(Party $party, $filterContract, $collaborator){
        $arr = explode(":", $filterContract);
        $strOwnerClient = $arr[1];

        $query = $this->buildQuery($arr[0]);
        if(!is_null($collaborator)) {
            $query
                ->leftJoin('c.invitation','i')
                ->andWhere($query->expr()->orX(
                    $query->expr()->eq('c.'.$strOwnerClient.'Collaborator', ':collaborator'),
                    $query->expr()->andX(
                        $query->expr()->isNotNull('i.id'),
                        $query->expr()->eq('i.infos', ':filterContract'),
                        $query->expr()->eq('i.collaborator', ':collaborator')
                    )))
                ->setParameter('filterContract', $filterContract)
                ->setParameter('collaborator', $collaborator);
        } else {
            $query
                ->andWhere($query->expr()->eq('c.'.$strOwnerClient, ':party'))
                ->setParameter('party', $party);
        }

        return $query->getQuery()->getResult();
    }
    
    public function getContractBetweenParties(Party $owner, Party $client, $type) {
        $query = $this->buildQuery($type);

        $query
            ->andWhere($query->expr()->eq('c.owner', ':owner'))
            ->setParameter('owner', $owner)
            ->andWhere($query->expr()->eq('c.client', ':client'))
            ->setParameter('client', $client);

        return $query->getQuery()->getResult();
    }
}

?>
