<?php

# src/Controller/SecurityController.php
namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class SecurityController extends AbstractController
{
    #[IsGranted('PUBLIC_ACCESS')]
    #[Route('/login', name: 'app_login')]
    public function login(
        Request $request,
        AuthenticationUtils $auth,
        TokenStorageInterface $tokens
    ): Response {
        $user = $this->getUser();

        if ($user instanceof User && !$user->isVerified()) {
            $tokens->setToken(null);
            $request->getSession()->invalidate();
            $this->addFlash('error', "Votre adresse email n'est pas encore vérifiée. Veuillez cliquer sur le lien reçu.");
            return $this->redirectToRoute('app_login');
        }

        if ($user instanceof User) {
            $session = $request->getSession();
            $targetPath = $session && $session->has('_security.main.target_path')
                ? $session->get('_security.main.target_path')
                : null;
            if ($targetPath) {
                return $this->redirect($targetPath);
            }
            return $this->redirectToRoute('app_dashboard');
        }

        $form = $this->createForm(LoginType::class);

        return $this->render('security/login.html.twig', [
            'loginForm' => $form->createView(),
            'last_username' => $auth->getLastUsername(),
            'error' => $auth->getLastAuthenticationError(),
        ]);
    }

    #[IsGranted('PUBLIC_ACCESS')]
    #[Route('/logout', name: 'app_logout')]
    public function logout(): void {}
}
