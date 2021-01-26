<?php

namespace App\Controller\Dashboard;

use App\Entity\Type;
use App\Filter\TypeFilterType;
use App\Form\TypeCreateType;
use App\Form\TypeEditType;
use App\Repository\TypeRepository;
use App\Repository\ThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * Class DashboardController
 * @package App\Controller
 * @Route("/dashboard/genre")
 */
class TypeController extends AbstractController
{
    /**
     * @Route("/", name="dashboard_type_index", methods={"GET", "POST"})
     * @param TypeRepository $typeRepository
     * @param PaginatorInterface $paginator
     * @param Breadcrumbs $breadcrumbs
     * @param Request $request
     * @return Response
     */
    public function index(
        TypeRepository $typeRepository,
        PaginatorInterface $paginator,
        Breadcrumbs $breadcrumbs,
        Request $request
    ): Response
    {
        $breadcrumbs->addItem("Administration", $this->generateUrl('admin_index'));
        $breadcrumbs->addItem("Types", $this->generateUrl('type_index'));
        $breadcrumbs->addItem("Liste");

        $search = $typeRepository->searchTypeNbBook();

        $pagination = $paginator->paginate(
            $search->getQuery(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );

        return $this->render('dashboard/type/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
