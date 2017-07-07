<?php

namespace AppBundle\Controller\Front;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/user/{id}", name="user")
     */
    public function userActions (Request $request)
    {
        $doctrine = $this->getDoctrine();
        $user = $doctrine->getRepository('AppBundle:User')->find($request->get('id'));
        $form = $this->createForm('AppBundle\Form\ContactType',null,array(
            'method' => 'POST'
        ));

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if($form->isValid()){
                if($this->sendEmail($form->getData())){
                    return $this->redirectToRoute('redirect_to_somewhere_now');
                }else{
                    var_dump("Errooooor :(");
                }
            }
        }

        return $this->render('profile/user.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'controller' => 'user',
            'user' => $user,
            'connected_user' => $this->getUser(),
            'form' => $form->createView()
        ]);
    }

    private function sendEmail($data){
        $myappContactMail = 'mycontactmail@mymail.com';
        $myappContactPassword = 'yourmailpassword';

        // In this case we'll use the ZOHO mail services.
        // If your service is another, then read the following article to know which smpt code to use and which port
        // http://ourcodeworld.com/articles/read/14/swiftmailer-send-mails-from-php-easily-and-effortlessly
        $transport = \Swift_SmtpTransport::newInstance('smtp.zoho.com', 465,'ssl')
            ->setUsername($myappContactMail)
            ->setPassword($myappContactPassword);

        $mailer = \Swift_Mailer::newInstance($transport);

        $message = \Swift_Message::newInstance("Our Code World Contact Form ". $data["subject"])
            ->setFrom(array($myappContactMail => "Message by ".$data["name"]))
            ->setTo(array(
                $myappContactMail => $myappContactMail
            ))
            ->setBody($data["message"]."<br>ContactMail :".$data["email"]);

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
        return $this->redirectToRoute('profil');
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
}