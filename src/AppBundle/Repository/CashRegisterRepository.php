<?php

namespace AppBundle\Repository;

use AppBundle\Entity\CashRegister;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class CashRegisterRepository extends EntityRepository
{
    /**
     * @return CashRegister[]
     */
    public function findAllForDate(\DateTime $date)
    {
        return $this->createQueryBuilder('cr')
            ->where('cr.date = :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->execute();
    }

    public function findAllNames()
    {
        return $this->createQueryBuilder('cr')
            ->select('cr.name')
            ->orderBy('cr.name', 'ASC')
            ->getQuery()
            ->execute();
    }

    /**
     * @param \DateTime $date
     * @param \DateTime $date_old
     * @return array
     */
    public function findValues($name, \DateTime $date, \DateTime $date_old)
    {
        return $this->createQueryBuilder('cr')
                    ->where('cr.date >= :date_start')
                    ->andWhere('cr.date <= :date_end')
                    ->andWhere('cr.name = :name')
                    ->setParameter('date_end', $date)
                    ->setParameter('date_start', $date_old)
                    ->setParameter('name', $name)
                    ->select('cr.value')
                    ->getQuery()
                    ->execute();
    }

    /**
     * @param \DateTime $date
     * @param \DateTime $date_old
     * @return CashRegister[]
     */
    public function findCashRecords($name, \DateTime $date, \DateTime $date_old)
    {
        return $this->createQueryBuilder('cr')
                    ->where('cr.date >= :date_start')
                    ->andWhere('cr.date <= :date_end')
                    ->andWhere('cr.name = :name')
                    ->setParameter('date_end', $date)
                    ->setParameter('date_start', $date_old)
                    ->setParameter('name', $name)
                    ->orderBy('cr.date', 'DESC')
                    ->getQuery()
                    ;
    }

    /**
     * Paginator Helper
     *
     * Pass through a query object, current page & limit
     * the offset is calculated from the page and limit
     * returns an `Paginator` instance, which you can call the following on:
     *
     *     $paginator->getIterator()->count() # Total fetched (ie: `5` presentations)
     *     $paginator->count() # Count of ALL presentations (ie: `20` presentations)
     *     $paginator->getIterator() # ArrayIterator
     *
     * @param Doctrine\ORM\Query $dql   DQL Query Object
     * @param integer            $page  Current page (defaults to 1)
     * @param integer            $limit The total number per page (defaults to 5)
     *
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function paginate($dql, $page = 1, $limit = 5)
    {
        $paginator = new Paginator($dql);

        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1)) // Offset
            ->setMaxResults($limit); // Limit

        return $paginator;
    }
}