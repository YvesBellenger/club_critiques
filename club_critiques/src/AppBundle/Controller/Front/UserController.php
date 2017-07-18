<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{

    /**
     * @Route("/users", name="users")
     */
    public function usersActions(Request $request)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('fos_user_security_login');
        } else if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $this->addFlash("danger", "Vous n'avez pas l'autorisation d'accéder à cette page.");
            return $this->redirectToRoute('homepage');
        } else {
            $doctrine = $this->getDoctrine();

            /** Users **/
            $users = $doctrine->getRepository('AppBundle:User')->findBy(array('status' => 1), array('username' => 'ASC'));

            /*$paginator = $this->get('knp_paginator');
            $users = $paginator->paginate(
                $users,
                $request->query->getInt('page',1),
                $request->query->getInt('limit',12)
            );*/

            return $this->render('profile/user-list.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
                'controller' => 'users',
                'users' => $users
            ]);
        }
    }

    /**
     * @Route("/users/loadMore", name="users_load_more")
     */
    public function loadMoreAction(Request $request)
    {
        $limit = 8;
        $offset = $request->get('offset');
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->getByFilters($limit, $offset);

        return $this->render('profile/load-more.html.twig', [
            'users' => $users ?: null,
        ]);
    }

    /**
     * @Route("/user/{id}", name="user")
     */
    public function userActions (Request $request)
    {
        if (!$this->getUser()) {
            $this->addFlash("danger", "Il faut être connecté pour voir les profils des utilisateurs.");
            return $this->redirectToRoute('fos_user_security_login');
        }
        else
        {
            $doctrine = $this->getDoctrine();
            $user = $doctrine->getRepository('AppBundle:User')->find($request->get('id'));
            $form = $this->createForm('AppBundle\Form\ContactType', null, array(
                'method' => 'POST'
            ));

            if ($request->isMethod('POST')) {
                $form->handleRequest($request);
                if ($form->isValid()) {
                    if ($this->sendEmailContact($form->getData(),$this->getUser(),$user->getEmail())) {
                        $this->addFlash("success", "L'email a correctement été envoyé.");
                        return $this->redirectToRoute('user',array('id' => $request->get('id')));
                    } else {
                        $this->addFlash("danger", "Une erreur est survenue.");
                        return $this->redirectToRoute('user',array('id' => $request->get('id')));
                    }
                }
            }

            return $this->render('profile/user.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
                'controller' => 'user',
                'user' => $user,
                'connected_user' => $this->getUser(),
                'form' => $form->createView()
            ]);
        }
    }

    private function sendEmailContact($data, $sender, $receiver){
        $mailer = $this->container->get('mailer');
        $message = (new \Swift_Message())
            ->setFrom($data['email'])
            ->setTo($receiver)
            ->setSubject('[Club des critiques] '.$data['subject'])
            ->setBody(
                $this->renderView
                (
                    'mails/contact.html.twig',
                    array
                    (
                            'data' => $data,
                            'sender' => $sender
                    )
                ),'text/html'
            );
        return $mailer->send($message);

    }

    /**
     * @Route("/user/{id}/add", name="user_add")
     */
    public function addToContactAction(Request $request)
    {
        $doctrine = $this->getDoctrine();
        $contact = $doctrine->getRepository('AppBundle:User')->find($request->get('id'));
        $user = $this->getUser();
        $user->addContact($contact);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        $this->addFlash("success", "L'utilisateur a bien été ajouté à vos contacts.");
        return $this->redirectToRoute('user', ['id' => $contact->id]);
    }

    /**
     * @Route("/user/{id}/remove", name="user_remove")
     */
    public function removeContactAction(Request $request)
    {
        $doctrine = $this->getDoctrine();
        $contact = $doctrine->getRepository('AppBundle:User')->find($request->get('id'));
        $user = $this->getUser();
        $user->removeContact($contact);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        $this->addFlash("success", "L'utilisateur a bien été supprimé de vos contacts.");
        $referer = $request->headers->get('referer');
        if(isset($referer)) {
            return $this->redirect($referer);
        }
        else{
            return $this->redirectToRoute('user',array('id' => $request->get('id')));
        }

    }

    /**
     * @Route("/profil", name="profil")
     */
    public function profilAction (Request $request) {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('fos_user_security_login');
        } else {
            return $this->render('profile/profil.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
                'controller' => 'user',
                'user' => $user,
            ]);
        }
    }


    /**
     * @Route("/salon/user/report", name="user_report")
     */
    public function reportAction (Request $request) {
        $participant = $this->getDoctrine()->getRepository('AppBundle:User')->find($request->get('participant_id'));
        $lobby = $this->getDoctrine()->getRepository('AppBundle:Lobby')->find($request->get('lobby_id'));
        $user = $this->getUser();
        User::IncreaseReportNumber($participant);
        $em = $this->getDoctrine()->getManager();
        $em->persist($participant);
        $em->flush();
        $mailer = $this->container->get('mailer');
        $message = (new \Swift_Message('[Report] Un utilisateur a été signalé'))
            ->setFrom('noreply@club-critiques.com')
            ->setTo('humbertsimon@gmail.com')
            ->setBody(
                $this->renderView(
                    'mails/report.html.twig',
                    array('participant' => $participant,
                          'user' => $user,
                          'lobby' => $lobby)
                ),
                'text/html'
            );
        $mailer->send($message);

        $response = array('success' => true);
        return new JsonResponse(json_encode($response));
    }

    /**
     * @Route("/salon/user/invitation", name="lobby_invite")
     */

    public
    function lobbyInviteAction(Request $request)
    {
        $user = $this->getUser();
        $lobby = $this->getDoctrine()->getRepository('AppBundle:Lobby')->find($request->get('lobby_id'));
        $contact = $this->getDoctrine()->getRepository('AppBundle:User')->find($request->get('contact_id'));

        $mailer = $this->container->get('mailer');
        $message = (new \Swift_Message('[Invitation] Un utilisateur vous a invité à un salon'))
            ->setFrom('noreply@club-critiques.com')
            ->setTo($contact->getEmail())
            ->setBody(
                $this->renderView(
                    'mails/invite.html.twig',
                    array('lobby' => $lobby,
                        'user' => $user,
                        'contact' => $contact)
                ),
                'text/html'
            );
        $mailer->send($message);

        $response = array('success' => true);
        return new JsonResponse(json_encode($response));
    }

    /**
     * @Route("/profil/shared/add", name="profil_add_shared_content")
     */
    public function userAddSharedContentAction(Request $request)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('fos_user_security_login');
        } else {
            $content = $this->getDoctrine()->getRepository('AppBundle:Content')->find($request->get('content_id'));
            if(isset($content)) {
                if ($user->contentsToShare->contains($content)) {
                    $user->removeContentToShare($content);
                    $user->addContentShared($content);
                } else {
                    var_dump($content);
                    die();
                    $user->addContentWanted($content);
                }
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }
        $this->addFlash("success", "Le contenu est désormais considéré comme prêté.");
        return $this->redirectToRoute('profil');
    }

    /**
     * @Route("/profil/shared/remove", name="profil_remove_shared_content")
     */
    public function userRemoveSharedContentAction(Request $request)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('fos_user_security_login');
        } else {
            $content = $this->getDoctrine()->getRepository('AppBundle:Content')->find($request->get('content_id'));
            if(isset($content)) {
                if ($user->contentsShared->contains($content)) {
                    $user->removeContentShared($content);
                    $user->addContentToShare($content);
                } else {
                    var_dump($content);
                    die();
                    $user->addContentWanted($content);
                }
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }
        $this->addFlash("success", "Le contenu est désormais considéré comme rendu.");
        return $this->redirectToRoute('profil');
    }

}