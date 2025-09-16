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
    #[Route('/dashboard', name: 'app_dashboard', methods: ['GET'])]
    public function index(
        TicketRepository $ticketRepo,
        CompanyRepository $companyRepo,
        ContactRepository $contactRepo
    ): Response {
        $now = new \DateTimeImmutable();

        // Compteurs
        $ticketsCount    = $ticketRepo->count([]);
        $companiesCount  = $companyRepo->count([]);
        $contactsCount   = $contactRepo->count([]);

        // Statuts utiles (doivent exister dans ta data)
        $openStatuses    = ['new', 'in_progress', 'waiting'];
        $closedStatuses  = ['resolved', 'closed', 'canceled'];

        // Tickets ouverts
        $openTicketsCount = $ticketRepo->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.ticket_status IN (:st)')
            ->setParameter('st', $openStatuses)
            ->getQuery()
            ->getSingleScalarResult();

        // En attente
        $waitingCount = $ticketRepo->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.ticket_status = :st')
            ->setParameter('st', 'waiting')
            ->getQuery()
            ->getSingleScalarResult();

        // En retard (due_at passée et pas clôturé)
        $overdueCount = $ticketRepo->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.due_at IS NOT NULL')
            ->andWhere('t.due_at < :now')
            ->andWhere('t.ticket_status NOT IN (:closed)')
            ->setParameter('now', $now)
            ->setParameter('closed', $closedStatuses)
            ->getQuery()
            ->getSingleScalarResult();

        // Derniers tickets
        $recentTickets = $ticketRepo->findBy([], ['id' => 'DESC'], 20);

        return $this->render('page/dashboard.html.twig', [
            'ticketsCount'     => (int)$ticketsCount,
            'companiesCount'   => (int)$companiesCount,
            'contactsCount'    => (int)$contactsCount,
            'openTicketsCount' => (int)$openTicketsCount,
            'waitingCount'     => (int)$waitingCount,
            'overdueCount'     => (int)$overdueCount,
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
