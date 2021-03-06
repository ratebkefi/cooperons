<?php

namespace Apr\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * HistoriqueRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository {

    /**
     * Create query find users for ajax grid, return users
     * @param array $params search parameters for grid 
     * @return Array.
     */
    public function search($search = NULL) {

        //Begin build query 
        $query = $this->createQueryBuilder('u');
        
        //$query->andWhere('u INSTANCE OF \Apr\UserBundle\Entity\User');
        
        if (is_string($search) && !empty($search)) {
            $query->leftJoin('u.contact','c');
            $query->andWhere($query->expr()->like('lower(u.username)', '?1'));
            $query->orWhere($query->expr()->like('lower(u.firstName)', '?1'));
            $query->orWhere($query->expr()->like('lower(u.lastName)', '?1'));
            $query->orWhere($query->expr()->like('c.phone', '?1'));
            $query->setParameter('1', '%' . strtolower($search) . '%');
        }
        // Get result
        return $query->getQuery()->getResult();
    }
    
    /**
     * 
     * Query find user by api_key
     * 
     *@param string $apiKey 
     * @return Array
     */
     
    public function loadByApiKey($apiKey){
        $query = $this->createQueryBuilder('u');
        $query->where('u.apiKey = :api_key');
        $query->setParameter('api_key', $apiKey);
        // Get result
        return $query->setMaxResults('1')->getQuery()->getResult();
        
    }

}

?>
