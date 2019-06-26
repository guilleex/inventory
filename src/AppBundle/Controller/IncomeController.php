<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Income;
use AppBundle\Entity\Worker;
use AppBundle\Entity\IncomeInput;
use AppBundle\Form\IncomeFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Oneup\UploaderBundle\Event\PostPersistEvent;
use Symfony\Component\Finder\Finder;

class IncomeController extends Controller
{
    /**
     * @Route("/salaries", name="income_list")
     */
    public function indexAction() {

        $em = $this->getDoctrine()->getManager();

        $salaries = $em->getRepository('AppBundle:Income')->findAll();

        return $this->render('income/index.html.twig', [
          'salaries' => $salaries
        ]);
    }

    /**
     * @Route("/salaries/new", name="income_new")
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $salaries = $em->getRepository('AppBundle:Income')->findAll();

        $form = $this->createForm(IncomeFormType::class);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $income = $form->getData();

            $em->persist($income);
            $em->flush();

            $this->addFlash('success', 'New Salaries List created!');

            return $this->redirectToRoute('income_edit', array('slug' => $income->getSlug()));
        }

        return $this->render('income/new.html.twig', [
            'incomeForm' => $form->createView(),
            'salaries' => $salaries
        ]);

    }

    /**
     * @Route("/salaries/{slug}/edit", name="income_edit")
     */
    public function editAction(Request $request, Income $income)
    {
        $em = $this->getDoctrine()->getManager();
        $salaries = $em->getRepository('AppBundle:Income')->findAll();

        $form = $this->createForm(IncomeFormType::class, $income);

        // only handles data on POST
        $form->handleRequest($request);

        $deleteForm = $this->createDeleteForm($income);

        if ($form->isSubmitted() && $form->isValid()) {

            $income = $form->getData();

            $em = $this->getDoctrine()->getManager();

            $em->persist($income);
            $em->flush();

            return $this->redirectToRoute('income_edit', array('slug' => $income->getSlug()));
        }

        return $this->render('income/edit.html.twig', [
            'incomeForm'  => $form->createView(),
            'delete_form' => $deleteForm->createView(),
            'income'      => $income,
            'salaries'    => $salaries
        ]);

    }

    /**
     *@Route("/salaries/{slug}/show", name="income_show")
     */
    public function showAction(Request $request, Income $income)
    {
        $em = $this->getDoctrine()->getManager();
        $salaries = $em->getRepository('AppBundle:Income')->findAll();
        return $this->render('income/show.html.twig', [
            'salarie' => $income,
            'salaries' => $salaries
        ]);
    }

    /**
     * Creates a form to delete a income entity.
     *
     * @param Income $income The income entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Income $income)
    {
      return $this->createFormBuilder()
        ->setAction($this->generateUrl('income_delete', array('id' => $income->getId())))
        ->setMethod('DELETE')
        ->getForm()
        ;
    }

    /**
     * Deletes a income entity.
     * @Route("/delete/{id}", name="income_delete")
     */
    public function deleteAction(Request $request, Income $income)
    {
      $form = $this->createDeleteForm($income);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
        $em = $this->getDoctrine()->getManager();

        $em->remove($income);
        $em->flush();

        $this->addFlash('success', 'Salaries List deleted!');
      }

      return $this->redirectToRoute('income_list');
    }
}