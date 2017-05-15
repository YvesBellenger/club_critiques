<?php

namespace AppBundle\Controller\Front;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ContentController extends Controller
{
    /**
     * @Route("/contenus", name="contenus")
     */
    public function contenusActions (Request $request)
    {
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
        if ($request->get('category_id') > 0) {
            $cat_id = $request->get('category_id');
            $category = $categoryRepository->find($cat_id);
            $contents = $doctrine->getRepository('AppBundle:Content')->findBy(array('category' => $category));
        } else {
            $contents = $doctrine->getRepository('AppBundle:Content')->findBy(array('status' => 1));
//            $sub_categories =
        }

        return $this->render('contents/content-list.html.twig', [
            'contents' => $contents ? : null
        ]);
    }
}