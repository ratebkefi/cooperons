<?php
namespace Apr\ProgramBundle\Manager;

use Doctrine\ORM\EntityManager;
use Apr\UserBundle\Manager\BaseManager as BaseManager;
use Apr\ProgramBundle\Entity\OperationCredit;

class OperationCreditManager extends BaseManager {
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function getOperationById($idOperation){
        return $this->getRepository()->find($idOperation);
    }
    
    public function getOperationByLabel($program, $labelOperation){
        return $this->getRepository()->findOneBy(array('program' => $program, 'label' => $labelOperation));
    }
    
    public function getRepository()
    {
        return $this->em->getRepository('AprProgramBundle:OperationCredit');
    }
    
    public function searchOperations($search, $idProgram)
    {
        return $this->getRepository()->searchOperation($search, $idProgram);
    }
    public function addOperation($program, $label, $amount, $type){
        $operation = $this->getOperationByLabel($program, $label);
        if($operation){
            return false;
        }
        $operation = new OperationCredit($program);
        $operation->setLabel($label);
        $operation->setDefaultAmount($amount);
        if($type == 'multipoints'){
            $operation->setMulti(true);
        }else{
            $operation->setMulti(false);
        }
        $this->persistAndFlush($operation);
        return $operation;
    }

    public function updateOperation($id, $label, $amount){
        $operation = $this->getOperationById($id);
        if(!$operation){
            return false;
        }
        $operation->setLabel($label);
        $operation->setDefaultAmount($amount);
        $this->persistAndFlush($operation);
        return $operation;
    }

}
?>
