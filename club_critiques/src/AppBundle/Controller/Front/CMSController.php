<?php

namespace AppBundle\Controller\Front;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Acl\Exception\Exception;

class CMSController extends Controller
{
    /**
     * @Route("/cms/{id}", name="cms")
     */
    public function displayAction (Request $request)
    {
        $doctrine = $this->getDoctrine();

        $cms_id = $request->get('id');
        $cms = $doctrine->getRepository('AppBundle:CMS')->find($cms_id);
        if (!$cms) {
            throw new NotFoundHttpException();
        }

        return $this->render('cms/cms.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'controller' => 'cms',
            'cms' => $cms
        ]);
    }
}