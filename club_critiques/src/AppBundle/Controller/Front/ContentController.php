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
        $modeAdd = false;
        if ($request->query->has('modeAdd')) {
            $modeAdd = true;
        }
        $doctrine = $this->getDoctrine();
        $categoryRepository = $doctrine->getRepository('AppBundle:Category');

        /** Filters **/
        $categories = $categoryRepository->findBy(array('parentCategory' => null, 'status' => 1), array('name' => 'ASC'));
        $subcategories = $categoryRepository->getSubCategories();
        $authors = $doctrine->getRepository('AppBundle:Author')->findByStatus(1);

        /** Contents **/
        $category = $categoryRepository->findOneByCode('livre');
        $contents = $doctrine->getRepository('AppBundle:Content')->findBy(array('status' => 1), array('title' => 'ASC'), 8);

        return $this->render('contents/contenus.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
            'controller' => 'contenus',
            'contents' => $contents,
            'categories' => $categories,
            'authors' => $authors,
            'selected_category_id' => 0,
            'selected_sub_category_id' => 0,
            'selected_author_id' => 0,
            'modeAdd' => $modeAdd,
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
        $contents = $doctrine->getRepository('AppBundle:Content')->getByFilters($category, $sub_category, $author);

        return $this->render('contents/content-list.html.twig', [
            'contents' => $contents ?: null,
            'subcategories' => $sub_categories,
            'authors' => $authors,
            'categories' => $categories,
            'selected_category_id' => $category_id,
            'selected_author_id' => $author_id,
            'selected_sub_category_id' => $sub_category_id
        ]);
    }

    /**
     * @Route("/contenus/loadMore", name="contents_load_more")
     */
    public function loadMoreAction(Request $request)
    {
        $limit = 8;
        $offset = $request->get('offset');
        $contents = $this->getDoctrine()->getRepository('AppBundle:Content')->findBy(array('status' => 1, array('title' => 'ASC'), $limit, $offset));
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
            'note_user' => $note_user
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
     * @Route("/content/remove", name="user_remove_content")
     */
    public function userRemoveContentAction(Request $request)
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
}