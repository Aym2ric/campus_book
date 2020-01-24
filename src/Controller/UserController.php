<?php

namespace App\Controller;

use App\Entity\User;
use App\Filter\UserFilterType;
use App\Form\UserCreateType;
use App\Form\UserEditPasswordType;
use App\Form\UserEditType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
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
    public function index(EntityManagerInterface $entityManager, UserRepository $userRepository, PaginatorInterface $paginator, FormFactoryInterface $formFactory, FilterBuilderUpdaterInterface $filterBuilderUpdater, Breadcrumbs $breadcrumbs, Request $request): Response
    {
        $breadcrumbs->addItem("Administration", $this->generateUrl('admin_index'));
        $breadcrumbs->addItem("Utilisateurs", $this->generateUrl('user_index'));
        $breadcrumbs->addItem("Liste");

        $isForm = false;

        $form = $formFactory->create(UserFilterType::class);
        $queryBuilder = $entityManager->getRepository(User::class)->createQueryBuilder('q');

        if ($request->query->has($form->getName())) {
            // Sauvegarde les champs sélectionnés du formulaire
            $form->submit($request->query->get($form->getName()));

            // Test Username
            if(!empty($request->query->get("user_filter")["username"])) {
                $queryBuilder->andWhere('q.username = :username')
                    ->setParameter("username", $request->query->get("user_filter")["username"]);
            }

            // Test Rôles
            if(!empty($request->query->get("user_filter")["roles"])) {
                $i = 0;
                foreach($request->query->get("user_filter")["roles"] as $role) {
                    $i++;
                    $queryBuilder->andWhere('q.roles LIKE ?'.$i.'')
                        ->setParameter($i, '%'.$role.'%');
                }
            }

            // Test Enabled
            if(!empty($request->query->get("user_filter")["enabled"])) {
                $queryBuilder->andWhere('q.enabled = :enabled')
                    ->setParameter("enabled", $request->query->get("user_filter")["enabled"]);
            }

            // On renvoie true pour confirmer a la vue qu'il y a une recherche en cours
            // Afin de déplier automatiquement la box du formulaire
            $isForm = true;
        }

        //var_dump($queryBuilder->getDql());
        $query = $queryBuilder->getQuery();

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );

        return $this->render('user/index.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination,
            'isForm' => $isForm,
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
