<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Machine;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;

class InValueRepository extends EntityRepository
{
    /**
     * @return integer
     */
    public function valueOld(Machine $machine,\DateTime $date, \DateTime $date_old)
    {
        return $this
            ->createQueryBuilder('i')
            ->where('i.date >= :date_start')
            ->andWhere('i.date  <= :date_end')
            ->andWhere('i.machine = :machine')
            ->setParameter('machine', $machine)
            ->setParameter('date_end', $date)
            ->setParameter('date_start', $date_old)
            ->select('i.value')
            ->addOrderBy('i.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return integer
     */
    public function value(Machine $machine,\DateTime $date)
    {
        return $this
            ->createQueryBuilder('i')
            ->where('i.date = :date 
                     AND i.machine = :machine')
//            ->andWhere('i.machine = :machine')
            ->setParameter('machine', $machine)
            ->setParameter('date', $date)
            ->select('i.value')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return array
     */
    public function valuesPeriodic(Machine $machine,\DateTime $date, \DateTime $date_old)
    {
        return $this
            ->createQueryBuilder('i')
            ->where('i.date >= :date_start')
            ->andWhere('i.date  <= :date_end')
            ->andWhere('i.machine = :machine')
            ->setParameter('machine', $machine)
            ->setParameter('date_end', $date)
            ->setParameter('date_start', $date_old)
            ->select('i.value')
            ->getQuery()
            ->getArrayResult();
    }

    static public function createDateCriteria($date)
    {
        return Criteria::create()
            ->andWhere(Criteria::expr()->eq('date', $date));
    }
}