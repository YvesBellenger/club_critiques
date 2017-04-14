<?php

namespace AppBundle\Controller\Front;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $doctrine = $this->getDoctrine();
        $cms_home = $doctrine->getRepository('AppBundle:CMS')->findOneByCode('home');

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'controller' => 'accueil',
            'cms' => $cms_home
        ]);
    }

    /**
     * @Route("/contenus", name="contenus")
     */
    public function contenusActions (Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/contenus.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'controller' => 'contenus'
        ]);
    }
}
