<?php

namespace App\Controller;

use App\Entity\Etat\LivreEtat;
use App\Entity\Livre;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiController
 * @package App\Controller
 * @Route("/api")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/livre/{hash}/reserver", name="api_livre_reserver", methods={"GET", "POST"})
     * @param Livre $livre
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function reserver(Livre $livre, EntityManagerInterface $entityManager): Response
    {
        if ($livre->getEtat() === LivreEtat::DISPONIBLE && $livre->getReserverPar() === null) {

            $livre->setEtat(LivreEtat::INDISPONIBLE);
            $livre->setReserverPar($this->getUser());
            $entityManager->flush();

            $this->addFlash("success", "Livre réserver avec succès.");
            return $this->redirectToRoute('dashboard_livre_reserved');
        }

        $this->addFlash("danger", "Impossible de réserver ce livre.");
        return $this->redirectToRoute('dashboard_livre_show', ['hash' => $livre->getHash()]);
    }

    /**
     * @Route("/livre/{hash}/rendre", name="api_livre_rendre", methods={"GET", "POST"})
     * @param Livre $livre
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function rendre(Livre $livre, EntityManagerInterface $entityManager): Response
    {
        if ($livre->getEtat() == LivreEtat::INDISPONIBLE) {

            $livre->setEtat(LivreEtat::DISPONIBLE);
            $livre->setReserverPar(null);

            // N'est pas remis a disposition si prochaine réservation bloqué
            if ($livre->getBloquerProchaineReservation() == true) {
                $livre->setEtat(LivreEtat::INDISPONIBLE);
            }

            $entityManager->flush();

            $this->addFlash("success", "Livre rendu avec succès.");
            return $this->redirectToRoute('dashboard_livre_reserved');
        }

        $this->addFlash("danger", "Impossible de rendre ce livre.");
        return $this->redirectToRoute('dashboard_livre_show', ['hash' => $livre->getHash()]);
    }
}
