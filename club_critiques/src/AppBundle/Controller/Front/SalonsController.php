<?php

namespace AppBundle\Controller\Front;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SalonsController extends Controller
{
    /**
     * @Route("/salons/salon/exemple", name="salon")
     */

    public function salonAction(Request $request)
    {
        return $this->render('salons/salon.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'controller' => 'salon'
        ]);
    }

    /**
     * @Route("/salons/liste", name="salons-liste")
     */

    public function salonListeAction(Request $request)
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


        return $this->render('salons/salons-list.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'controller' => 'salons-liste',
            'contents' => $contents,
            'categories' => $categories,
            'selected_category_id' => 0,
            'selected_sub_category_id' => 0,
            'modeAdd' => $modeAdd,
            'subcategories' => $subcategories
        ]);
    }
}
