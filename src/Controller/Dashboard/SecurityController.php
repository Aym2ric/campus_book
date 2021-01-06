<?php

namespace App\Controller\Dashboard;

use App\Form\UserEditYourPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SecurityController
 * @package App\Controller\Dashboards
 * @Route("/dashboard")
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/password", name="dashboard_password", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Breadcrumbs $breadcrumbs
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function password(Request $request, EntityManagerInterface $entityManager, Breadcrumbs $breadcrumbs, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $breadcrumbs->addItem("Dashboard", $this->generateUrl('dashboard_index'));
        $breadcrumbs->addItem("Mon Compte", $this->generateUrl('dashboard_password'));
        $breadcrumbs->addItem("Changement de mot de passe");

        $user = $this->getUser();
        $form = $this->createForm(UserEditYourPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($passwordEncoder->isPasswordValid($user, $form["oldpassword"]->getData())) {
                // Ancien mot de passe correct
                $password = $passwordEncoder->encodePassword($user, $form["password"]->getData());
                $user->setPassword($password);
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash("success", "Mot de passe modifié.");
                return $this->redirectToRoute('dashboard_password');

            } else {
                // Ancien mot de passe incorrect
                $form->get("oldpassword")->addError(new FormError("Ancien mot de passe incorrect."));

                return $this->render('dashboard/security/password.html.twig', [
                    'form' => $form->createView(),
                ]);
            }
        }

        return $this->render('dashboard/security/password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
