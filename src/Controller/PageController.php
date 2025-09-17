<?php

// src/Controller/HomeController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Form\ContactType;
use App\Repository\TicketRepository;
use App\Repository\CompanyRepository;
use App\Repository\ContactRepository;
use App\Enum\TicketStatus;

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

    #[IsGranted('ROLE_USER')]
    #[Route('/dashboard', name: 'app_dashboard', methods: ['GET'])]
    public function index(
        TicketRepository $ticketRepo,
        CompanyRepository $companyRepo,
        ContactRepository $contactRepo
    ): Response {
        $now = new \DateTimeImmutable();

        // Agrégation des statistiques tickets en 1 requête
        $stats = $ticketRepo->getDashboardStats(
            $now,
            [TicketStatus::New->value, TicketStatus::InProgress->value, TicketStatus::Waiting->value], // open
            [TicketStatus::Waiting->value], // waiting
            [TicketStatus::Resolved->value, TicketStatus::Closed->value, TicketStatus::Canceled->value] // closed
        );

        // Compteurs globaux
        $ticketsCount    = (int) ($stats['total'] ?? 0);
        $openTicketsCount = (int) ($stats['openCnt'] ?? 0);
        $waitingCount     = (int) ($stats['waitingCnt'] ?? 0);
        $overdueCount     = (int) ($stats['overdueCnt'] ?? 0);

        // Compteurs annexes (company / contact)
        $companiesCount  = (int) $companyRepo->count([]);
        $contactsCount   = (int) $contactRepo->count([]);

        // Derniers tickets avec préchargement des relations pour éviter le N+1
        $recentTickets = $ticketRepo->createQueryBuilder('t')
            ->leftJoin('t.company_id', 'co')->addSelect('co')
            ->leftJoin('t.assigned_to_id', 'u')->addSelect('u')
            ->orderBy('t.id', 'DESC')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();

        return $this->render('page/dashboard.html.twig', [
            'ticketsCount'     => $ticketsCount,
            'companiesCount'   => $companiesCount,
            'contactsCount'    => $contactsCount,
            'openTicketsCount' => $openTicketsCount,
            'waitingCount'     => $waitingCount,
            'overdueCount'     => $overdueCount,
            'recentTickets'    => $recentTickets,
        ]);
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
