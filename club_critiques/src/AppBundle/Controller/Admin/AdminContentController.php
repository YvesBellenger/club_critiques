<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Category;
use AppBundle\Entity\Content;
use Proxies\__CG__\AppBundle\Entity\Author;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Acl\Exception\Exception;

class AdminContentController extends Controller
{

    /**
     * @Route("/admin/api/content", name="sonata_admin_api")
     */
    public function apiAction(Request $request)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('fos_user_security_login');
        } else if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $this->addFlash("danger", "Vous n'avez pas l'autorisation d'accéder à cette page.");
            return $this->redirectToRoute('homepage');
        } else {
            $api_manager = $this->container->get('app.util.api_manager');
            $contents = $api_manager->findBooks('harry');
            return $this->render('admin/index.html.twig', [
                'contents' => $contents
            ]);
        }
    }

    /**
     * @Route("/admin/api/content/search", name="sonata_admin_api_search")
     */
    public function apiSearchAction(Request $request)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('fos_user_security_login');
        } else if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $this->addFlash("danger", "Vous n'avez pas l'autorisation d'accéder à cette page.");
            return $this->redirectToRoute('homepage');
        } else {
            $keywords = $request->get('keywords');
            $api_manager = $this->container->get('app.util.api_manager');
            $contents = $api_manager->findBooks($keywords);
            return $this->render('admin/content-list.html.twig', [
                'contents' => $contents
            ]);
        }
    }

    /**
     * @Route("/admin/api/content/add", name="sonata_admin_api_add")
     */
    public function apiAddAction(Request $request)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('fos_user_security_login');
        } else if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $this->addFlash("danger", "Vous n'avez pas l'autorisation d'accéder à cette page.");
            return $this->redirectToRoute('homepage');
        } else {
            try {
                $em = $this->getDoctrine()->getManager();

                $content = new Content();
                if ($request->get('authors')) {
                    $authors = explode(',', $request->get('authors'));
                    foreach ($authors as $_author) {
                        $author = $this->getDoctrine()->getRepository('AppBundle:Author')->findOneByName(trim($_author));
                        if (!$author) {
                            $author = new Author();
                            $author->setName(trim($_author));
                            $author->setStatus(1);
                            $em->persist($author);
                            $em->flush();
                        }
                        $content->addAuthor($author);
                    }
                }
                if ($request->get('category')) {
                    $category = $this->getDoctrine()->getRepository('AppBundle:Category')->findOneByName(trim($request->get('category')));
                    if (!$category) {
                        $category = new Category();
                        $category->setStatus(1);
                        $category->setParentCategory($this->getDoctrine()->getRepository('AppBundle:Category')->find(1));
                        $category->setCode($request->get('category'));
                        $category->setName($request->get('category'));
                        $em->persist($category);
                        $em->flush();
                    }
                    $content->setCategory($category);
                }
                if ($request->get('title')) {
                    $title = trim($request->get('title'));
                    $content->setTitle($title);
                }
                if ($request->get('description')) {
                    $description = trim($request->get('description'));
                    $content->setDescription($description);
                }
                if ($request->get('image')) {
                    $image = trim($request->get('image'));
                    $content->setImage($image);
                }
                if ($request->get('publishedDate')) {
                    $publishedDate = $request->get('publishedDate');
                    $content->setPublishedDate($publishedDate);
                }
                $content->setStatus(1);
                $em->persist($content);
                $em->flush();
                $response = 'Le contenu a bien été ajouté';
            } catch (\Exception $e) {
                $response = $e->getMessage();
            }

        }
        return new JsonResponse($response);
    }
}