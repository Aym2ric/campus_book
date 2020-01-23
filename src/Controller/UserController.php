<?php

namespace App\Controller;

use App\Entity\User;
use App\Filter\UserFilterType;
use App\Form\UserCreateType;
use App\Form\UserEditPasswordType;
use App\Form\UserEditType;
use App\Repository\UserRepository;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdaterInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("/admin/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET", "POST"})
     */
    public function index(UserRepository $userRepository, FormFactoryInterface $formFactory, FilterBuilderUpdaterInterface $filterBuilderUpdater, Breadcrumbs $breadcrumbs, Request $request): Response
    {
        $breadcrumbs->addItem("Administration", $this->generateUrl('admin_index'));
        $breadcrumbs->addItem("Utilisateurs", $this->generateUrl('user_index'));
        $breadcrumbs->addItem("Liste");

        $form = $formFactory->create(UserFilterType::class);

        if ($request->query->has($form->getName())) {
            // Récupération des valeurs du formulaires
            $form->submit($request->query->get($form->getName()));

            // Initialisation du QueryBuilder (Constructeur de requête)
            $entityManager = $this->getDoctrine()->getManager();
            $filterBuilder = $entityManager->getRepository(User::class)->createQueryBuilder('e');

            // Construction de la requête en fonction des paramètres du formulaire
            $filterBuilderUpdater->addFilterConditions($form, $filterBuilder);

            // Récupère le DQL et execute la requête
            $query = $entityManager->createQuery($filterBuilder->getDql());
            $users = $query->getResult();

            return $this->render('user/index.html.twig', [
                'form' => $form->createView(),
                'users' => $users,
            ]);
        }


        return $this->render('user/index.html.twig', [
            'form' => $form->createView(),
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder, Breadcrumbs $breadcrumbs): Response
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

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder, Breadcrumbs $breadcrumbs): Response
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

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'formPassword' => $formPassword->createView(),
        ]);
    }

    /**
     * @Route("/delete/ajax/", name="user_delete_ajax", methods={"POST"})
     */
    public function delete_ajax(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy(["id"=> $request->request->get("userId")]);
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash("success","Utilisateur supprimé");

        return $this->json(["etat" => true]);
    }

    /**
     * @Route("/delete/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(User $user): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash("success","Utilisateur supprimé");
        return $this->redirectToRoute('user_index');
    }
}
