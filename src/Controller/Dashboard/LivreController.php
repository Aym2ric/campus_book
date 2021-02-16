<?php

namespace App\Controller\Dashboard;

use App\Entity\Etat\LivreEtat;
use App\Entity\Livre;
use App\Entity\Theme;
use App\Filter\Dashboard\LivreFilterType;
use App\Form\LivrePreterType;
use App\Repository\LivreRepository;
use App\Repository\ThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
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

        $form = $this->createForm(LivreFilterType::class);
        $form->handleRequest($request);

        $livres = $livreRepository->livresDisponibles();


        if ($form->isSubmitted() && $form->isValid()) {
            $livres = $livreRepository->searchDashboard($form->getData());
        }

        $pagination = $paginator->paginate(
            $livres->getQuery(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );

        return $this->render('dashboard/livre/index.html.twig', [
            'pagination' => $pagination,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/preter", name="dashboard_livre_preter")
     * @param Request $request
     * @param Breadcrumbs $breadcrumbs
     * @param ThemeRepository $themeRepository
     * @param EntityManagerInterface $entityManager
     * @param KernelInterface $kernel
     * @return Response
     */
    public function preter(
        Request $request,
        Breadcrumbs $breadcrumbs,
        KernelInterface $kernel
    )
    {
        $breadcrumbs->addItem("Dashboard", $this->generateUrl('dashboard_index'));
        $breadcrumbs->addItem("Livres", $this->generateUrl('dashboard_livre_index'));
        $breadcrumbs->addItem("Prêter un livre");

        $livre = new Livre();
        $livre->setHash(uniqid());
        $livre->setEtat(LivreEtat::DEMAMDE_VALIDATION);
        $livre->setPreterPar($this->getUser());

        $form = $this->createForm(LivrePreterType::class, $livre);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->get('urlImage')->getData() != null && $form->get('urlImage')->getData() != $request->getUriForPath('/vich/livre_default.PNG')) {
                $upload_path = $kernel->getProjectDir() . '/public/vich/upload/image/';
                $filename = uniqid();
                $ext = pathinfo($form->get('urlImage')->getData(), PATHINFO_EXTENSION);
                file_put_contents($upload_path . $filename . '.' . $ext, file_get_contents($form->get('urlImage')->getData()));
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($livre);
            $entityManager->flush();

            $this->addFlash("success", "Prêt de livre réalisé avec succès.");
            return $this->redirectToRoute('dashboard_livre_reserved');
        }

        return $this->render('dashboard/livre/preter.html.twig', [
            'livre' => $livre,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/reserved", name="dashboard_livre_reserved")
     * @param LivreRepository $livreRepository
     * @param Breadcrumbs $breadcrumbs
     * @return Response
     */
    public function reserved(
        LivreRepository $livreRepository,
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
     * @Route("/{hash}", name="dashboard_livre_show", methods={"GET"})
     * @param Livre $livre
     * @param Breadcrumbs $breadcrumbs
     * @return Response
     */
    public function show(
        Livre $livre,
        Breadcrumbs $breadcrumbs
    ): Response
    {
        $breadcrumbs->addItem("Dashboard", $this->generateUrl('dashboard_index'));
        $breadcrumbs->addItem("Livres", $this->generateUrl('dashboard_livre_index'));
        $breadcrumbs->addItem("Informations du livre");

        return $this->render('dashboard/livre/show.html.twig', [
            'livre' => $livre,
        ]);
    }
}
