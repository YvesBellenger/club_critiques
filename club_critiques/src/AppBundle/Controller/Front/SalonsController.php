<?php

namespace AppBundle\Controller\Front;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SalonsController extends Controller
{
    /**
     * @Route("/", name="salons")
     */

    public function salonsAction(Request $request)
    {
        return $this->render('salons/salons-list.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'controller' => 'salons',
        ]);
    }
}
