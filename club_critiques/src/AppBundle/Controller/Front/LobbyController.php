<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Content;
use AppBundle\Entity\Lobby;
use AppBundle\Entity\Participation;
use AppBundle\Form\LobbyType;
use AppBundle\Repository\ContentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sonata\AdminBundle\Form\Type\Filter\DateTimeType;
use Sonata\AdminBundle\Form\Type\Filter\DateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\DateFormatter\IntlDateFormatter;

class LobbyController extends Controller
{
    /**
     * @Route("/salon/{id}/chat", name="lobby")
     */

    public function lobbyAction(Request $request)
    {
        $user = $this->getUser();
        $lobby = $this->getDoctrine()->getRepository('AppBundle:Lobby')->find($request->get('id'));
        $user_note = $this->getDoctrine()->getRepository('AppBundle:Note')->findBy(array('content' => $lobby->content, 'user' => $user));

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

        if (!$participation && !$request->query->has('from_invite')) {
            $this->addFlash("danger", "Vous n'êtes pas inscrit à ce salon");
            return $this->redirectToRoute('lobby_list');
        } else if (!$participation && $request->query->has('from_invite')) {
            return $this->redirectToRoute('lobby_register', array('id' => $lobby->id, 'from_invite' => true));
        } else if ($participation) {
            if (date('Y-m-d H:i') > $lobby->date_end->format('Y-m-d H:i')) {
                $this->addFlash("danger", "Ce salon est terminé. Si vous y avez participé, vous pouvez consulter l'historique.");
                return $this->redirectToRoute('lobby_list_history');
            }
            if (!$request->query->has('from_invite')) {
                if (date('Y-m-d H:i', strtotime('+10 minutes', strtotime($lobby->date_start->format('Y-m-d H:i')))) < date('Y-m-d H:i') && !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                    $this->addFlash("danger", "Ce salon a commencé depuis plus de 10 minutes. Vous ne pouvez plus le rejoindre.");
                    return $this->redirectToRoute('lobby_list');
                }
            }
            // Répartition des participants dans les salles en fonction de leurs notes pour avoir une salle ayant des avis différents
            $user_ids = array();
            foreach ($participations as $_participation) {
                $user_ids[] = $_participation->user->id;
            }
            $notes = $this->getDoctrine()->getRepository('AppBundle:Note')->getNotesForLobby($lobby, $user_ids);
            $repartition = array();
            $nb_user_per_room = count($notes) / $nb_rooms;
            for ($i = 0; $i < $nb_rooms; $i++) {
                for ($j = 0; $j < $nb_user_per_room; $j++) {
                    if ($j % 2 == 0) {
                        $repartition[$i][] = $notes[0];
                        unset($notes[0]);
                    } else {
                        $repartition[$i][] = $notes[count($notes) - 1];
                        unset($notes[count($notes) - 1]);
                    }
                    $notes = array_values($notes);
                }
            }

            $user_note = $this->getDoctrine()->getRepository('AppBundle:Note')->findBy(array('content' => $lobby->content, 'user' => $user));
            $user_room = 0;
            foreach ($repartition as $k => $room) {
                foreach ($room as $participant) {
                    if ($participant->id == $user_note[0]->user->id) {

                        $user_room = $k + 1;
                    }
                }
            }
            $participation->setRoom($user_room);
            $em = $this->getDoctrine()->getManager();
            $em->persist($participation);
            $em->flush();
            return $this->render('lobby/lobby.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
                'controller' => 'salon',
                'user_room' => $user_room,
                'lobby' => $lobby,
                'user' => $user,
                'user_note' => 3
            ]);
        }
    }

    /**
     * @Route("/salon/{id}/register", name="lobby_register")
     */

    public
    function lobbyRegisterAction(Request $request)
    {
        $user = $this->getUser();
        if (!$user)
        {
            $this->addFlash("danger", "Vous devez être connecté pour participer aux salons.");
            return $this->redirectToRoute('lobby_list');
        }
        else
        {
            $lobby = $this->getDoctrine()->getRepository('AppBundle:Lobby')->find($request->get('id'));
            $participation = $this->getDoctrine()->getRepository('AppBundle:Participation')->findOneBy(array('lobby' => $lobby, 'user' => $user));
            if ($participation) {
                $this->addFlash("warning", "Vous êtes déjà inscrit à ce salon");
            } else {
                $note = $this->getDoctrine()->getRepository('AppBundle:Note')->findOneBy(array('content' => $lobby->content, 'user' => $this->getUser()));
                if ($note) {
                    $this->addFlash("success", "Vous êtes maintenant inscrit à ce salon");
                    $participation = new Participation();
                    $participation->setUser($user);
                    $participation->setLobby($lobby);
                    $participation->setStatus(1);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($participation);
                    $em->flush();
                    if ($request->query->has('from_invite')) {
                        return $this->redirectToRoute('lobby', array('id' => $lobby->id, 'from_invite' => 1));
                    }
                } else {
                    return $this->redirectToRoute('contenu', array('id' => $lobby->content->id, 'frmlby' => 1, 'lby' => $lobby->id));
                }

            }
            return $this->redirectToRoute('lobby_list');
        }
    }

    /**
     * @Route("/salons", name="lobby_list")
     */

    public function lobbyListAction(Request $request)
    {
        $doctrine = $this->getDoctrine();
        $categoryRepository = $doctrine->getRepository('AppBundle:Category');

        /** Filters **/
        $subcategories = $categoryRepository->getSubCategories();
        $authors = $doctrine->getRepository('AppBundle:Author')->findBy(array('status' => 1), array('name' => 'ASC'));

        $em = $doctrine->getManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT id FROM `lobby` WHERE date_start + INTERVAL 10 MINUTE > now()");
        $statement->execute();
        $results = $statement->fetchAll();

        $lobby_ids = array();
        foreach ($results as $key => $result) {
            $lobby_ids[] = intval($result['id']);
        }
        $lobby_list = $doctrine->getRepository('AppBundle:Lobby')->findById($lobby_ids, array('date_start' => 'ASC'));

        $content_list = array();
        foreach ($lobby_list as $lobby) {
            $content_list[] = $lobby->content;
        }
        $user_notes = $doctrine->getRepository('AppBundle:Note')->findByContent($content_list);
        $user_participations = $doctrine->getRepository('AppBundle:Participation')->findByLobby($lobby_list);

        return $this->render('lobby/lobbies.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
            'controller' => 'lobby_list',
            'lobby_list' => $lobby_list,
            'user_participations' => $user_participations,
            'selected_author_id' => 0,
            'selected_sub_category_id' => 0,
            'user' => $this->getUser(),
            'user_notes' => $user_notes,
            'subcategories' => $subcategories,
            'authors' => $authors
        ]);
    }

    /**
     * @Route("/salons/history", name="lobby_list_history")
     */

    public
    function lobbyListHistoryAction(Request $request)
    {
        $user = $this->getUser();

        $categoryRepository = $this->getDoctrine()->getRepository('AppBundle:Category');

        /** Filters **/
        $categories = $categoryRepository->findBy(array('parentCategory' => null));
        $subcategories = $categoryRepository->getSubCategories();
        $authors = $this->getDoctrine()->getRepository('AppBundle:Author')->findBy(array('status' => 1), array('name' => 'ASC'));

        $lobby_list = array();
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $lobby_list = $this->getDoctrine()->getRepository('AppBundle:Lobby')->getLobbyHistory();
        } else {
            $participations = $this->getDoctrine()->getRepository('AppBundle:Participation')->findByUser($user);
            foreach ($participations as $participation) {
                if ($participation->lobby->date_end->format('Y-m-d H:i') < date('Y-m-d H:i')) {
                    $lobby_list[] = $participation->lobby;
                }
            }
        }
        return $this->render('lobby/lobbies.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
            'controller' => 'lobby_list_history',
            'categories' => $categories,
            'lobby_list' => $lobby_list,
            'selected_author_id' => 0,
            'selected_sub_category_id' => 0,
            'authors' => $authors,
            'user' => $this->getUser(),
            'subcategories' => $subcategories
        ]);
    }

    /**
     * @Route("/salon/{id}/history", name="lobby_history")
     */

    public
    function lobbyHistoryAction(Request $request)
    {
        $user = $this->getUser();
        $lobby = $this->getDoctrine()->getRepository('AppBundle:Lobby')->find($request->get('id'));

        $participation = $this->getDoctrine()->getRepository('AppBundle:Participation')->findOneBy(array('user' => $user, 'lobby' => $lobby));
        $participations = $this->getDoctrine()->getRepository('AppBundle:Participation')->findBy(array('lobby' => $lobby));
        if (!$participation && !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $this->addFlash("danger", "Vous n'avez pas participé à ce salon");
            return $this->redirectToRoute('lobby_list_history');
        } else {
            $rooms = array();
            $history = unserialize($lobby->getHistory());
            if (!empty($history)) {
                foreach ($history as $room) {
                    if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                        $rooms[$room->room_id]['messages'] = $room->messages;
                        foreach ($participations as $_participation) {
                            if ($_participation->room == $room->room_id) {
                                $rooms[$room->room_id]['participants'][] = $_participation;
                            }
                        }
                    } else {
                        if ($room->room_id == $participation->room) {
                            $rooms[$participation->room]['messages'] = $room->messages;
                            foreach ($participations as $_participation) {
                                if ($_participation->room == $participation->room) {
                                    $rooms[$participation->room]['participants'][] = $_participation;
                                }
                            }
                        }
                    }
                }
            }
            return $this->render('lobby/lobby-history.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
                'controller' => 'lobby_history',
                'lobby' => $lobby,
                'participations' => $participations,
                'user' => $user,
                'rooms' => $rooms
            ]);
        }
    }

    /**
     * @Route("/salons/filters", name="lobbies_on_filter_change")
     */
    public
    function ajaxOnFilterChange(Request $request)
    {
        $doctrine = $this->getDoctrine();
        $category = $author = null;
        $title = $request->get('title');
        $history = false;
        /*** Filters ***/
        $subcategories = $doctrine->getRepository('AppBundle:Category')->getSubCategories();
        $authors = $doctrine->getRepository('AppBundle:Author')->findBy(array('status' => 1), array('name' => 'ASC'));

        if ($request->get('category_id') > 0) {
            $category = $doctrine->getRepository('AppBundle:Category')->find($request->get('category_id'));
        }
        if ($request->get('history') == 1) {
            $history = true;
        }
        if ($request->get('author_id') > 0) {
            $author = $doctrine->getRepository('AppBundle:Author')->find($request->get('author_id'));
        }

        $lobby_list = $doctrine->getRepository('AppBundle:Lobby')->getLobbiesByFilters($category, $author, $title, $history);
        dump($lobby_list);

        return $this->render('lobby/lobby-list.html.twig', [
            'lobby_list' => $lobby_list ?: null,
            'subcategories' => $subcategories,
            'authors' => $authors,
            'selected_sub_category_id' => $category ? $category->id : 0,
            'selected_author_id' => $author ? $author->id : 0,
            'title' => $title,
            'controller' => $history ? 'lobby_list_history' : 'lobby_list'
        ]);
    }

    /**
     * @Route("/salon/invite/callback", name="invite_callback")
     */

    public function inviteCallbackAction(Request $request)
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash("warning", "Veuillez vous connecter puis réessayer.");
            return $this->redirectToRoute('fos_user_security_login');
        } else if ($request->query->has('id') && $request->query->has('from_invite')) {
            return $this->redirectToRoute('lobby', array('id' => $request->get('id'), 'from_invite' => $request->get('id')));
        } else {
            return $this->redirectToRoute('lobby_list');
        }
    }

    /**
     * @Route("/salon/suggestion", name="propose_lobby")
     */
    public function proposeLobbyAction(Request $request) {

        $lobby = new Lobby();
        $form = $this->createForm('AppBundle\Form\LobbyType', $lobby);
        $form->handleRequest($request);
        if($form->isValid()) {
            $lobby->setStatus(0);
            $lobby->setCreatedByUser(1);
            $em = $this->getDoctrine()->getManager();
            $em->persist($lobby);
            $em->flush();

            $mailer = $this->container->get('mailer');

            $message = (new \Swift_Message('[Suggestion] Un utilisateur a suggéré un salon.'))
                ->setFrom('noreply@club-critiques.com')
                ->setTo($this->container->getParameter('mailer_user'))
                ->setBody(
                    $this->renderView(
                        'mails/propose-lobby.html.twig',
                        array('lobby' => $lobby,
                              'user' => $this->getUser())
                    ),
                    'text/html'
                );
            $mailer->send($message);

            $this->addFlash("success", "Le salon a correctement été proposé.");
            $this->redirectToRoute("propose_lobby");

        }

        return $this->render('lobby/propose.html.twig', array(
            'form' => $form->createView(),
            'route' => 'propose_lobby'
        ));
    }

}
