<?php

namespace AppBundle\Controller\Front;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SalonsController extends Controller
{
    /**
     * @Route("/salons/salon/exemple", name="salon")
     */

    public function salonAction(Request $request)
    {
        return $this->render('salons/salon.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'controller' => 'salon'
        ]);
    }

    /**
     * @Route("/salons/liste", name="salons-liste")
     */

    public function salonListeAction(Request $request)
    {
        return $this->render('salons/salons-list.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'controller' => 'salons-liste'
        ]);
    }
}
