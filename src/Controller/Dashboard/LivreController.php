<?php

namespace App\Controller\Dashboard;

use App\Entity\Etat\LivreEtat;
use App\Entity\Livre;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @return Response
     */
    public function index(
        LivreRepository $livreRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response
    {
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
     * @return Response
     */
    public function reserved(
        LivreRepository $livreRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response
    {
        $livres = $livreRepository->livresParUtilisateur($this->getUser()->getId());

        $pagination = $paginator->paginate(
            $livres->getQuery(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );

        return $this->render('dashboard/livre/reserved.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/{hash}", name="dashboard_livre_show", methods={"GET"})
     */
    public function show(Livre $livre): Response
    {
        return $this->render('dashboard/livre/show.html.twig', [
            'livre' => $livre,
        ]);
    }

    /**
     * @Route("/{hash}/reserver", name="dashboard_livre_reserver", methods={"GET", "POST"})
     */
    public function reserver(Livre $livre, EntityManagerInterface $entityManager): Response
    {
        dump(LivreEtat::DISPONIBLE);
        if ($livre->getEtat() === LivreEtat::DISPONIBLE) {

            $livre->setEtat(LivreEtat::PRETE);
            $livre->setReserverPar($this->getUser());
            $entityManager->flush();

            $this->addFlash("success", "Livre réserver avec succès.");
            return $this->redirectToRoute('dashboard_livre_show', ['hash' => $livre->getHash()]);
        }

        $this->addFlash("success", "Impossible de réserver ce livre  .");
        return $this->redirectToRoute('dashboard_livre_show', ['hash' => $livre->getHash()]);
    }

    /**
     * @Route("/{hash}/rendre", name="dashboard_livre_rendre", methods={"GET", "POST"})
     * @param Livre $livre
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function rendre(Livre $livre, EntityManagerInterface $entityManager): Response
    {
        if ($livre->getEtat() == LivreEtat::PRETE) {

            $livre->setEtat(LivreEtat::DISPONIBLE);
            $livre->setReserverPar(null);

            // STOCK = n'est pas remis a disposition si prochaine réservation bloqué
            if ($livre->getBloquerProchaineReservation() == true) {
                $livre->setEtat(LivreEtat::STOCK);
            }

            $entityManager->flush();

            $this->addFlash("success", "Livre rendu avec succès.");
            return $this->redirectToRoute('dashboard_livre_show', ['id' => $livre->getId()]);
        }

        $this->addFlash("success", "Impossible de rendre ce livre.");
        return $this->redirectToRoute('dashboard_livre_show', ['id' => $livre->getId()]);
    }
}
