<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
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
     * @param MailerInterface $mailer
     * @return RedirectResponse
     * @throws TransportExceptionInterface
     */
    public function index(MailerInterface $mailer)
    {
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
