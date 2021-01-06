<?php

namespace App\Controller\Dashboard;

use App\Repository\LivreRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * Class DashboardController
 * @package App\Controller
 * @Route("/dashboard")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="dashboard_index")
     * @param LivreRepository $livreRepository
     * @return Response
     */
    public function index(LivreRepository $livreRepository): Response
    {
        $livresPretes = $livreRepository->livresPreterDeUtilisateur($this->getUser()->getId());
        $livresEmpruntes = $livreRepository->livresEmprunterDeUtilisateur($this->getUser()->getId());

        return $this->render('dashboard/default/index.html.twig', [
            'livresPretes' => $livresPretes,
            'livresEmpruntes' => $livresEmpruntes,
        ]);
    }
}
