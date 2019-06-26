<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\CashRegisterFormType;
use AppBundle\Entity\CashRegister;
use AppBundle\Form\DatePickerFormType;


class CashRegisterController extends Controller
{
    /**
     * @Route("/cash/{date}/new", name="cash_register_new")
     */
    public function newAction(Request $request, $date)
    {
        $form = $this->createForm(CashRegisterFormType::class);

        return $this->render('cr/_form.html.twig', [
          'crForm' => $form->createView(),
          'date' => $date
        ]);
    }

    /**
     * @Route("/cash", name="cash_list")
     */
    public function listAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $crnames = $this->getDoctrine()->getRepository('AppBundle:CashRegister')->findAllNames();

        $names = [];
        foreach ($crnames as $crname){
            if (!in_array($crname, $names)) {
                $names[] = $crname;
            }
        }

        $pagination = [];
        $date_db = null;
        $date_db_old = null;
        $date = $request->query->getAlnum('date');
        $date_old = $request->query->getAlnum('date_old');
        $name = $request->query->get('name');

        $sumPlus = NULL;
        $sumMinus = NULL;

        if ($date && $date_old) {

            $dp = $this->get('app.doctrine.date_listener');

            $date = $dp->getDateForm($date);
            $date_old = $dp->getDateForm($date_old);

            $date_db = $dp->getDateDB($date);
            $date_db_old = $dp->getDateDBOld($date_old);

            $query = $em->getRepository('AppBundle:CashRegister')->findCashRecords($name, $date_db, $date_db_old);

            $cashRegister = $em->getRepository('AppBundle:CashRegister')->findValues($name, $date_db, $date_db_old);
            $plus = [];
            $minus = [];
            foreach ($cashRegister as $cr){
                if($cr['value']>0){
                    $plus[] = $cr['value'];
                } else {
                    $minus[] = $cr['value'];
                }
            }
            $sumPlus = array_sum($plus);
            $sumMinus = array_sum($minus);

            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1)/*page number*/,
                10/*limit per page*/
            );

        }

        return $this->render('default/expenses.html.twig', [
            'date'          => $date,
            'date_old'      => $date_old,
            'selected_name' => $name,
            'names'         => $names,
            'pagination'    => $pagination,
            'sumPlus'       => $sumPlus,
            'sumMinus'      => $sumMinus
        ]);
    }

}