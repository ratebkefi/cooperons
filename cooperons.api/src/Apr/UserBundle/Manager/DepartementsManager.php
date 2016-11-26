<?php

namespace Apr\UserBundle\Manager;

use Doctrine\ORM\EntityManager;
use Apr\UserBundle\Entity\Departement;

class DepartementsManager extends BaseManager
{

    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function loadDepartement($departementId)
    {
        return $this->getRepository()
                        ->findOneBy(array('id' => $departementId));
    }

    /**
     * Save Departement entity
     *
     * @param Departement $departement 
     */
    public function saveDepartement(Departement $departement)
    {
        $this->persistAndFlush($departement);
    }

    public function getRepository()
    {
        return $this->em->getRepository('AprUserBundle:Departement');
    }

}

?>
