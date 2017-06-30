<?php

namespace AppBundle\Controller\Front;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class LobbyController extends Controller
{
    /**
     * @Route("/salon/{id}", name="lobby")
     */

    public function lobbyAction(Request $request)
    {
        $user = $this->getUser();
        $lobby = $this->getDoctrine()->getRepository('AppBundle:Lobby')->find($request->get('id'));
        return $this->render('lobby/lobby.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'controller' => 'salon',
            'lobby' => $lobby,
            'user' => $user
        ]);
    }

    /**
     * @Route("/salon/{id}/register", name="lobby_register")
     */

    public function lobbyRegisterAction(Request $request)
    {
        $user = $this->getUser();
        $lobby = $this->getDoctrine()->getRepository('AppBundle:Lobby')->find($request->get('id'));
        if ($lobby->participants->contains($user)) {
            $this->addFlash("warning", "Vous êtes déjà inscrit à ce salon");
        } else {
            $this->addFlash("success", "Vous êtes maintenant inscrit à ce salon");
            $lobby->addParticipant($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($lobby);
            $em->flush();
        }
        return $this->redirectToRoute('lobby_list');
    }

    /**
     * @Route("/salons", name="lobby_list")
     */

    public function lobbyListeAction(Request $request)
    {

        $modeAdd = false;
        if ($request->query->has('modeAdd')) {
            $modeAdd = true;
        }
        $doctrine = $this->getDoctrine();
        $categoryRepository = $doctrine->getRepository('AppBundle:Category');

        /** Filters **/
        $categories = $categoryRepository->findBy(array('parentCategory' => null));
        $subcategories = $categoryRepository->getSubCategories();

        /** Contents **/
        $category = $categoryRepository->findOneByCode('livre');
        $contents = $doctrine->getRepository('AppBundle:Content')->findBy(array('status' => 1));

        $lobby_list = $doctrine->getRepository('AppBundle:Lobby')->getNextLobbies();


        return $this->render('lobby/lobby-list.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'controller' => 'lobby_list',
            'contents' => $contents,
            'categories' => $categories,
            'lobby_list' => $lobby_list,
            'selected_category_id' => 0,
            'selected_sub_category_id' => 0,
            'modeAdd' => $modeAdd,
            'subcategories' => $subcategories
        ]);
    }
}
