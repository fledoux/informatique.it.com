<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\Ldap\LdapInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;

class LdapAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private LdapInterface $ldap,
        private UserRepository $userRepository,
        private UrlGeneratorInterface $urlGenerator,
        private array $ldapRoleMap = []
    ) {}

    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === 'app_login' && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $username = $request->request->get('_username');
        $password = $request->request->get('_password');

        if (!$username || !$password) {
            throw new CustomUserMessageAuthenticationException('Nom d’utilisateur ou mot de passe manquant.');
        }

        $userDn = sprintf('uid=%s,cn=users,dc=yellowcactus,dc=com', $username);
        $ldapAuthenticated = false;
        $roles = [];

        try {
            $this->ldap->bind($userDn, $password);
            $ldapAuthenticated = true;

            $query = $this->ldap->query('cn=groups,dc=yellowcactus,dc=com', sprintf('(member=%s)', $userDn));
            foreach ($query->execute() as $group) {
                $cn = $group->getAttribute('cn')[0] ?? null;
                if ($cn && isset($this->ldapRoleMap[$cn])) {
                    $roles = array_merge($roles, $this->ldapRoleMap[$cn]);
                }
            }

            $roles = array_unique($roles);

            if (empty($roles)) {
                throw new CustomUserMessageAuthenticationException('Aucun groupe autorisé trouvé dans LDAP.');
            }
        } catch (\Exception $e) {
            $ldapAuthenticated = false;
        }

        if ($ldapAuthenticated) {
            return new Passport(
                new UserBadge($username, function (string $userIdentifier) use ($roles) {
                    $user = $this->userRepository->findOneBy(['username' => $userIdentifier]);
                    if (!$user) {
                        throw new CustomUserMessageAuthenticationException('Utilisateur inconnu (LDAP).');
                    }
                    $user->setRoles(array_unique(array_merge($user->getRoles(), $roles)));
                    return $user;
                }),
                new CustomCredentials(
                    function ($credentials, $user) {
                        return true;
                    },
                    $password
                )
            );
        }

        // Fallback SQL (par username OU email)
        return new Passport(
            new UserBadge($username, function (string $userIdentifier) {
                $user = $this->userRepository->findOneBy(['username' => $userIdentifier])
                      ?? $this->userRepository->findOneBy(['email' => $userIdentifier]);

                if (!$user) {
                    throw new CustomUserMessageAuthenticationException('Utilisateur inconnu (SQL).');
                }

                return $user;
            }),
            new PasswordCredentials($password)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse($this->urlGenerator->generate('app_meeting_index'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }
}