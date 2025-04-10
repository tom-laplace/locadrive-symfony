<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class JwtAuthenticator extends AbstractAuthenticator
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization') &&
               strpos($request->headers->get('Authorization'), 'Bearer ') === 0;
    }

    public function authenticate(Request $request): Passport
    {
        $token = str_replace('Bearer ', '', $request->headers->get('Authorization'));
        
        if (empty($token)) {
            throw new CustomUserMessageAuthenticationException('Token JWT manquant');
        }

        // Ici, nous devrions vérifier et décoder le token JWT
        // Mais comme nous n'avons pas encore installé le bundle JWT, nous simulerons simplement
        // une authentification basique avec l'email stocké dans le token (à des fins de démonstration uniquement)
        
        // En production, vous utiliseriez lexik/jwt-authentication-bundle ou un autre bundle JWT

        // Simulons un email extrait du token pour cette démonstration
        $email = 'admin@example.com'; // Remplacer par la logique de décodage du token
        
        return new SelfValidatingPassport(
            new UserBadge($email, function ($userIdentifier) {
                return $this->entityManager->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Authentification réussie, laisser la requête continuer
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'error' => $exception->getMessage()
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}