<?php

namespace Apr\UserBundle\Repository;

use Apr\ProgramBundle\Entity\Program;
use Doctrine\ORM\EntityRepository;
use Apr\CoreBundle\Tools\Tools;

/**
 * InvitationRepository
 */

class InvitationRepository extends EntityRepository
{

    public function getInvitationsByMember($member, $old = false, $program = null)
    {

        if (is_null($member)) {
            return null;
        } else {
            $query = $this->createQueryBuilder('i');
            $query
                ->leftJoin('i.sponsor', 'p')
                ->andWhere($query->expr()->eq('p.member', ':member'))
                ->setParameter('member', $member);

            if (!is_null($program)) {
                $query
                    ->andWhere($query->expr()->eq('p.program', ':program'))
                    ->setParameter('program', $program);
            }

            if ($old) {
                $query
                    ->andWhere('i.dateValidate <= :now')
                    ->setParameter('now', Tools::DateTime(), \Doctrine\DBAL\Types\Type::DATETIME);
            }

            return $query->getQuery()->getResult();
        }
    }

    /**
     * Search invitations by program and label
     *
     * @author Fondative <dev devteam@fondative.com>
     * @param $program Program Invitations program
     * @param $search string Searching label
     * @return array
     */
    public function getInvitationsByProgram($program, $search)
    {
        $query = $this->createQueryBuilder('i');

        $query
            ->andWhere($query->expr()->eq('i.program', ':program'));
        if (is_string($search) && !empty($search)) {
            $query->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('lower(i.email)', '?1'),
                    $query->expr()->like('lower(i.firstName)', '?1'),
                    $query->expr()->like('lower(i.lastName)', '?1')))
                ->setParameter('1', '%' . strtolower($search) . '%');
        }
        $query->setParameter('program', $program);

        return $query->getQuery()->getResult();
    }

    /**
     * Create query find users for ajax grid, return users
     * @param array $params search parameters for grid
     * @return Array.
     */
    public function searchInvitations($program, $search)
    {
        if (is_string($search) && !empty($search)) {
            //Begin build query
            $query = $this->createQueryBuilder('i');

            $query
                ->andWhere($query->expr()->eq('i.program', ':program'))
                ->andWhere(
                    $query->expr()->orX(
                        $query->expr()->like('lower(i.email)', '?1'),
                        $query->expr()->like('lower(i.firstName)', '?1'),
                        $query->expr()->like('lower(i.lastName)', '?1')))
                ->setParameter('1', '%' . strtolower($search) . '%')
                ->setParameter('program', $program);

            // Get result
            return $query->getQuery()->getResult();
        }
    }

}

?>
