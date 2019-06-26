<?php

namespace AppBundle\Doctrine;

use AppBundle\Entity\CashRegister;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraints\DateTime;

class CashRegisterListener {

    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }


}