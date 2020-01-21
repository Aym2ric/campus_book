<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin_index")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/menu", name="admin_menu")
     */
    public function menu()
    {
        return $this->render("admin/menu.html.twig");
    }

    /**
     * @Route("/navbar", name="admin_navbar")
     */
    public function navbar()
    {
        return $this->render("admin/navbar.html.twig");
    }


}
