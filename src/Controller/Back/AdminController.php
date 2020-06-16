<?php

namespace App\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class AdminController
 * @package App\Controller\Back
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin_index")
     */
    public function index()
    {
        return $this->render('back/admin/index.html.twig');
    }

    /**
     * @Route("/components", name="admin_components")
     */
    public function components()
    {
        return $this->render('back/admin/components.html.twig');
    }

    /**
     * @Route("/menu", name="admin_menu")
     */
    public function menu()
    {
        return $this->render("back/admin/menu.html.twig");
    }

    /**
     * @Route("/navbar", name="admin_navbar")
     */
    public function navbar()
    {
        return $this->render("back/admin/navbar.html.twig");
    }
}
