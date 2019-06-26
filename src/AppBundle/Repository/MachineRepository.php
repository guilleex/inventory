<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Machine;
use Doctrine\ORM\EntityRepository;

class MachineRepository extends EntityRepository
{
    public function countMachines()
    {
        return $this
            ->createQueryBuilder('m')
            ->select('count(m)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function allOrderByPosition()
    {
        return $this
            ->createQueryBuilder('m')
            ->orderBy('m.position', 'ASC')
            ->getQuery()
            ->execute();

    }
    public function createAlphabeticalQueryBuilder()
    {
        return $this->createQueryBuilder('m')
            ->orderBy('m.name', 'ASC');

    }
}