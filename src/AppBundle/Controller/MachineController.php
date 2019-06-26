<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\MachineFormType;
use AppBundle\Entity\Machine;
use AppBundle\Entity\MachineType;

class MachineController extends Controller
{
    /**
     * @Route("/setings/machine/new", name="setings_machine_new")
     */
    public function newAction(Request $request)
    {
        $form = $this->createForm(MachineFormType::class);
        $em = $this->getDoctrine()->getManager();
        $number = $em->getRepository('AppBundle:Machine')->countMachines();
        $types_machines = $this->get('app.doctrine.type_and_machine_listener')
            ->findTypesAndMachines();

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $machine = $form->getData();

            $em->persist($machine);
            $em->flush();

            $this->addFlash('success', 'New Machine created!');

            return $this->redirectToRoute('setings');
        }

        return $this->render('machine/new.html.twig', [
            'machines'     => $types_machines['machines'],
            'machineTypes' => $types_machines['machineTypes'],
            'machineForm' => $form->createView(),
            'number' => $number
        ]);
    }

    /**
     * @Route("/setings/machine/{slug}/edit", name="setings_machine_edit")
     */
    public function editAction(Request $request, Machine $machine)
    {

        $form = $this->createForm(MachineFormType::class, $machine);
        $em = $this->getDoctrine()->getManager();
        $number = $em->getRepository('AppBundle:Machine')->countMachines();
        $types_machines = $this->get('app.doctrine.type_and_machine_listener')
            ->findTypesAndMachines();

        // only handles data on POST
        $form->handleRequest($request);

        $deleteForm = $this->createDeleteForm($machine);

        if ($form->isSubmitted() && $form->isValid()) {

            $machine = $form->getData();

            $em = $this->getDoctrine()->getManager();

            $em->persist($machine);
            $em->flush();

            $this->addFlash('success', 'Machine updated!');

            return $this->redirectToRoute('setings');
        }

        return $this->render('machine/edit.html.twig', [
            'machines'     => $types_machines['machines'],
            'machineTypes' => $types_machines['machineTypes'],
            'machineForm'  => $form->createView(),
            'delete_form'  => $deleteForm->createView(),
            'number' => $number
        ]);
    }

    /**
     * @Route("/setings/machine/{slug}/show", name="machine_show")
     *
     * Finds and displays a machine entity.
     *
     */
    public function showAction(Machine $machine, Request $request)
    {
        return $this->render('machine/show.html.twig', [
            'machine' => $machine,
        ]);
    }

    /**
     * Creates a form to delete a machine entity.
     *
     * @param Machine $machine The machine entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Machine $machine)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('machine_delete', array('id' => $machine->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

    /**
     * Deletes a machine entity.
     * @Route("/setings/machine/delete/{id}", name="machine_delete")
     */
    public function deleteAction(Request $request, Machine $machine)
    {
        $form = $this->createDeleteForm($machine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($machine);
            $em->flush();

            $this->addFlash('success', 'Machine deleted!');
        }

        return $this->redirectToRoute('setings');
    }
}