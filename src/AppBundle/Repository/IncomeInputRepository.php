<?php

namespace AppBundle\Repository;

use AppBundle\Entity\IncomeInput;
use AppBundle\Entity\Worker;
use Doctrine\ORM\EntityRepository;

class IncomeInputRepository extends EntityRepository
{
    /**
     * @return IncomeInput[]
     */
    public function findAllbyWorker(Worker $worker)
    {
        return $this->createQueryBuilder('i')
            ->where('i.worker = :worker')
            ->setParameter('worker', $worker)
            ->getQuery()
            ->execute();
    }
}