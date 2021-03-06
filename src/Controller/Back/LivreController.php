<?php

namespace App\Controller\Back;

use App\Entity\Livre;
use App\Filter\LivreFilterType;
use App\Form\LivreCreateType;
use App\Form\LivreEditType;
use App\Repository\LivreRepository;
use App\Repository\ThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("/admin/livre")
 */
class LivreController extends AbstractController
{
    /**
     * @Route("/", name="livre_index", methods={"GET", "POST"})
     * @param LivreRepository $livreRepository
     * @param PaginatorInterface $paginator
     * @param Breadcrumbs $breadcrumbs
     * @param Request $request
     * @return Response
     */
    public function index(
        LivreRepository $livreRepository,
        PaginatorInterface $paginator,
        Breadcrumbs $breadcrumbs,
        Request $request
    ): Response
    {
        $breadcrumbs->addItem("Administration", $this->generateUrl('admin_index'));
        $breadcrumbs->addItem("Livres", $this->generateUrl('livre_index'));
        $breadcrumbs->addItem("Liste");

        $form = $this->createForm(LivreFilterType::class);
        $form->handleRequest($request);

        $isForm = false;
        $search = $livreRepository->search();

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $livreRepository->search($form->getData());
            $isForm = true;
        }

        $pagination = $paginator->paginate(
            $search->getQuery(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );

        return $this->render('back/livre/index.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination,
            'isForm' => $isForm,
        ]);
    }

    /**
     * @Route("/new", name="livre_new", methods={"GET","POST"})
     * @param Request $request
     * @param Breadcrumbs $breadcrumbs
     * @param KernelInterface $kernel
     * @return Response
     */
    public function new(
        Request $request,
        Breadcrumbs $breadcrumbs,
        KernelInterface $kernel
    ): Response
    {
        $breadcrumbs->addItem("Administration", $this->generateUrl('admin_index'));
        $breadcrumbs->addItem("Livres", $this->generateUrl('livre_index'));
        $breadcrumbs->addItem("Créer");

        $livre = new Livre();
        $livre->setHash(uniqid());

        $form = $this->createForm(LivreCreateType::class, $livre);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->get('urlImage')->getData() != null && $form->get('urlImage')->getData() != $request->getUriForPath('/vich/livre_default.PNG')) {
                $upload_path = $kernel->getProjectDir() . '/public/vich/upload/image/';
                $filename = uniqid();
                $ext = pathinfo($form->get('urlImage')->getData(), PATHINFO_EXTENSION);
                if($ext == null) {
                    $ext = 'PNG';
                }
                file_put_contents($upload_path . $filename . '.' . $ext, file_get_contents($form->get('urlImage')->getData()));
                $livre->setUrlImage( '/vich/upload/image/'.$filename . '.' . $ext);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($livre);
            $entityManager->flush();

            $this->addFlash("success", "Livre créé.");
            return $this->redirectToRoute('livre_index');
        }

        return $this->render('back/livre/new.html.twig', [
            'livre' => $livre,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="livre_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Livre $livre
     * @param Breadcrumbs $breadcrumbs
     * @return Response
     */
    public function edit(
        Request $request,
        Livre $livre,
        Breadcrumbs $breadcrumbs
    ): Response
    {
        $breadcrumbs->addItem("Administration", $this->generateUrl('admin_index'));
        $breadcrumbs->addItem("Livres", $this->generateUrl('livre_index'));
        $breadcrumbs->addItem("Modifier");

        $form = $this->createForm(LivreEditType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash("success", "Livre modifié.");
            return $this->redirectToRoute('livre_index');
        }

        return $this->render('back/livre/edit.html.twig', [
            'livre' => $livre,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/delete", name="livre_delete", methods={"GET","POST"})
     * @param Livre $livre
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Livre $livre, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($livre);
        $entityManager->flush();

        $this->addFlash("success", "Livre supprimé.");
        return $this->redirectToRoute('livre_index');
    }
}
