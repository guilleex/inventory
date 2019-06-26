<?php

namespace AppBundle\Controller;

use AppBundle\Entity\InValue;
use AppBundle\Entity\MachineType;
use AppBundle\Form\InValueFormType;
use AppBundle\Repository\JackpotRepository;
use AppBundle\Repository\CashRegisterRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\DatePickerFormType;
use AppBundle\Entity\Machine;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function indexAction(Request $request)
    {
        $tm = $this->get('app.doctrine.type_and_machine_listener');

        $types_machines = $tm->findTypesAndMachines();
        $date_db = null;
        $date = $request->query->getAlnum('date');

        if ($date) {

            $dp = $this->get('app.doctrine.date_listener');

            $date = $dp->getDateForm($date);
            $date_db = $dp->getDateDB($date);
            $date_db_old = $dp->getDateDBOld($date);

            foreach ($types_machines['machines'] as $machine){
                $tm->findValues($machine, $date_db, $date_db_old);
            }

            foreach ($types_machines['machineTypes'] as $machineType){
                /** @var MachineType $machineType */
                $machineType->setDateJP($date_db);
            }

        }

            $em = $this->getDoctrine()->getManager();

            $active_types = $em->getRepository('AppBundle:MachineType')->findAllActive();

        return $this->render('default/index.html.twig', [
            'date'          => $date,
            'date_db'       => $date_db,
            'machines'      => $types_machines['machines'],
            'machineTypes'  => $types_machines['machineTypes'],
            'activeTypes'   => $active_types
            // 'cash_register' => $cr,
        ]);
    }

    /**
     * @Route("/periodic", name="periodic")
     */
    public function periodicAction(Request $request)
    {
        $tm = $this->get('app.doctrine.type_and_machine_listener');

        $types_machines = $tm->findTypesAndMachines();

        $date_db = null;
        $date_db_old = null;
        $date = $request->query->getAlnum('date');
        $date_old = $request->query->getAlnum('date_old')
        ;
        if ($date && $date_old) {

            $dp = $this->get('app.doctrine.date_listener');

            $date = $dp->getDateForm($date);
            $date_old = $dp->getDateForm($date_old);

            $date_db = $dp->getDateDB($date);
            $date_db_old = $dp->getDateDBOld($date_old);

            foreach ($types_machines['machines'] as $machine){
                $tm->findPeriodicValues($machine, $date_db, $date_db_old);
            }

            foreach ($types_machines['machineTypes'] as $machineType){
                $tm->jackpot($machineType, $date_db, $date_db_old );
            }
        }

        return $this->render('default/periodic.html.twig', [
            'date'         => $date,
            'date_old'     => $date_old,
            'date_db'      => $date_db,
            'date_db_old'  => $date_db_old,
            'machines'     => $types_machines['machines'],
            'machineTypes' => $types_machines['machineTypes'],
        ]);
    }

    /**
     * @Route("/setings", name="setings")
     */
    public function listTypesAndMachinesAction()
    {
        $types_machines = $this->get('app.doctrine.type_and_machine_listener')
                               ->findTypesAndMachines();

        return $this->render('default/setings.html.twig', [
            'machines'     => $types_machines['machines'],
            'machineTypes' => $types_machines['machineTypes']
        ]);
    }

    /**
     * @Route("/pdf/{date}", name="pdf_create")
     */
    public function pdfCreateAction($date)
    {
      $tm = $this->get('app.doctrine.type_and_machine_listener');

      $types_machines = $tm->findTypesAndMachines();
      $date_db = null;
      $date_new = $date;

      if ($date) {

        $dp = $this->get('app.doctrine.date_listener');

        $date = $dp->getDateForm($date_new);
        $date_db = $dp->getDateDB($date_new);
        $date_db_old = $dp->getDateDBOld($date_new);

        foreach ($types_machines['machines'] as $machine){
          $tm->findValues($machine, $date_db, $date_db_old);
        }

        foreach ($types_machines['machineTypes'] as $machineType){
          /** @var MachineType $machineType */
          $machineType->setDateJP($date_db);
        }

      }

      $em = $this->getDoctrine()->getManager();

      $active_types = $em->getRepository('AppBundle:MachineType')->findAllActive();

      $snappy = $this->get('knp_snappy.pdf');

      $html =  $this->renderView('default/pdf.html.twig', [
        'date'          => $date_new,
        'date_db'       => $date_db,
        'machines'      => $types_machines['machines'],
        'machineTypes'  => $types_machines['machineTypes'],
        'activeTypes'   => $active_types
      ]);

      $filename = $date_new;

      return new Response(
        $snappy->getOutputFromHtml($html),
        200,
        array(
          'Content-Type'          => 'application/pdf',
          'Content-Disposition'   => 'inline; filename="'.$filename.'.pdf"'
        )
      );
    }

    /**
     * @Route("/pdf-periodic/{date_old}/{date}", name="pdf_periodic")
     */
    public function pdfCreatePeriodicAction($date_old, $date)
    {
      $tm = $this->get('app.doctrine.type_and_machine_listener');

      $types_machines = $tm->findTypesAndMachines();

      $date_db = null;
      $date_db_old = null;
      $date_pr = $date;
      $date_old_pr = $date_old;

      if ($date && $date_old) {

        $dp = $this->get('app.doctrine.date_listener');

//        $date = $dp->getDateForm($date_pr);
//        $date_old = $dp->getDateForm($date_old_pr);

        $date_db = $dp->getDateDB($date_pr);
        $date_db_old = $dp->getDateDBOld($date_old_pr);

        foreach ($types_machines['machines'] as $machine){
          $tm->findPeriodicValues($machine, $date_db, $date_db_old);
        }

        foreach ($types_machines['machineTypes'] as $machineType){
          $tm->jackpot($machineType, $date_db, $date_db_old );
        }
      }

      $snappy = $this->get('knp_snappy.pdf');

      $html =  $this->renderView('default/pdf_periodic.html.twig', [
        'date'         => $date_pr,
        'date_old'     => $date_old_pr,
        'date_db'      => $date_db,
        'date_db_old'  => $date_db_old,
        'machines'     => $types_machines['machines'],
        'machineTypes' => $types_machines['machineTypes'],
      ]);

      $filename = $date_old_pr . ' - ' . $date_pr;

      return new Response(
        $snappy->getOutputFromHtml($html),
        200,
        array(
          'Content-Type'          => 'application/pdf',
          'Content-Disposition'   => 'inline; filename="'.$filename.'.pdf"'
        )
      );
    }

}
