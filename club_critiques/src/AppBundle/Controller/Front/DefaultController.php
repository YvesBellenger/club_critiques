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
        $form = $this->createForm('AppBundle\Form\HomeContactType',null,array(
            'method' => 'POST'
        ));

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                if ($this->sendEmailFromHome($form->getData())) {
                    $this->addFlash("success", "L'email a correctement été envoyé.");
                    return $this->redirectToRoute('homepage');
                } else {
                    $this->addFlash("success", "Une erreur est survenue.");
                    return $this->redirectToRoute('homepage');
                }
            }
        }

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'controller' => 'accueil',
            'cms' => $cms_home,
            'next_lobby' => !empty($next_lobby) ? $next_lobby[0] : null,
            'block_home' => $block_home,
            'form' => $form->createView(),
        ]);
    }

    private function sendEmailFromHome($data){
        $mailer = $this->container->get('mailer');
        $message = (new \Swift_Message('mail'))
            ->setFrom($this->container->getParameter('mailer_user'))
            ->setTo($this->container->getParameter('mailer_user'))
            ->setSubject('[Formulaire de contact] - '.$data["subject"])
            ->setBody(
                $this->renderView
                (
                    'mails/email-index.html.twig',
                    array
                    (
                        'data' => $data
                    )
                ),'text/html'
            );
        return $mailer->send($message);

    }

    public function footerAction(Request $request)
    {
        $doctrine = $this->getDoctrine();

        return $this->render('default/footer.html.twig', [
            'footer' => $doctrine->getRepository('AppBundle:CMS')->getFooter(),
        ]);
    }

    public function menuAction(Request $request)
    {
        $doctrine = $this->getDoctrine();

        return $this->render('default/menu.html.twig', [
            'menu' => $doctrine->getRepository('AppBundle:CMS')->getMenu(),
        ]);
    }
}
