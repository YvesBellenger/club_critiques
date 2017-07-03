<?php

namespace AppBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;

class ChatApiController extends Controller
{
    /**
     *
     * @param Request $request
     * @Rest\Get("/chat/{id}/messages")
     */
    public function getMessagesAction(Request $request)
    {

        return new JsonResponse("");

    }

    /**
     *
     * @param Request $request
     * @Rest\Post("/chat/messages")
     */
    public function postMessagesAction(Request $request)
    {
        $result = array();
        $data = json_decode($request->getContent());
        $doctrine = $this->getDoctrine();
        if (!$data) {
            $result['success'] = false;
            $result['message'] = 'no data provided';
        } else {
            $lobby = $doctrine->getRepository('AppBundle:Lobby')->find($data->id);
            $lobby->setHistory(serialize($data->messages));
            $lobby->setStatus(0);
            $em = $this->getDoctrine()->getManager();
            $em->persist($lobby);
            $em->flush();
            $result['success'] = true;
            $result['message'] = 'lobby closed';
        }
        return new JsonResponse(json_encode($result));

    }
}
