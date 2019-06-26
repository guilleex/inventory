<?php

namespace AppBundle\Repository;

use AppBundle\Entity\MachineType;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;

class JackpotRepository extends EntityRepository
{
    /**
     * @param MachineType $machineType
     * @param \DateTime $date
     * @param \DateTime $date_old
     * @return array
     */
    public function valuesPeriodic(MachineType $machineType, \DateTime $date, \DateTime $date_old)
    {
        return $this->createQueryBuilder('i')
                    ->where('i.date > :date_start')
                    ->andWhere('i.date <= :date_end')
                    ->andWhere('i.machineType = :machineType')
                    ->setParameter('machineType', $machineType)
                    ->setParameter('date_end', $date)
                    ->setParameter('date_start', $date_old)
                    ->select('i.value')
                    ->getQuery()
                    ->getArrayResult();
//                    ->getSQL();
    }

    /**
     * @param $date
     * @return Criteria
     */
    static public function createDateCriteria($date)
    {
        return Criteria::create()
            ->andWhere(Criteria::expr()->eq('date', $date));
    }
}