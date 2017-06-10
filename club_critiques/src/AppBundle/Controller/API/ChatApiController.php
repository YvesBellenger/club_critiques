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
     * @param Request $request
     * @Get("/chat/{id}/join")
     */
    public function checkAccessAction(Request $request)
    {
        return new JsonResponse("");
    }

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
     * @Rest\Get("/chat/{id}/messages")
     */
    public function postMessagesAction(Request $request)
    {

        return new JsonResponse("");

    }
}
