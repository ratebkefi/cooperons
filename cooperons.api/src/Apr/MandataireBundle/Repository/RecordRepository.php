<?php

namespace Apr\MandataireBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Apr\MandataireBundle\Entity\Party;

class RecordRepository extends EntityRepository
{


    public function getAllRecords(Party $party, $mandataire = null, $forInvoice = false, $cutOffDate = null){

        $query= $this->createQueryBuilder('r');
        $query
            ->andWhere('r.party = :party')
            ->setParameter('party', $party);

        if(!is_null($mandataire)) {
            $query
                ->andWhere($query->expr()->orX(
                    $query->expr()->eq('r.debitMandataire', ':mandataire'),
                    $query->expr()->eq('r.creditMandataire', ':mandataire')))
                ->setParameter('mandataire', $mandataire);
        }

        if($forInvoice) {
            if(is_null($cutOffDate)) $cutOffDate = $mandataire->getCutOffDate();

            $query
                ->leftJoin('r.invoice', 'i')
                ->andWhere($query->expr()->isNull('i.id'))
                ->andWhere($query->expr()->lt('r.createdDate', ':cutOffDate'))
                ->setParameter('cutOffDate', $cutOffDate, \Doctrine\DBAL\Types\Type::DATETIME);
        } else {
            $query
                ->orderBy('r.id', 'DESC');
        }

        return $query->getQuery()->getResult();
    }
}