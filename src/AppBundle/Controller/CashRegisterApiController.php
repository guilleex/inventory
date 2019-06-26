<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CashRegister;
use AppBundle\Form\CashRegisterFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use AppBundle\Api\CashRegisterApiModel;

class CashRegisterApiController extends BaseController
{
    /**
     * @Route("/cr/{date}/list", name="cash_register_list", options={"expose" = true})
     * @Method("GET")
     */
    public function getCashRegisterAction(\DateTime $date)
    {
        $cash_register = $this->getDoctrine()->getRepository('AppBundle:CashRegister')
            ->findAllForDate($date)
        ;

        $models = [];
        foreach ($cash_register as $cr) {
            $models[] = $this->createCashRegisterApiModel($cr);
        }

        return $this->createApiResponse([
            'items' => $models
        ]);
    }

    /**
     * @Route("/cr/{id}", name="cr_get")
     * @Method("GET")
     */
    public function getCRAction(CashRegister $CashRegister)
    {
        $apiModel = $this->createCashRegisterApiModel($CashRegister);

        return $this->createApiResponse($apiModel);
    }

    /**
     * @Route("/cr_delete/{id}", name="cr_delete")
     * @Method("DELETE")
     */
    public function deleteCashRegisterAction(CashRegister $CashRegister)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($CashRegister);
        $em->flush();

        return new Response(null, 204);
    }

    /**
     * @Route("/cr/{date}/new", name="cr_new")
     * @Method("POST")
     */
    public function newCashRegisterAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        if ($data === null) {
            throw new BadRequestHttpException('Invalid JSON');
        }

        $form = $this->createForm(CashRegisterFormType::class, null, [
            'csrf_protection' => false,
        ]);
        $form->submit($data);
        if (!$form->isValid()) {
            $errors = $this->getErrorsFromForm($form);

            return $this->createApiResponse([
                'errors' => $errors
            ], 400);
        }

        /** @var CashRegister $cr */
        $cr = $form->getData();

        $em = $this->getDoctrine()->getManager();
        $em->persist($cr);
        $em->flush();

        $apiModel = $this->createCashRegisterApiModel($cr);

        $response = $this->createApiResponse($apiModel);
        // setting the Location header... it's a best-practice
        $response->headers->set(
            'Location',
            $this->generateUrl('cr_get', ['id' => $cr->getId()])
        );

        return $response;
    }

    /**
     * @Route("/cr/{id}/edit", name="cr_edit")
     * @Method("POST")
     */
    public function editCashRegisterAction(Request $request, CashRegister $cr)
    {
         $data = json_decode($request->getContent(), true);
         if ($data === null) {
           throw new BadRequestHttpException('Invalid JSON');
         }

         $form = $this->createForm(CashRegisterFormType::class, $cr, [
           'csrf_protection' => false,
         ]);

        $form->submit($data);

        if (!$form->isValid()) {
          $errors = $this->getErrorsFromForm($form);

          return $this->createApiResponse([
            'errors' => $errors
          ], 400);
        }

        $cr = $form->getData();

        $em = $this->getDoctrine()->getManager();
        $em->persist($cr);
        $em->flush();

        $apiModel = $this->createCashRegisterApiModel($cr);

        $response = $this->createApiResponse($apiModel);
        // setting the Location header... it's a best-practice
        $response->headers->set(
          'Location',
          $this->generateUrl('cr_get', ['id' => $cr->getId()])
        );

        return $response;
    }

    /**
     * Turns a CashRegister into a CashRegisterApiModel for the API.
     *
     * This could be moved into a service if it needed to be
     * re-used elsewhere.
     *
     * @param CashRegister $CashRegister
     * @return CashRegisterApiModel
     */
    private function createCashRegisterApiModel(CashRegister $cr)
    {
        $model = new CashRegisterApiModel();
        $model->id = $cr->getId();
        $model->name = $cr->getName();
        $model->value = $cr->getValue();
        $model->date = $cr->getDate();

        return $model;
    }
}
