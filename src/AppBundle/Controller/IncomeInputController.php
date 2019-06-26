<?php

namespace AppBundle\Controller;

use AppBundle\Entity\IncomeInput;
use AppBundle\Entity\Worker;
use AppBundle\Entity\Income;
use AppBundle\Form\IncomeInputFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Finder\Finder;
use AppBundle\Api\IncomeInputApiModel;


class IncomeInputController extends BaseController
{

    /**
     * @Route("/incomeInput/{worker}/list", name="incomeInput_list", options={"expose" = true})
     * @Method("GET")
     */
    public function getIncomeInputsAction(Worker $worker)
    {
        $incomeInputs = $this->getDoctrine()->getRepository('AppBundle:IncomeInput')
            ->findAllbyWorker($worker)
        ;

        $models = [];
        foreach ($incomeInputs as $incomeInput) {
            $models[] = $this->createIncomeInputApiModel($incomeInput);
        }

        return $this->createApiResponse([
            'items' => $models
        ]);
    }

    /**
     * @Route("/incomeInput/{id}", name="income_input_nput_get")
     * @Method("GET")
     */
    public function getincomeInputAction(IncomeInput $incomeInput)
    {
        $apiModel = $this->createIncomeInputApiModel($incomeInput);

        return $this->createApiResponse($apiModel);
    }

     /**
      * @Route("/income_input/new", name="income_input_new")
      * @Method("POST")
      */
     public function newAction(Request $request)
     {
         $data = json_decode($request->getContent(), true);
         if ($data === null) {
             throw new BadRequestHttpException('Invalid JSON');
         }

         $form = $this->createForm(IncomeInputFormType::class, null, [
             'csrf_protection' => false,
         ]);
         $form->submit($data);
         if (!$form->isValid()) {
             $errors = $this->getErrorsFromForm($form);

             return $this->createApiResponse([
                 'errors' => $errors
             ], 400);
         }

         /** @var IncomeInput $incomeInpur */
         $incomeInput = $form->getData();

         $em = $this->getDoctrine()->getManager();
         $em->persist($incomeInput);
         $em->flush();

         $apiModel = $this->createIncomeInputApiModel($incomeInput);

         $response = $this->createApiResponse($apiModel);
         // setting the Location header... it's a best-practice
         $response->headers->set(
             'Location',
             $this->generateUrl('income_input_nput_get', ['id' => $incomeInput->getId()])
         );

         return $response;
     }

    /**
     * @Route("/income_input/{id}/edit", name="income_input_edit")
     * @Method("POST")
     */
    public function editAction(Request $request, IncomeInput $incomeInput)
    {
         $data = json_decode($request->getContent(), true);
         if ($data === null) {
           throw new BadRequestHttpException('Invalid JSON');
         }

         $form = $this->createForm(IncomeInputFormType::class, $incomeInput, [
           'csrf_protection' => false,
         ]);

        $form->submit($data);

        if (!$form->isValid()) {
          $errors = $this->getErrorsFromForm($form);

          return $this->createApiResponse([
            'errors' => $errors
          ], 400);
        }

        $incomeInput = $form->getData();

        $em = $this->getDoctrine()->getManager();
        $em->persist($incomeInput);
        $em->flush();

        $apiModel = $this->createIncomeInputApiModel($incomeInput);

        $response = $this->createApiResponse($apiModel);
        // setting the Location header... it's a best-practice
        $response->headers->set(
          'Location',
          $this->generateUrl('income_input_nput_get', ['id' => $incomeInput->getId()])
        );

        return $response;
    }

    /**
     * @Route("/income_delete/{id}", name="income_input_delete")
     * @Method("DELETE")
     */
    public function deleteIncomeInputAction(IncomeInput $incomeInput)
    {
      $em = $this->getDoctrine()->getManager();
      $em->remove($incomeInput);
      $em->flush();

      return new Response(null, 204);
    }


    /**
     * Turns a IncomeInput into a IncomeinputApiModel for the API.
     *
     * @param IncomeInput $incomeInput
     * @return IncomeInputApiModel
     */
    private function createIncomeInputApiModel(IncomeInput $incomeInput)
    {
        $model = new IncomeInputApiModel();
        $model->id = $incomeInput->getId();
        $model->worker = $incomeInput->getWorker()->getId();
        $model->date = $incomeInput->getDate();
        $model->value = $incomeInput->getValue();

        return $model;
    }
}