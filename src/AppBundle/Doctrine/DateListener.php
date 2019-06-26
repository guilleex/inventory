<?php

namespace AppBundle\Doctrine;

class DateListener
{
    public function getDateForm($date)
    {

            $day = substr($date, 0, -6);
            $month = substr($date, 2, -4);
            $year = substr($date, 4, 4);

            $date = $day . '.' . $month . '.' . $year;

            return $date;

    }

    public function getDateDB($date)
    {
            $date_db = date_create($date);

            return $date_db;
    }

    public function getDateDBOld($date)
    {
        $date_db_old = date_create($date);
        date_sub($date_db_old, date_interval_create_from_date_string('1 day'));

        return $date_db_old;
    }

    public function getDateLink($date)
    {
        $date = explode("-", $date);
        $date = array_reverse($date);
        $date = implode(".", $date);

        return $date;

    }
}