<?php

namespace AppBundle\Controller;

use AppBundle\Entity\OutValue;
use AppBundle\Form\OutValueFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use AppBundle\Api\ValueApiModel;

class OutValueApiController extends BaseController {

  /**
   * @Route("/outvalue/{id}", name="out_get")
   * @Method("GET")
   */
  public function getOutValueAction(OutValue $outValue)
  {
    $apiModel = $this->createOutValueApiModel($outValue);

    return $this->createApiResponse($apiModel);
  }


  /**
   * @Route("/outvalue/new", name="outvalue_new")
   * @Method("POST")
   */
  public function newAction(Request $request)
  {
    $data = json_decode($request->getContent(), true);
    if ($data === null) {
      throw new BadRequestHttpException('Invalid JSON');
    }

    $form = $this->createForm(OutValueFormType::class, null, [
      'csrf_protection' => false,
    ]);

    $form->submit($data);
    if (!$form->isValid()) {
      $errors = $this->getErrorsFromForm($form);

      return $this->createApiResponse([
        'errors' => $errors
      ], 400);
    }

    /** @var OutValue $outValue */
    $outValue = $form->getData();

    $em = $this->getDoctrine()->getManager();
    $em->persist($outValue);
    $em->flush();

    $apiModel = $this->createOutValueApiModel($outValue);

    $response = $this->createApiResponse($apiModel);
    // setting the Location header... it's a best-practice
    $response->headers->set(
      'Location',
      $this->generateUrl('out_get', ['id' => $outValue->getId()])
    );

    return $response;
  }

  /**
   * @Route("/outvalue/{id}/edit", name="outvalue_edit")
   * @Method("POST")
   */
  public function editOutValueAction(Request $request, OutValue $outValue)
  {
    $data = json_decode($request->getContent(), true);
    if ($data === null) {
      throw new BadRequestHttpException('Invalid JSON');
    }

    $form = $this->createForm(OutValueFormType::class, $outValue, [
      'csrf_protection' => false,
    ]);

    $form->submit($data);

    if (!$form->isValid()) {
      $errors = $this->getErrorsFromForm($form);

      return $this->createApiResponse([
        'errors' => $errors
      ], 400);
    }

    $outValue = $form->getData();

    $em = $this->getDoctrine()->getManager();
    $em->persist($outValue);
    $em->flush();

    $apiModel = $this->createOutValueApiModel($outValue);

    $response = $this->createApiResponse($apiModel);
    // setting the Location header... it's a best-practice
    $response->headers->set(
      'Location',
      $this->generateUrl('out_get', ['id' => $outValue->getId()])
    );

    return $response;
  }

  /**
   * Turns a Outvalue into a OutValueApiModel for the API.
   *
   * @param OutValue $outValue
   * @return ValueApiModel
   */
  private function createOutValueApiModel(OutValue $outValue)
  {
    $model = new ValueApiModel();
    $model->id = $outValue->getId();
    $model->date = $outValue->getDate();
    $model->value = $outValue->getValue();
    $model->machine = $outValue->getMachine()->getId();
    $model->createdAt = $outValue->getCreatedAt();
    $model->updatedAt = $outValue->getUpdatedAt();

    return $model;
  }
}