<?php

namespace AppBundle\Repository;

use AppBundle\Entity\MachineType;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\Criteria;

class MachineTypeRepository extends EntityRepository
{
    public function createAlphabeticalQueryBuilder()
    {
        return $this->createQueryBuilder('type')
                    ->orderBy('type.name', 'ASC');

    }

    /**
     * @return MachineType[]
     */
    public function findAllWithJackpots()
    {
        return $this->createQueryBuilder('type')
            ->where('type.haveJackpot = :haveJackpot')
            ->setParameter('haveJackpot', TRUE)
            ->orderBy('type.name', 'ASC')
            ->getQuery()
            ->execute();
    }

    /**
     * @return MachineType[]
     */
    public function findAllActive()
    {
        return $this->createQueryBuilder('type')
            ->join('type.machines', 'machine')
            ->where('machine.visible = :visible')
            ->setParameter('visible', TRUE)
            ->getQuery()
            ->execute();
    }


}