<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserFormType;
use AppBundle\Form\UserEditFormType;
use AppBundle\Form\AccountEditFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserController extends Controller
{

  /**
   *  @Route("/test", name="test")
   */
  public function pdfAction()
  {

    $snappy = $this->get('knp_snappy.pdf');

    $html = '<h1>Hello</h1>';

    $filename = 'myFirstSnappyPDF';

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
     * @Route("/users", name="users")
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('AppBundle:User')
            ->findAllOrderedByLastName();

        $paginator  = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('user/list.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/user/new", name="user_create")
     */
    public function newAction(Request $request)
    {
        $form = $this->createForm(UserFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'User created!');
            return $this->redirectToRoute('users');
        }

        return $this->render('user/new.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/user/{id}/edit", name="user_edit")
     */
    public function editAction(Request $request, User $user)
    {

        $form = $this->createForm(UserEditFormType::class, $user);

        // only handles data on POST
        $form->handleRequest($request);
        $deleteForm = $this->createDeleteForm($user);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'User updated!');

            return $this->redirectToRoute('user_edit', ['id' => $user->getId()]);
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/edit/account", name="account_edit")
     */
    public  function editAccountAction(Request $request)
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createNotFoundException('Unable to find User.');
        }

        $form = $this->createForm(AccountEditFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Account updated!');

            return $this->redirectToRoute('account_edit');
        }

        return $this->render('user/acountEdit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * Creates a form to delete a user entity.
     *
     * @param User $user The user entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array('id' => $user->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

    /**
     * Deletes a user entity.
     * @Route("/user/delete/{id}", name="user_delete")
     */
    public function deleteAction(Request $request, User $user)
    {
        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();

            $this->addFlash('success', 'User deleted!');
        }

        return $this->redirectToRoute('users');
    }
}

