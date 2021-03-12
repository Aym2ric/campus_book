<?php

namespace App\Controller;

use App\Entity\Etat\LivreEtat;
use App\Entity\Livre;
use App\Entity\Theme;
use App\Repository\ThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class ApiController
 * @package App\Controller
 * @Route("/api")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/ajax/theme/new", name="api_theme_new", methods={"GET", "POST"})
     * @param EntityManagerInterface $entityManager
     * @param ThemeRepository $themeRepository
     * @param Request $request
     * @return JsonResponse
     */
    public function newTheme(
        EntityManagerInterface $entityManager,
        ThemeRepository $themeRepository,
        Request $request
    )
    {
        $nomTheme = $request->query->get("nomTheme");

        $existe = $themeRepository->findOneBy(['nom' => $nomTheme]);

        if (!$existe && $nomTheme != null) {
            $newTheme = new Theme();
            $newTheme->setNom($nomTheme);
            $entityManager->persist($newTheme);
            $entityManager->flush();

            $response = [
                'duplicate' => false,
                'id' => $newTheme->getId()
            ];

        } else {

            $response = [
                'duplicate' => true,
                'id' => $existe->getId()
            ];

        }

        return new JsonResponse($response);

    }

    /**
     * @Route("/livre/{hash}/reserver", name="api_livre_reserver", methods={"GET", "POST"})
     * @param Livre $livre
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function reserver(Livre $livre, EntityManagerInterface $entityManager): Response
    {
        if ($livre->getEtat() === LivreEtat::DISPONIBLE && $livre->getReserverPar() === null && !$livre->getBloquerProchaineReservation()) {

            $livre->setEtat(LivreEtat::INDISPONIBLE);
            $livre->setReserverPar($this->getUser());
            $entityManager->flush();

            $this->addFlash("success", "Livre réservé avec succès.");
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

    /**
     * @Route("/livre/{hash}/bloquer-prochaine-reservation", name="api_livre_bloquer_prochaine_reservation", methods={"GET", "POST"})
     * @param Livre $livre
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function bloquerProchaineReservation(Livre $livre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('ROLE_SUPER_ADMIN') || $livre->getPreterPar() === $this->getUser()) {
            $livre->setBloquerProchaineReservation(true);
            $entityManager->flush();

            $this->addFlash("success", "Prochaine reservation bloquée avec succès.");
            return $this->redirectToRoute('dashboard_livre_reserved');
        }

        $this->addFlash("danger", "Impossible de bloquer la prochaine réservation ce livre.");
        return $this->redirectToRoute('dashboard_livre_show', ['hash' => $livre->getHash()]);
    }

    /**
     * @Route("/livre/{hash}/debloquer-prochaine-reservation", name="api_livre_debloquer_prochaine_reservation", methods={"GET", "POST"})
     * @param Livre $livre
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function debloquerProchaineReservation(Livre $livre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('ROLE_SUPER_ADMIN') || $livre->getPreterPar() === $this->getUser()) {
            $livre->setBloquerProchaineReservation(false);
            $entityManager->flush();

            $this->addFlash("success", "Prochaine reservation débloquée avec succès.");
            return $this->redirectToRoute('dashboard_livre_reserved');
        }

        $this->addFlash("danger", "Impossible de débloquer les réservations de ce livre.");
        return $this->redirectToRoute('dashboard_livre_show', ['hash' => $livre->getHash()]);
    }

    /**
     * @Route("/infos_livre", name="infos_livre", methods={"GET", "POST"})
     */
    public function infosLivre(Request $request): JsonResponse
    {
        $isbn = $request->request->get('isbn');
        // if($isbn == null)
        //     return false;

        $response = $this->client->request(
            'GET',
            "https://www.googleapis.com/books/v1/volumes?q=isbn:" . $isbn
        );

        $statusCode = $response->getStatusCode();
        // $statusCode = 200
        $contentType = $response->getHeaders()['content-type'][0];
        // $contentType = 'application/json'
        // $content = $response->getContent();

        // $content = $response->toArray();

        return $content;
    }
}
