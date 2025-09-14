<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('PUBLIC_ACCESS')]
#[Route('/register')]
class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier) {}

    #[Route('', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $user->setRoles(['ROLE_USER']);
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('mail@mail.com', 'Sender Name'))
                    ->to((string) $user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_register_pending');
        }

        // return $this->render('registration/register.html.twig', ['registrationForm' => $form,]);
        return $this->render('registration/wait.html.twig');
    }

    #[Route('/pending', name: 'app_register_pending')]
    public function pendingPage()
    {
        return $this->render('registration/pending.html.twig', []);
    }

    #[Route('/verify', name: 'app_verify', methods: ['GET'])]
    public function verifyUserEmail(
        Request $request,
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        Security $security
    ): Response {
        // On ne force PAS la connexion ici
        $id = $request->query->get('id');
        if (!$id) {
            $this->addFlash('verify_email_error', 'Lien invalide: identifiant manquant.');
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);
        if (!$user) {
            $this->addFlash('verify_email_error', 'Utilisateur introuvable.');
            return $this->redirectToRoute('app_register');
        }

        try {
            // Valide la signature + setIsVerified(true) + flush()
            $this->emailVerifier->handleEmailConfirmation($request, $user);

            // (Optionnel) attribuer le rôle si tu le fais à la vérification
            $roles = $user->getRoles();
            if (!in_array('ROLE_USER', $roles, true)) {
                $user->setRoles(array_values(array_unique([...$roles, 'ROLE_USER'])));
                $entityManager->flush();
            }

            // (Optionnel) connecter l’utilisateur juste après vérification
            $security->login($user, 'form_login', 'main');

            $this->addFlash('success', 'Votre email a bien été vérifié.');
            return $this->redirectToRoute('app_dashboard');
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('verify_email_error', $translator->trans($e->getReason(), [], 'VerifyEmailBundle'));
            return $this->redirectToRoute('app_register');
        }
    }
}
