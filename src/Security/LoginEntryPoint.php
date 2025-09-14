<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class LoginEntryPoint implements AuthenticationEntryPointInterface
{
    public function __construct(
        private RouterInterface $router
    ) {}

    public function start(Request $request, \Throwable $authException = null): Response
    {
        return new RedirectResponse($this->router->generate('app_login'));
    }
}