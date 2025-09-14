<?php

// src/Controller/TestMailController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class TestMailController extends AbstractController
{
    #[Route('/_test-mail', name: 'test_mail')]
    public function __invoke(MailerInterface $mailer): Response
    {
        $mailer->send(
            (new Email())
                ->from('test@local.dev')
                ->to('me@local.dev')
                ->subject('Hello Mailpit')
                ->text('OK')
        );

        return new Response('sent');
    }
}