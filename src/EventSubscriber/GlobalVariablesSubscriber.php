<?php
# /Users/fledoux/Sites/informatique.it.com/src/EventSubscriber/GlobalVariablesSubscriber.php

namespace App\EventSubscriber;

use App\Repository\ContactRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class GlobalVariablesSubscriber implements EventSubscriberInterface
{
	public function __construct(
		private ContactRepository $contactRepository,
		private Environment $twig
	) {}

	public function onKernelController(ControllerEvent $event): void
	{
		$count = $this->contactRepository->countAll();
		$this->twig->addGlobal('contactsCount', $count);
	}

	public static function getSubscribedEvents(): array
	{
		return [
			KernelEvents::CONTROLLER => 'onKernelController',
		];
	}
}
