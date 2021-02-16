<?php

namespace App\Controller\Dashboard;

use App\Entity\Etat\LivreEtat;
use App\Entity\Livre;
use App\Repository\LivreRepository;
use App\Form\LivreISBNType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * Class DashboardController
 * @package App\Controller
 * @Route("/dashboard/livre")
 */
class LivreController extends AbstractController
{
    /**
     * @Route("/", name="dashboard_livre_index")
     * @param LivreRepository $livreRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @param Breadcrumbs $breadcrumbs
     * @return Response
     */
    public function index(
        LivreRepository $livreRepository,
        PaginatorInterface $paginator,
        Request $request,
        Breadcrumbs $breadcrumbs
    ): Response
    {
        $breadcrumbs->addItem("Dashboard", $this->generateUrl('dashboard_index'));
        $breadcrumbs->addItem("Livres", $this->generateUrl('dashboard_livre_index'));
        $breadcrumbs->addItem("Livres disponibles");

        $livres = $livreRepository->livresDisponibles();

        $pagination = $paginator->paginate(
            $livres->getQuery(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );

        return $this->render('dashboard/livre/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/reserved", name="dashboard_livre_reserved")
     * @param LivreRepository $livreRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @param Breadcrumbs $breadcrumbs
     * @return Response
     */
    public function reserved(
        LivreRepository $livreRepository,
        PaginatorInterface $paginator,
        Request $request,
        Breadcrumbs $breadcrumbs
    ): Response
    {
        $breadcrumbs->addItem("Dashboard", $this->generateUrl('dashboard_index'));
        $breadcrumbs->addItem("Livres", $this->generateUrl('dashboard_livre_index'));
        $breadcrumbs->addItem("Mes livres");

        $livresPretes = $livreRepository->livresPreterDeUtilisateur($this->getUser()->getId());
        $livresEmpruntes = $livreRepository->livresEmprunterDeUtilisateur($this->getUser()->getId());

        return $this->render('dashboard/livre/reserved.html.twig', [
            'livresPretes' => $livresPretes,
            'livresEmpruntes' => $livresEmpruntes,
        ]);
    }

    /**
     * @Route("/new", name="dashboard_livre_new", methods={"GET","POST"})
     * @param Request $request
     * @param Breadcrumbs $breadcrumbs
     * @return Response
     */
    public function new(
        Request $request,
        Breadcrumbs $breadcrumbs
    ): Response
    {
        $breadcrumbs->addItem("Dashboard", $this->generateUrl('dashboard_index'));
        $breadcrumbs->addItem("Livres", $this->generateUrl('dashboard_livre_index'));
        $breadcrumbs->addItem("Créer");

        $livre = new Livre();
        $livre->setHash(uniqid());

        $form = $this->createForm(LivreISBNType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($livre);
            $entityManager->flush();

            $this->addFlash("success","Livre créé.");
            return $this->redirectToRoute('livre_index');
        }

        return $this->render('dashboard/livre/find.html.twig', [
            'livre' => $livre,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{hash}", name="dashboard_livre_show", methods={"GET"})
     * @param Livre $livre
     * @param Breadcrumbs $breadcrumbs
     * @return Response
     */
    public function show(Livre $livre, Breadcrumbs $breadcrumbs): Response
    {
        $breadcrumbs->addItem("Dashboard", $this->generateUrl('dashboard_index'));
        $breadcrumbs->addItem("Livres", $this->generateUrl('dashboard_livre_index'));
        $breadcrumbs->addItem("Informations du livre");

        return $this->render('dashboard/livre/show.html.twig', [
            'livre' => $livre,
        ]);
    }
}
