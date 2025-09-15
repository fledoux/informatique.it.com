<?php

// src/Controller/HomeController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Form\ContactType;

final class PageController extends AbstractController
{
    #[IsGranted('PUBLIC_ACCESS')]
    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard');
        }
        $form = $this->createForm(ContactType::class);
        return $this->render('page/home.html.twig', ['form' => $form->createView()]);
    }

    #[IsGranted('PUBLIC_ACCESS')]
    #[Route('/legal', name: 'app_legal')]
    public function legal(): Response
    {
        $form = $this->createForm(ContactType::class);
        return $this->render('page/legal.html.twig', ['form' => $form->createView()]);
    }

    #[IsGranted('PUBLIC_ACCESS')]
    #[Route('/rgpd', name: 'app_rgpd')]
    public function rgpd(): Response
    {
        $form = $this->createForm(ContactType::class);
        return $this->render('page/rgpd.html.twig', ['form' => $form->createView()]);
    }

    #[IsGranted('PUBLIC_ACCESS')]
    #[Route('/cgv', name: 'app_cgv')]
    public function cgv(): Response
    {
        $form = $this->createForm(ContactType::class);
        return $this->render('page/cgv.html.twig', ['form' => $form->createView()]);
    }
}
