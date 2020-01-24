<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserCreateType;
use App\Form\UserEditPasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/password", name="app_password", methods={"GET","POST"})
     */
    public function password(Request $request, Breadcrumbs $breadcrumbs, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $breadcrumbs->addItem("Administration", $this->generateUrl('admin_index'));
        $breadcrumbs->addItem("Mon Compte", $this->generateUrl('app_password'));
        $breadcrumbs->addItem("Changement de mot de passe");


        $user = $this->getUser();
        $form = $this->createForm(UserEditPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $password = $passwordEncoder->encodePassword($user, $form["password"]->getData());
            $user->setPassword($password);
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash("success","Mot de passe modifié");
            return $this->redirectToRoute('index');
        }

        return $this->render('security/password.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     * @throws \Exception
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}
