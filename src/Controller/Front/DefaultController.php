<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package App\Controller\Front
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function index(MailerInterface $mailer)
    {
        $email = (new Email())
            ->from('hello@example.com')
            ->to('aym2ric@live.fr')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $mailer->send($email);

        return $this->redirectToRoute('app_login');
        //return $this->render("front/default/index.html.twig");
    }

    /**
     * @Route("/mentions-legales", name="mentions")    
     */
    public function mentions()
    {
        return $this->render("front/default/mentions-legales.html.twig");
    }

    /**
     * @Route("/contact", name="contact")    
     */
    public function contact()
    {
        return $this->render("front/default/contact.html.twig");
    }

    /**
     * @Route("/menu", name="menu")
     */
    public function menu()
    {
        return $this->render("front/default/menu.html.twig");
    }

    /**
     * @Route("/navbar", name="navbar")
     */
    public function navbar()
    {
        return $this->render("front/default/navbar.html.twig");
    }

}
