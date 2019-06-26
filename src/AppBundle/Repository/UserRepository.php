<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class UserRepository extends EntityRepository
{
    /**
     * @return User[]
     */
    public function findAllOrderedByLastName()
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.last_name', 'ASC')
            ->getQuery()
        ;
    }

    public function findUsersOrderByName() {

        return $this
            ->createQueryBuilder('e')
            ->addOrderBy('e.username', 'ASC')
            ->getQuery()
            ->execute()
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