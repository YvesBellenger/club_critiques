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


        return $this->render('lobby/lobby-list.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'controller' => 'lobby-liste',
            'contents' => $contents,
            'categories' => $categories,
            'selected_category_id' => 0,
            'selected_sub_category_id' => 0,
            'modeAdd' => $modeAdd,
            'subcategories' => $subcategories
        ]);
    }
}
