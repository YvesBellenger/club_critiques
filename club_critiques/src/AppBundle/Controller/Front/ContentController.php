<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Acl\Exception\Exception;

class ContentController extends Controller
{
    /**
     * @Route("/contenus", name="contenus")
     */
    public function contenusActions (Request $request)
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

        return $this->render('contents/contenus.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'controller' => 'contenus',
            'contents' => $contents,
            'categories' => $categories,
            'selected_category_id' => 0,
            'selected_sub_category_id' => 0,
            'modeAdd' => $modeAdd,
            'subcategories' => $subcategories
        ]);
    }

    /**
     * @Route("/contenus/filters", name="contents_on_filter_change")
     */
    public function ajaxOnFilterChange (Request $request)
    {
        $doctrine = $this->getDoctrine();
        $categoryRepository = $doctrine->getRepository('AppBundle:Category');
        $categories = $categoryRepository->findBy(array('status' => 1, 'parentCategory' => null));
        $category_id = $request->get('category_id');
        $sub_category_id = $request->get('sub_category_id');
        if ($sub_category_id > 0) {
            $sub_category = $categoryRepository->find($sub_category_id);
            $contents = $doctrine->getRepository('AppBundle:Content')->findBy(array('category' => $sub_category));
            $sub_categories = $categoryRepository->findBy(array('parentCategory' => $sub_category->getParentCategory()));
            $category_id = $sub_category->getParentCategory()->getId();
        } else if ($category_id > 0) {
            $category = $categoryRepository->find($category_id);
            $contents = $doctrine->getRepository('AppBundle:Content')->getByCategory($category);
            $sub_categories = $categoryRepository->findBy(array('parentCategory' => $category));
        } else {
            $contents = $doctrine->getRepository('AppBundle:Content')->findBy(array('status' => 1));
            $sub_categories = $categoryRepository->getSubCategories();
        }
        return $this->render('contents/content-list.html.twig', [
            'contents' => $contents ? : null,
            'subcategories' => $sub_categories,
            'categories' => $categories,
            'selected_category_id' => $category_id,
            'selected_sub_category_id' => $sub_category_id
        ]);
    }

    /**
     * @Route("/contenu/{id}", name="contenu")
     */
    public function contenuActions (Request $request)
    {
        $doctrine = $this->getDoctrine();
        $categoryRepository = $doctrine->getRepository('AppBundle:Category');
        $content = $doctrine->getRepository('AppBundle:Content')->find($request->get('id'));
        $other_contents = $doctrine->getRepository('AppBundle:Content')->getSuggestions($content);
        shuffle($other_contents);
        return $this->render('contents/content.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'controller' => 'oeuvre',
            'content' => $content,
            'other_contents' => $other_contents
        ]);
    }

    /**
     * @Route("/content/add", name="user_add_content")
     */
    public function userAddContentAction (Request $request) {
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
        }
    }
}