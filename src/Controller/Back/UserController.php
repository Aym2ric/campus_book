<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Filter\UserFilterType;
use App\Form\UserCreateType;
use App\Form\UserEditPasswordType;
use App\Form\UserEditType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdaterInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package App\Controller
 * @Route("/admin/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET", "POST"})
     * @param UserRepository $userRepository
     * @param PaginatorInterface $paginator
     * @param Breadcrumbs $breadcrumbs
     * @param Request $request
     * @return Response
     */
    public function index(
        UserRepository $userRepository,
        PaginatorInterface $paginator,
        Breadcrumbs $breadcrumbs,
        Request $request
    ): Response
    {
        $breadcrumbs->addItem("Administration", $this->generateUrl('admin_index'));
        $breadcrumbs->addItem("Utilisateurs", $this->generateUrl('user_index'));
        $breadcrumbs->addItem("Liste");

        $form = $this->createForm(UserFilterType::class);
        $form->handleRequest($request);

        $isForm = false;
        $search = $userRepository->search();

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $userRepository->search($form->getData());
            $isForm = true;
        }

        $pagination = $paginator->paginate(
            $search->getQuery(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );

        return $this->render('back/user/index.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination,
            'isForm' => $isForm,
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Breadcrumbs $breadcrumbs
     * @return Response
     */
    public function new(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        Breadcrumbs $breadcrumbs
    ): Response
    {
        $breadcrumbs->addItem("Administration", $this->generateUrl('admin_index'));
        $breadcrumbs->addItem("Utilisateurs", $this->generateUrl('user_index'));
        $breadcrumbs->addItem("Créer");

        $user = new User();

        $form = $this->createForm(UserCreateType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash("success","Utilisateur créé");
            return $this->redirectToRoute('user_index');
        }

        return $this->render('back/user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Breadcrumbs $breadcrumbs
     * @return Response
     */
    public function edit(
        Request $request,
        User $user,
        UserPasswordEncoderInterface $passwordEncoder,
        Breadcrumbs $breadcrumbs
    ): Response
    {
        $breadcrumbs->addItem("Administration", $this->generateUrl('admin_index'));
        $breadcrumbs->addItem("Utilisateurs", $this->generateUrl('user_index'));
        $breadcrumbs->addItem("Modifier");

        // Informations générales
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash("success","Utilisateur modifié");
            return $this->redirectToRoute('user_index');
        }

        // Changement du mot de passe
        $formPassword = $this->createForm(UserEditPasswordType::class, $user);
        $formPassword->handleRequest($request);

        if ($formPassword->isSubmitted() && $formPassword->isValid()) {

            $password = $passwordEncoder->encodePassword($user, $formPassword["password"]->getData());
            $user->setPassword($password);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash("success","Mot de passe modifié");
            return $this->redirectToRoute('user_index');
        }

        return $this->render('back/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'formPassword' => $formPassword->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="user_delete", methods={"GET","POST"})
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(User $user, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash("success", "Utilisateur supprimé.");
        return $this->redirectToRoute('user_index');
    }
}
