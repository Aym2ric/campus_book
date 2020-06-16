<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class DefaultController
 * @package App\Controller\Front
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render("front/default/index.html.twig");
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
