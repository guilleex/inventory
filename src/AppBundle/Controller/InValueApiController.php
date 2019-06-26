<?php

namespace AppBundle\Controller;

use AppBundle\Entity\InValue;
use AppBundle\Form\InValueFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use AppBundle\Api\ValueApiModel;

class InValueApiController extends BaseController {

    /**
     * @Route("/invalue/{id}", name="in_get")
     * @Method("GET")
     */
    public function getInValueAction(InValue $inValue)
    {
        $apiModel = $this->createInValueApiModel($inValue);

        return $this->createApiResponse($apiModel);
    }


     /**
     * @Route("/invalue/new", name="invalue_new")
     * @Method("POST")
     */
    public function newAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        if ($data === null) {
            throw new BadRequestHttpException('Invalid JSON');
        }

        $form = $this->createForm(InValueFormType::class, null, [
            'csrf_protection' => false,
        ]);

        $form->submit($data);
        if (!$form->isValid()) {
            $errors = $this->getErrorsFromForm($form);

            return $this->createApiResponse([
                'errors' => $errors
            ], 400);
        }

        /** @var InValue $inValue */
        $inValue = $form->getData();

        $em = $this->getDoctrine()->getManager();
        $em->persist($inValue);
        $em->flush();

        $apiModel = $this->createInValueApiModel($inValue);

        $response = $this->createApiResponse($apiModel);
        // setting the Location header... it's a best-practice
        $response->headers->set(
           'Location',
            $this->generateUrl('in_get', ['id' => $inValue->getId()])
        );

        return $response;
    }

    /**
     * @Route("/invalue/{id}/edit", name="invalue_edit")
     * @Method("POST")
     */
    public function editInValueAction(Request $request, InValue $inValue)
    {
      $data = json_decode($request->getContent(), true);
      if ($data === null) {
        throw new BadRequestHttpException('Invalid JSON');
      }

      $form = $this->createForm(InValueFormType::class, $inValue, [
        'csrf_protection' => false,
      ]);

      $form->submit($data);

      if (!$form->isValid()) {
        $errors = $this->getErrorsFromForm($form);

        return $this->createApiResponse([
          'errors' => $errors
        ], 400);
      }

      $inValue = $form->getData();

      $em = $this->getDoctrine()->getManager();
      $em->persist($inValue);
      $em->flush();

      $apiModel = $this->createInValueApiModel($inValue);

      $response = $this->createApiResponse($apiModel);
      // setting the Location header... it's a best-practice
      $response->headers->set(
        'Location',
        $this->generateUrl('in_get', ['id' => $inValue->getId()])
      );

      return $response;
    }

     /**
     * Turns a Invalue into a InValueApiModel for the API.
     *
     * @param InValue $inValue
     * @return ValueApiModel
     */
    private function createInValueApiModel(InValue $inValue)
    {
        $model = new ValueApiModel();
        $model->id = $inValue->getId();
        $model->date = $inValue->getDate();
        $model->value = $inValue->getValue();
        $model->machine = $inValue->getMachine()->getId();
        $model->createdAt = $inValue->getCreatedAt();
        $model->updatedAt = $inValue->getUpdatedAt();

        return $model;
    }
}