<?php

namespace App\Controller\Back;

use App\Entity\Type;
use App\Form\TypeType;
use App\Repository\TypeRepository;
use App\Filter\TypeFilterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use App\Entity\Theme;
use App\Form\ThemeType;


/**
 * @Route("/admin/type")
 */
class TypeController extends AbstractController
{

    /**
     * @Route("/", name="type_index", methods={"GET", "POST"})
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

        $form = $this->createForm(TypeFilterType::class);
        $form->handleRequest($request);

        $isForm = false;
        $search = $typeRepository->search();

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $typeRepository->search($form->getData());
            $isForm = true;
        }

        $pagination = $paginator->paginate(
            $search->getQuery(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );

        return $this->render('back/type/index.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination,
            'isForm' => $isForm,
        ]);
    }

    /**
     * @Route("/new", name="type_new", methods={"GET","POST"})
     * @param Request $request
     * @param Breadcrumbs $breadcrumbs
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function new(
        Request $request,
        Breadcrumbs $breadcrumbs,
        EntityManagerInterface $entityManager
    ): Response
    {
        $breadcrumbs->addItem("Administration", $this->generateUrl('admin_index'));
        $breadcrumbs->addItem("Types", $this->generateUrl('type_index'));
        $breadcrumbs->addItem("Créer");

        $type = new Type();
        $form = $this->createForm(TypeType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($type);
            $entityManager->flush();

            return $this->redirectToRoute('type_index');
        }

        return $this->render('back/type/new.html.twig', [
            'type' => $type,
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/{id}/edit", name="type_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Breadcrumbs $breadcrumbs
     * @param EntityManagerInterface $entityManager
     * @param Type $type
     * @return Response
     */
    public function edit(
        Request $request,
        Breadcrumbs $breadcrumbs,
        EntityManagerInterface $entityManager,
        Type $type): Response
    {
        $breadcrumbs->addItem("Administration", $this->generateUrl('admin_index'));
        $breadcrumbs->addItem("Types", $this->generateUrl('type_index'));
        $breadcrumbs->addItem("Modifier");

        $form = $this->createForm(TypeType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('type_index');
        }

        // Formulaire créer un thème
        $theme = new Theme();
        $theme->setType($type);
        $formTheme = $this->createForm(ThemeType::class, $theme);
        $formTheme->handleRequest($request);

        if ($formTheme->isSubmitted() && $formTheme->isValid()) {
            $entityManager->persist($theme);
            $entityManager->flush();

            $this->addFlash("success", "Thème créé.");
            return $this->redirectToRoute('type_edit', ['id' => $theme->getType()->getId()]);
        }

        return $this->render('back/type/edit.html.twig', [
            'type' => $type,
            'form' => $form->createView(),
            'theme' => $theme,
            'formTheme' => $formTheme->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="type_delete", methods={"GET", "POST"})
     * @param Type $type
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Type $type, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($type);
        $entityManager->flush();

        $this->addFlash("success", "Type supprimé.");
        return $this->redirectToRoute('type_index');
    }
}
