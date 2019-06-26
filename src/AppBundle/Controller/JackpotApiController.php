<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Jackpot;
use AppBundle\Form\JackpotFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use AppBundle\Api\JackpotApiModel;

class JackpotApiController extends BaseController {

    /**
     * @Route("/jp/{id}/show", name="jackpot_get")
     * @Method("GET")
     */
    public function getJackpotAction(Jackpot $jp)
    {
        $apiModel = $this->createJackpotApiModel($jp);

        return $this->createApiResponse($apiModel);
    }


    /**
     * @Route("/jp/new", name="jackpot_new")
     * @Method("POST")
     */
    public function newAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        if ($data === null) {
          throw new BadRequestHttpException('Invalid JSON');
        }

        $form = $this->createForm(JackpotFormType::class, null, [
          'csrf_protection' => false,
        ]);

        $form->submit($data);
        if (!$form->isValid()) {
          $errors = $this->getErrorsFromForm($form);

          return $this->createApiResponse([
            'errors' => $errors
          ], 400);
        }

        /** @var Jackpot $jp */
        $jp = $form->getData();

        $em = $this->getDoctrine()->getManager();
        $em->persist($jp);
        $em->flush();

        $apiModel = $this->createJackpotApiModel($jp);

        $response = $this->createApiResponse($apiModel);
        // setting the Location header... it's a best-practice
        $response->headers->set(
          'Location',
          $this->generateUrl('jackpot_get', ['id' => $jp->getId()])
        );

        return $response;
    }

    /**
     * @Route("/jp/{id}/edit", name="jackpot_edit")
     * @Method("POST")
     */
    public function editJackpotAction(Request $request, Jackpot $jp)
    {
        $data = json_decode($request->getContent(), true);
        if ($data === null) {
          throw new BadRequestHttpException('Invalid JSON');
        }

        $form = $this->createForm(JackpotFormType::class, $jp, [
          'csrf_protection' => false,
        ]);

        $form->submit($data);

        if (!$form->isValid()) {
          $errors = $this->getErrorsFromForm($form);

          return $this->createApiResponse([
            'errors' => $errors
          ], 400);
        }

        $jp = $form->getData();

        $em = $this->getDoctrine()->getManager();
        $em->persist($jp);
        $em->flush();

        $apiModel = $this->createJackpotApiModel($jp);

        $response = $this->createApiResponse($apiModel);
        // setting the Location header... it's a best-practice
        $response->headers->set(
          'Location',
          $this->generateUrl('jackpot_get', ['id' => $jp->getId()])
        );

        return $response;
    }

    /**
     * Turns a jackpot into a JackpotApiModel for the API.
     *
     * @param Jackpot $jp
     * @return JackpotApiModel
     */
    private function createJackpotApiModel(Jackpot $jp)
    {
        $model = new JackpotApiModel();
        $model->id = $jp->getId();
        $model->date = $jp->getDate();
        $model->value = $jp->getValue();
        $model->machineType = $jp->getMachineType()->getId();
        $model->createdAt = $jp->getCreatedAt();
        $model->updatedAt = $jp->getUpdatedAt();

        return $model;
    }
}