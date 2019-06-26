<?php

namespace AppBundle\Controller;

use AppBundle\Entity\MachineType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\MachineTypeFormType;


class MachineTypeController extends Controller
{

    /**
     * @Route("/setings/type/new", name="setings_type_new")
     */
    public function newAction(Request $request)
    {
        $types_machines = $this->get('app.doctrine.type_and_machine_listener')
            ->findTypesAndMachines();

        $form = $this->createForm(MachineTypeFormType::class);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $type = $form->getData();

            $em = $this->getDoctrine()->getManager();

            $em->persist($type);
            $em->flush();

            $this->addFlash('success', 'New Machine Type created!');

            return $this->redirectToRoute('setings');
        }

        return $this->render('type/new.html.twig', [
            'machines'     => $types_machines['machines'],
            'machineTypes' => $types_machines['machineTypes'],
            'typeForm'     => $form->createView()
        ]);

    }

    /**
     * @Route("/setings/type/{slug}/edit", name="setings_type_edit")
     */
    public function editAction(Request $request, MachineType $type)
    {
        $types_machines = $this->get('app.doctrine.type_and_machine_listener')
            ->findTypesAndMachines();

        $form = $this->createForm(MachineTypeFormType::class, $type);

        // only handles data on POST
        $form->handleRequest($request);

        $deleteForm = $this->createDeleteForm($type);


        if ($form->isSubmitted() && $form->isValid()) {

            $type = $form->getData();

            $em = $this->getDoctrine()->getManager();

            $em->persist($type);
            $em->flush();

            $this->addFlash('success', 'Type updated!');

            return $this->redirectToRoute('setings');
        }

        return $this->render('type/edit.html.twig', [
            'type' => $type,
            'machines'     => $types_machines['machines'],
            'machineTypes' => $types_machines['machineTypes'],
            'typeForm' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Creates a form to delete a machine type entity.
     *
     * @param MachineType $machineType The machine type entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(MachineType $machineType)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('type_delete', array('id' => $machineType->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

    /**
     * Deletes a machine type entity.
     * @Route("/setings/type/delete/{id}", name="type_delete")
     */
    public function deleteAction(Request $request, MachineType $machineType)
    {
        $form = $this->createDeleteForm($machineType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($machineType);
            $em->flush();

            $this->addFlash('success', 'Machine Type deleted!');
        }

        return $this->redirectToRoute('setings');
    }
}