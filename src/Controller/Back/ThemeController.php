<?php

namespace App\Controller\Back;

use App\Entity\Theme;
use App\Form\ThemeType;
use App\Repository\ThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("/admin/theme")
 */
class ThemeController extends AbstractController
{
    /**
     * @Route("/{id}/edit", name="theme_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Breadcrumbs $breadcrumbs
     * @param EntityManagerInterface $entityManager
     * @param Theme $theme
     * @return Response
     */
    public function edit(
        Request $request,
        Breadcrumbs $breadcrumbs,
        EntityManagerInterface $entityManager,
        Theme $theme
    ): Response
    {
        $breadcrumbs->addItem("Administration", $this->generateUrl('admin_index'));
        $breadcrumbs->addItem("Types", $this->generateUrl('type_index'));
        $breadcrumbs->addItem("Modifier un thème");

        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash("success", "Thème modifié.");
            return $this->redirectToRoute('type_edit', ['id' => $theme->getType()->getId()]);
        }

        return $this->render('back/theme/edit.html.twig', [
            'theme' => $theme,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="theme_delete", methods={"GET", "POST"})
     * @param Theme $theme
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Theme $theme, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($theme);
        $entityManager->flush();

        $this->addFlash("success", "Thème supprimé.");
        return $this->redirectToRoute('type_edit', ['id' => $theme->getType()->getId()]);
    }
}
