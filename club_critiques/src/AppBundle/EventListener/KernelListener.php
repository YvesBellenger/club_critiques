<?php
namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class KernelListener
{
    private $em;
    private $container;

    public function __construct($container, $em) {
        $this->em = $em;
        $this->container = $container;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->getRequestType() !== \Symfony\Component\HttpKernel\HttpKernel::MASTER_REQUEST) {
            return;
        }

        if ($this->container->get('security.token_storage')->getToken()) {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $user->setLastIp($event->getRequest()->getClientIp());
            $this->em->persist($user);
            $this->em->flush();
        }
        $sql = "SELECT ip FROM `black_list`";


        $connection = $this->em->getConnection();
        $statement = $connection->prepare($sql);
        $statement->execute();
        $results = $statement->fetchAll();
        $bannedIps = array();
        foreach ($results as $result) {
            $bannedIps[] = $result['ip'];
        }
        if (in_array($event->getRequest()->getClientIp(), $bannedIps)) {
            $event->setResponse(new Response('Your IP is banned', 403));
        }

    }
}