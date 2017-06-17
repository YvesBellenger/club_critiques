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
        $block_home = $doctrine->getRepository('AppBundle:BlockContent')->findOneByCode('a-la-une');
        $next_lobby = $doctrine->getRepository('AppBundle:Lobby')->findBy(array('status' => 1), array('date_start' => 'ASC'), 1);
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'controller' => 'accueil',
            'cms' => $cms_home,
            'next_lobby' => !empty($next_lobby) ? $next_lobby[0] : null,
            'block_home' => $block_home
        ]);
    }
}
