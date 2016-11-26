<?php

namespace Apr\UserBundle\Manager;

abstract class BaseManager
{

    public function persistAndFlush($entity)
    {
        $this->persist($entity);
        $this->flush();
    }

    public function removeAndFlush($entity)
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    public function persist($entity)
    {
        $this->em->persist($entity);
    }

    public function flush()
    {
        $this->em->flush();
    }

    abstract protected function getRepository();
}

?>
