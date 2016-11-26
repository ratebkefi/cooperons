<?php

namespace Apr\UserBundle\Manager;

use Apr\CoreBundle\ApiException\ApiException;
use Doctrine\ORM\EntityManager;

class TokenManager extends BaseManager
{

    protected $em;
    protected $container;

    public function __construct(EntityManager $em, $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function getRepository()
    {
        return $this->em->getRepository('AprUserBundle:Token');
    }

    public function loadToken($value)
    {
        $tokenObject = $this->getRepository()->find($value);
        if(is_null($tokenObject)) throw new ApiException(400200);
        return $tokenObject;
    }

}

?>
