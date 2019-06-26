<?php

namespace AppBundle\Doctrine;

use AppBundle\Entity\Machine;
use AppBundle\Entity\MachineType;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\InValue;
use AppBundle\Entity\OutValue;
use AppBundle\Entity\Jackpot;
use Symfony\Component\Validator\Constraints\DateTime;
use Doctrine\ORM\EntityManagerInterface;

class TypeAndMachineListener
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function findTypesAndMachines()
    {
        $machineTypes = $this->em->getRepository('AppBundle:MachineType')
            ->createAlphabeticalQueryBuilder()
            ->getQuery()
            ->execute();

        $machines = $this->em->getRepository('AppBundle:Machine')
            ->allOrderByPosition();

        $types_machines['machineTypes'] = $machineTypes;
        $types_machines['machines'] = $machines;

        return $types_machines;
    }

    public function findValues(Machine $machine, \DateTime $date_db, \DateTime $date_db_old)
    {
        /** @var InValue $inValue */
        /** @var InValue $inValueOld */
        /** @var OutValue $outValue */
        /** @var OutValue $outValueOld */
        /** @var Machine $machine */
        $date_create = $machine->getCreatedAt();
        date_sub($date_create, date_interval_create_from_date_string('1 day'));

        $inValue = $this->em->getRepository('AppBundle:InValue')->value($machine, $date_db);
        $inValueOld = $this->em->getRepository('AppBundle:InValue')->valueOld($machine, $date_db_old, $date_create);
        $outValue = $this->em->getRepository('AppBundle:OutValue')->value($machine, $date_db);
        $outValueOld = $this->em->getRepository('AppBundle:OutValue')->valueOld($machine, $date_db_old, $date_create);

        if ($inValue) {
            /** @var Machine $machine */
            $machine->setInFindValue($inValue['value']);
        }

        if ($inValueOld) {
            /** @var Machine $machine */
            $machine->setInFindValueOld($inValueOld['value']);
        }

        if ($outValue) {
            /** @var Machine $machine */
            $machine->setOutFindValue($outValue['value']);
        }

        if ($outValueOld) {
            /** @var Machine $machine */
            $machine->setOutFindValueOld($outValueOld['value']);
        }

        $machine->setDate($date_db);
        $machine->setDateOld($date_db_old);

    }

    public function findPeriodicValues(Machine $machine, \DateTime $date_db, \DateTime $date_db_old)
    {
        $inValues = $this->em->getRepository('AppBundle:InValue')->valuesPeriodic($machine, $date_db, $date_db_old);
        $inValuesOld = $this->em->getRepository('AppBundle:InValue')->valuesPeriodic($machine, $date_db, $date_db_old);
        $outValues = $this->em->getRepository('AppBundle:OutValue')->valuesPeriodic($machine, $date_db, $date_db_old);
        $outValuesOld = $this->em->getRepository('AppBundle:OutValue')->valuesPeriodic($machine, $date_db, $date_db_old);

        if ($inValues && $inValuesOld) {

            $value_array = max($inValues);
            $max_value = $value_array['value'];
            $machine->setInFindValue($max_value);

            $value_array = min($inValuesOld);
            $min_value = $value_array['value'];
            $machine->setInFindValueOld($min_value);
        }


        if ($outValues && $outValuesOld) {

            $value_array = max($outValues);
            $max_value = $value_array['value'];
            $machine->setOutFindValue($max_value);

            $value_array = min($outValuesOld);
            $min_value = $value_array['value'];
            $machine->setOutFindValueOld($min_value);
        }

    }

    public function jackpot(MachineType $machineType, \DateTime $date_db, \DateTime $date_db_old)
    {
        $jackpots = $this->em->getRepository('AppBundle:Jackpot')->valuesPeriodic($machineType, $date_db, $date_db_old);

        if ($jackpots) {

            $jp = 0;

            foreach ($jackpots as $jackpot) {

                $jp = $jp + $jackpot["value"];

            }

            $machineType->setPeriodJackpot($jp);

        }
    }
}