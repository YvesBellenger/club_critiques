<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\User;
use AppBundle\Entity\Note;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Acl\Exception\Exception;

class ContentController extends Controller
{
    /**
     * @Route("/contenus", name="contenus")
     */
    public function contenusActions(Request $request)
    {
        $doctrine = $this->getDoctrine();
        $categoryRepository = $doctrine->getRepository('AppBundle:Category');


        /** Filters **/
        $categories = $categoryRepository->findBy(array('parentCategory' => null, 'status' => 1), array('name' => 'ASC'));
        $subcategories = $categoryRepository->getSubCategories();
        $authors = $doctrine->getRepository('AppBundle:Author')->findByStatus(1);

        /** Contents **/
        $category = $categoryRepository->findOneByCode('livre');
        $contents = $doctrine->getRepository('AppBundle:Content')->findBy(array('status' => 1), array('title' => 'ASC'), 12);

        /*$paginator = $this->get('knp_paginator');
        $contents = $paginator->paginate(
            $contents,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',12)
        );*/

        return $this->render('contents/contenus.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
            'controller' => 'contenus',
            'contents' => $contents,
            'categories' => $categories,
            'authors' => $authors,
            'selected_category_id' => 0,
            'selected_sub_category_id' => 0,
            'selected_author_id' => 0,
            'selected_orderBy' => 0,
            'offset' => 12,
            'subcategories' => $subcategories
        ]);
    }

    /**
     * @Route("/contenus/filters", name="contents_on_filter_change")
     */
    public function ajaxOnFilterChange(Request $request)
    {
        $doctrine = $this->getDoctrine();
        $categoryRepository = $doctrine->getRepository('AppBundle:Category');
        $categories = $categoryRepository->findBy(array('status' => 1, 'parentCategory' => null));
        $sub_categories = $categoryRepository->getSubCategories();
        $authors = $doctrine->getRepository('AppBundle:Author')->findByStatus(1);
        $category_id = $request->get('category_id');
        $sub_category_id = $request->get('sub_category_id');
        $author_id = $request->get('author_id');
        $title = $request->get('title');
        $orderBy = $request->get('orderBy');
        $author = $category = $sub_category = null;
        if ($author_id > 0) {
            $author = $doctrine->getRepository('AppBundle:Author')->find($author_id);
        }
        if ($sub_category_id > 0) {
            $sub_category = $categoryRepository->find($sub_category_id);
            $sub_categories = $categoryRepository->findBy(array('parentCategory' => $sub_category->getParentCategory()));
            $category_id = $sub_category->getParentCategory()->getId();
        }
        if ($category_id > 0) {
            $category = $categoryRepository->find($category_id);
            $sub_categories = $categoryRepository->findBy(array('parentCategory' => $category));
        }
        $contents = $doctrine->getRepository('AppBundle:Content')->getByFilters($category, $sub_category, $author, $title, $orderBy);
        return $this->render('contents/content-list.html.twig', [
            'contents' => $contents ?: null,
            'subcategories' => $sub_categories,
            'authors' => $authors,
            'categories' => $categories,
            'selected_category_id' => $category_id,
            'title' => $title,
            'offset' => 12,
            'selected_author_id' => $author_id,
            'selected_orderBy' => $orderBy,
            'selected_sub_category_id' => $sub_category_id
        ]);
    }

    /**
     * @Route("/contenus/loadMore", name="contents_load_more")
     */
    public function loadMoreAction(Request $request)
    {

        $limit = 12;
        $offset = $request->get('offset');
        $filters = $request->get('filters');
        $category_id = $filters['category_id'];
        $sub_category_id = $filters['sub_category_id'];
        $author_id = $filters['author_id'];
        $title = $filters['title'];
        $orderBy = $filters['orderBy'];
        $author = $category = $sub_category = null;
        if ($author_id > 0) {
            $author = $this->getDoctrine()->getRepository('AppBundle:Author')->find($author_id);
        }
        if ($sub_category_id > 0) {
            $sub_category = $this->getDoctrine()->getRepository('AppBundle:Category')->find($sub_category_id);
            $category_id = $sub_category->getParentCategory()->getId();
        }
        if ($category_id > 0) {
            $category = $this->getDoctrine()->getRepository('AppBundle:Category')->find($category_id);
        }
        $contents = $this->getDoctrine()->getRepository('AppBundle:Content')->getByFilters($category, $sub_category, $author, $title, $orderBy, $limit, $offset);
        return $this->render('contents/load-more.html.twig', [
            'contents' => $contents ?: null,
        ]);
    }

    /**
     * @Route("/contenu/{id}", name="contenu")
     */
    public function contenuActions(Request $request)
    {
        $doctrine = $this->getDoctrine();
        $categoryRepository = $doctrine->getRepository('AppBundle:Category');
        $content = $doctrine->getRepository('AppBundle:Content')->find($request->get('id'));
        $other_contents = $doctrine->getRepository('AppBundle:Content')->getSuggestions($content);
        $user = $this->getUser();
        $note_user = $doctrine->getRepository('AppBundle:Note')->findBy(array(
            'content' => $content,
            'user' => $user,
        ));


        $wanted_contents = $doctrine->getRepository('AppBundle:User')->getUsersContentWanted($content);
        $to_share_contents = $doctrine->getRepository('AppBundle:User')->getUsersContentToShare($content);

        shuffle($other_contents);
        $from_lobby = false;
        if ($request->query->has('frmlby') && $request->query->has('lby')) {
            $from_lobby = true;
        }
        return $this->render('contents/content.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
            'controller' => 'oeuvre',
            'user' => $user,
            'content' => $content,
            'from_lobby' => $from_lobby,
            'lobby_id' => $request->query->has('lby') ? $request->get('lby') : 0,
            'other_contents' => $other_contents,
            'note_user' => $note_user,
            'wanted_contents' => $wanted_contents,
            'to_share_contents' => $to_share_contents
        ]);
    }

    /**
     * @Route("/content/add", name="user_add_content")
     */
    public function userAddContentAction(Request $request)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('fos_user_security_login');
        } else {
            $content = $this->getDoctrine()->getRepository('AppBundle:Content')->find($request->get('content_id'));
            if (isset($content)) {
                if ($request->get('type') == User::CONTENT_TO_SHARE && !$user->contentsWanted->contains($content) && !$user->contentsShared->contains($content)) {
                    $user->addContentToShare($content);
                    $this->addFlash("success", "Le contenu est désormais considéré comme disponible.");
                } else if ($request->get('type') == User::CONTENT_WANTED && !$user->contentsToShare->contains($content) && !$user->contentsShared->contains($content)) {
                    $user->addContentWanted($content);
                    $this->addFlash("success", "Le contenu est désormais considéré comme recherché.");
                } else if ($request->get('type') == User::CONTENT_SHARED && !$user->contentsWanted->contains($content) && $user->contentsToShare->contains($content)) {
                    $user->removeContentToShare($content);
                    $user->addContentShared($content);
                    $this->addFlash("success", "Le contenu est désormais considéré comme prêté.");
                } else if ($request->get('type') > 3){
                    $this->addFlash("success", "Aucune action disponible.");
                } else{
                    $this->addFlash("success", "Une erreur est survenue.");
                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
            }
        }

        $referer = $request->headers->get('referer');
        if(isset($referer)) {
            return $this->redirect($referer);
        }
        else{
            return $this->redirectToRoute('contenus');
        }
    }

    /**
     * @Route("/content/remove", name="user_remove_content")
     */
    public function userRemoveContentAction(Request $request)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('fos_user_security_login');
        } else {
            $content = $this->getDoctrine()->getRepository('AppBundle:Content')->find($request->get('content_id'));
            if(isset($content)) {
                if ($request->get('type') == User::CONTENT_TO_SHARE) {
                    $user->removeContentToShare($content);
                    $this->addFlash("success", "Le contenu est désormais considéré comme indisponible.");
                } else if ($request->get('type') == User::CONTENT_WANTED) {
                    $user->removeContentWanted($content);
                    $this->addFlash("success", "Le contenu est désormais considéré comme non recherché.");
                } else if ($request->get('type') == User::CONTENT_SHARED) {
                    $user->removeContentShared($content);
                    $user->addContentToShare($content);
                    $this->addFlash("success", "Le contenu est désormais considéré comme rendu.");
                }
                else if ($request->get('type') > 3){
                    $this->addFlash("success", "Aucune action disponible.");
                } else{
                    $this->addFlash("success", "Une erreur est survenue.");
                }
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
            }
        }
        $referer = $request->headers->get('referer');
        if(isset($referer)) {
            return $this->redirect($referer);
        }
        else{
            return $this->redirectToRoute('contenus');
        }
    }

    /**
     * @Route("/contenu/{id}/add", name="contenu_add_content")
     */
    public function contenuAddContentAction(Request $request)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('fos_user_security_login');
        } else {
            $content = $this->getDoctrine()->getRepository('AppBundle:Content')->find($request->get('content_id'));
            if ($request->get('type') == User::CONTENT_TO_SHARE) {
                $user->addContentToShare($content);
            } else {
                $user->addContentWanted($content);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }
        return new Response();
    }

    /**
     * @Route("/contenu/{id}/remove", name="contenu_remove_content")
     */
    public function contenuRemoveContentAction(Request $request)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('fos_user_security_login');
        } else {
            $content = $this->getDoctrine()->getRepository('AppBundle:Content')->find($request->get('content_id'));
            if ($request->get('type') == User::CONTENT_TO_SHARE) {
                $user->removeContentToShare($content);
            } else {
                $user->removeContentWanted($content);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }
        $this->addFlash("success", "Le contenu a bien été supprimé de votre liste.");
        return $this->redirectToRoute('profil');
    }

    /**
     * @Route("/contenus/note/save", name="contenu_update_note")
     */
    public function saveNoteAction(Request $request)
    {
        $user = $this->getUser();
        $doctrine = $this->getDoctrine();
        $content = $doctrine->getRepository('AppBundle:Content')->find($request->get("content-id"));
        $note = $request->get("note");
        $note_user = $doctrine->getRepository('AppBundle:Note')->findBy(array(
            'content' => $content,
            'user' => $user,
        ));
        if(isset($content) && $note >= 0 && $note <=4) {
            if (!isset($note_user[0])) {
                $nouvelle_note = new Note();
                $nouvelle_note->setNote($note);
                $nouvelle_note->setUser($user);
                $nouvelle_note->setContent($content);
                $nouvelle_note->setStatus(1);
                $em = $this->getDoctrine()->getManager();
                $em->persist($nouvelle_note);
                $em->flush();
            } else {
                $note_user[0]->setNote($note);
                $em = $this->getDoctrine()->getManager();
                $em->persist($note_user[0]);
                $em->flush();
            }
            return new Response();
        }
        else
        {
            return new Response();
        }

    }

    /**
     * @Route("/contenus/suggestion", name="contenus_suggest")
     */
    public function suggestContentAction(Request $request)
    {
        $doctrine = $this->getDoctrine();
        $user = $this->getUser();
        $form = $this->createForm('AppBundle\Form\SuggestionType',null,array(
            'method' => 'POST'
        ));
        if (!$user) {
            return $this->redirectToRoute('fos_user_security_login');
        } else {
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                if ($this->sendEmailSuggestContent($form->getData(), $user)) {
                    $this->addFlash("success", "L'email a correctement été envoyé.");
                    return $this->redirectToRoute('contenus_suggest');
                } else {
                    $this->addFlash("success", "Une erreur est survenue.");
                    return $this->redirectToRoute('contenus_suggest');
                }
            }
        }

            return $this->render('contents/suggest-content.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
                'controller' => 'content',
                'user' => $user,
                'form' => $form->createView()
            ]);
        }
    }

    private function sendEmailSuggestContent($data, $sender){
        $mailer = $this->container->get('mailer');
        $message = (new \Swift_Message('test'))
            ->setFrom('noreply@club-cc.com')
            ->setTo('lynohaz@gmail.com')
            ->setSubject('[Suggestion de contenus] - '.$data["titre"])
            ->setBody(
                $this->renderView
                (
                    'mails/suggestion-contenus.html.twig',
                    array
                    (
                        'data' => $data,
                        'sender' => $sender
                    )
                ),'text/html'
            );
        return $mailer->send($message);

    }
}