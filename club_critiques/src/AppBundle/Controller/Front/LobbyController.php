<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Participation;
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

        // Définition du nbr de salles
        $participations = $this->getDoctrine()->getRepository('AppBundle:Participation')->findBy(array('lobby' => $lobby));
        $nb_rooms = 0;
        if (count($participations) < 26) {
            $nb_rooms = 1;
        } elseif (count($participations) >= 26 && count($participations) < 41) {
            $nb_rooms = 2;
        } elseif (count($participations) >= 41 && count($participations) < 61) {
            $nb_rooms = 3;
        } else {
            $nb_rooms = 4;
        }

        $participation = $this->getDoctrine()->getRepository('AppBundle:Participation')->findOneBy(array('user' => $user, 'lobby' => $lobby));

        if (!$participation) {
          $this->addFlash("danger", "Vous n'êtes pas inscrit à ce salon");
          return $this->redirectToRoute('lobby_list');
        } else if (date('Y-m-d H:i', strtotime('+10 minutes', strtotime($lobby->date_start->format('Y-m-d H:i')))) < date('Y-m-d H:i') && !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $this->addFlash("danger", "Ce salon a commencé depuis plus de 10 minutes. Vous ne pouvez plus le rejoindre.");
            return $this->redirectToRoute('lobby_list');
        } else {
            // Répartition des participants dans les salles en fonction de leurs notes pour avoir une salle ayant des avis différents
            $user_ids = array();
            foreach ($participations as $_participation) {
                $user_ids[] = $_participation->user->id;
            }
            $notes = $this->getDoctrine()->getRepository('AppBundle:Note')->findByUser($user_ids);
            $repartition = array();
            $nb_user_per_room = count($notes)/$nb_rooms;
            for ($i = 0; $i < $nb_rooms; $i++) {
                for ($j = 0; $j <= $nb_user_per_room; $j++) {
                    if ($j % 2 == 0) {
                        $repartition[$i] = $notes[$j];
                        unset($notes[$j]);
                    } else {
                        $repartition[$i] = $notes[count($notes) - $j];
                        unset($notes[count($notes) - $j]);
                    }
                }
                array_values($notes);
            }

            $user_note = $this->getDoctrine()->getRepository('AppBundle:Note')->findBy(array('content' => $lobby->content, 'user' => $user));
            $user_room = array_search($user_note, $repartition);
            $participation->setRoom($user_room);
            $em = $this->getDoctrine()->getManager();
            $em->persist($participation);
            $em->flush();
            return $this->render('lobby/lobby.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
                'controller' => 'salon',
                'user_room' => $user_room,
                'lobby' => $lobby,
                'user' => $user
            ]);
        }
    }

    /**
     * @Route("/salon/{id}/register", name="lobby_register")
     */

    public function lobbyRegisterAction(Request $request)
    {
        $user = $this->getUser();
        $lobby = $this->getDoctrine()->getRepository('AppBundle:Lobby')->find($request->get('id'));
        $participation = $this->getDoctrine()->getRepository('AppBundle:Participation')->findOneBy(array('lobby' => $lobby, 'user' => $user));
        if ($participation) {
            $this->addFlash("warning", "Vous êtes déjà inscrit à ce salon");
        } else {
            $this->addFlash("success", "Vous êtes maintenant inscrit à ce salon");
            $participation = new Participation();
            $participation->setUser($user);
            $participation->setLobby($lobby);
            $participation->setStatus(1);
            $em = $this->getDoctrine()->getManager();
            $em->persist($participation);
            $em->flush();
        }
        return $this->redirectToRoute('lobby_list');
    }

    /**
     * @Route("/salons", name="lobby_list")
     */

    public function lobbyListeAction(Request $request)
    {
        $doctrine = $this->getDoctrine();
        $categoryRepository = $doctrine->getRepository('AppBundle:Category');

        /** Filters **/
        $categories = $categoryRepository->findBy(array('parentCategory' => null));
        $subcategories = $categoryRepository->getSubCategories();

        /** Contents **/
        $category = $categoryRepository->findOneByCode('livre');
        $contents = $doctrine->getRepository('AppBundle:Content')->findBy(array('status' => 1));

        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT id FROM `lobby` WHERE date_start + INTERVAL 10 MINUTE > now()");
        $statement->execute();
        $results = $statement->fetchAll();
        $lobby_ids = array();
        foreach ($results as $key => $result) {
            $lobby_ids[] = intval($result['id']);
        }
        $lobby_list = $doctrine->getRepository('AppBundle:Lobby')->findById($lobby_ids);

        return $this->render('lobby/lobbies.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'controller' => 'lobby_list',
            'contents' => $contents,
            'categories' => $categories,
            'lobby_list' => $lobby_list,
            'selected_category_id' => 0,
            'selected_sub_category_id' => 0,
            'user' => $this->getUser(),
            'subcategories' => $subcategories
        ]);
    }

    /**
     * @Route("/salons/history", name="lobby_list_history")
     */

    public function lobbyListHistoryAction(Request $request)
    {
        $user = $this->getUser();
//        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
//            $lobby_list = $this->getDoctrine()->getRepository('AppBundle:Lobby')->getLobbyHistory();
//        } else {
            $participations = $this->getDoctrine()->getRepository('AppBundle:Participation')->findByUser($user);
//        }
        return $this->render('lobby/lobby-list.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'controller' => 'lobby_list',
            'contents' => $contents,
            'categories' => $categories,
            'lobby_list' => $lobby_list,
            'selected_category_id' => 0,
            'selected_sub_category_id' => 0,
            'user' => $this->getUser(),
            'subcategories' => $subcategories
        ]);
    }
}
