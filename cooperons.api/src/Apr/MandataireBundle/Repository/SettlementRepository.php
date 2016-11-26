<?php

namespace Apr\MandataireBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Apr\CoreBundle\Tools\Tools;

class SettlementRepository extends EntityRepository
{

    private $arrStatus = array(
        0 => 'waitingForNotify',
        1 => 'waitingForPayment',
        2 => 'settled',
        3 => 'released');

    private $arrInvoicingFrequency = array(
        0 => 'auto',
        1 => 'week',
        2 => 'month',
        3 => 'quarter'
    );

    public function getFilteredSettlements($mandataire, $status, $isProgram)
    {
        $query = $this->createQueryBuilder('s');

        if (!is_null($mandataire)) {
            $query
                ->andWhere('s.mandataire = :mandataire')
                ->setParameter('mandataire', $mandataire);
        } elseif ($isProgram) {
            $query->andWhere($query->expr()->in('s.type', ':arrType'))
                ->setParameter('arrType', array('abonnement', 'points'));
        }

        if ($status) {
            if ($status == 'waiting') {
                $query->andWhere($query->expr()->in('s.status', ':arrStatus'))
                    ->setParameter('arrStatus', array(0, 1));
            } else {
                $query
                    ->andWhere('s.status = :status')
                    ->setParameter('status', array_search($status, $this->arrStatus));
            }
        }

        $query
            ->addOrderBy('s.priority', 'DESC')
            ->addOrderBy('s.createdDate', 'DESC');

        return $query->getQuery()->getResult();
    }

    public function getSettlementsForInvoicing($mandataire, $strFrequency, $cutOffDate)
    {
        $query = $this->createQueryBuilder('s');

        if (is_null($cutOffDate) && !is_null($mandataire)) $cutOffDate = $mandataire->getCutOffDate();

        $query
            ->leftJoin('s.invoice', 'i')
            ->andWhere($query->expr()->isNull('i.id'))
            ->andWhere($query->expr()->isNotNull('s.validatedDate'))
            ->andWhere($query->expr()->lt('s.validatedDate', ':cutOffDate'))
            ->setParameter('cutOffDate', $cutOffDate, \Doctrine\DBAL\Types\Type::DATETIME);

        if (is_null($mandataire)) {
            $query
                ->leftJoin('s.mandataire', 'm')
                ->andWhere('m.invoicingFrequency = :invoicingFrequency')
                ->setParameter('invoicingFrequency', array_search($strFrequency, $this->arrInvoicingFrequency));
        } else {
            $query
                ->andWhere('s.mandataire = :mandataire')
                ->setParameter('mandataire', $mandataire);
        }

        $query
            ->addOrderBy('s.priority', 'DESC')
            ->addOrderBy('s.createdDate', 'DESC');

        return $query->getQuery()->getResult();
    }

    public function getTotalSettlementsForQuarterlyTaxation($autoEntrepreneur, $current) {
        $query = $this->createQueryBuilder('s');

        $cutOffDate = Tools::firstDayOf('quarter');
        if($current) $cutOffDate->modify('+3 month');

        $query
            ->leftJoin('s.mandataire', 'm')
            ->andWhere($query->expr()->isNotNull('s.validatedDate'))
            ->andWhere($query->expr()->lt('s.validatedDate', ':cutOffDate'))
            ->setParameter('cutOffDate', $cutOffDate, \Doctrine\DBAL\Types\Type::DATETIME);

        if(!is_null($autoEntrepreneur)) {
            $query
                ->select('SUM(s.amount)')
                ->andWhere('m.owner = :owner')
                ->setParameter('owner', $autoEntrepreneur->getParty());
            $lastQuarterlyTaxation = $autoEntrepreneur->getLastQuarterlyTaxation();
            if($lastQuarterlyTaxation) {
                $query
                    ->andWhere($query->expr()->gte('s.validatedDate', ':startDate'))
                    ->setParameter('startDate', $lastQuarterlyTaxation->getCutOffDate(), \Doctrine\DBAL\Types\Type::DATETIME)
                    ->addOrderBy('s.priority', 'DESC')
                    ->addOrderBy('s.createdDate', 'DESC');
            }
            return $query->getQuery()->getSingleScalarResult();
        } else {
            $query
                ->leftJoin('m.owner', 'o')
                ->leftJoin('o.autoEntrepreneur', 'ae')
                ->andWhere(
                    $query->expr()->orX(
                        $query->expr()->lt('ae.lastQuarterlyDeclarationDate', ':cutOffDate'),
                        $query->expr()->isNull('ae.lastQuarterlyDeclarationDate')
                    ))
                ->leftJoin('ae.lastQuarterlyTaxation', 'qt')
                ->andWhere(
                    $query->expr()->orX(
                        $query->expr()->gte('s.validatedDate', 'qt.cutOffDate'),
                        $query->expr()->isNull('qt.id')
                    ))
                ->addOrderBy('ae.id', 'ASC')
                ->addOrderBy('s.priority', 'DESC')
                ->addOrderBy('s.createdDate', 'DESC');
            return $query->getQuery()->getResult();
        }
    }

    public function getSettlementsForMandataireFee($partyCooperons)
    {
        $query = $this->createQueryBuilder('s');

        $query
            ->leftJoin('s.settlementFee', 'sf')
            ->andWhere($query->expr()->isNull('sf.id'))
            ->leftJoin('s.avantage', 'a')
            ->andWhere($query->expr()->isNull('a.id'))
            ->andWhere($query->expr()->isNotNull('s.validatedDate'))
            ->leftJoin('s.mandataire', 'm')
            ->leftJoin('m.owner', 'o')
            ->andWhere($query->expr()->neq('o', ':partyCooperons'))
            ->setParameter('partyCooperons', $partyCooperons);


        $query
            ->addOrderBy('o.id', 'DESC')
            ->addOrderBy('s.amountHt', 'DESC');

        return $query->getQuery()->getResult();
    }
}