<?php
// src/AppBundle/Controller/CRUDController.php

namespace AppBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CRUDController extends Controller
{
    public function banIpAction($id)
    {
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT * FROM `black_list` WHERE ip = :ip");
        $statement->bindValue('ip', $object->getLastIp());
        $statement->execute();
        $results = $statement->fetchAll();

        if (count($results) == 0 ){
            $statement = $connection->prepare("INSERT INTO `black_list` (ip) VALUES (:ip)");
            $statement->bindValue('ip', $object->getLastIp());
            $statement->execute();
        }

        $this->addFlash('sonata_flash_success', 'L\'ip de l\'utilisateur a bien été bannie');

        return new RedirectResponse($this->admin->generateUrl('list'));
    }
}