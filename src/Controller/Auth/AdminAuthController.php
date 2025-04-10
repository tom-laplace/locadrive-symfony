<?php

namespace App\Controller\Auth;

use App\Application\Auth\Login\AdminLoginUseCase;
use App\Application\Auth\Login\LoginRequest;
use App\Application\Auth\Register\CreateAdminUseCase;
use App\Application\Auth\Register\RegisterRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/admin')]
class AdminAuthController extends AbstractController
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    #[Route('/login', name: 'admin_login', methods: ['POST'])]
    public function login(Request $request, AdminLoginUseCase $adminLoginUseCase): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['email']) || !isset($data['password'])) {
                return $this->json([
                    'error' => 'Email et mot de passe requis'
                ], Response::HTTP_BAD_REQUEST);
            }

            $loginRequest = new LoginRequest($data['email'], $data['password']);
            $result = $adminLoginUseCase->execute($loginRequest);
            $admin = $result['admin'];

            return $this->json([
                'id' => $admin->getId(),
                'email' => $admin->getEmail(),
                'roles' => $admin->getRoles(),
                'token' => $result['token'],
                'message' => 'Connexion réussie'
            ]);

        } catch (AuthenticationException $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Une erreur est survenue lors de la connexion'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/create', name: 'admin_create', methods: ['POST'])]
    public function create(Request $request, CreateAdminUseCase $createAdminUseCase): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['email']) || !isset($data['password'])) {
                return $this->json([
                    'error' => 'Email et mot de passe requis'
                ], Response::HTTP_BAD_REQUEST);
            }

            $admin = $createAdminUseCase->execute($data['email'], $data['password']);

            return $this->json([
                'id' => $admin->getId(),
                'email' => $admin->getEmail(),
                'roles' => $admin->getRoles(),
                'message' => 'Administrateur créé avec succès'
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Une erreur est survenue lors de la création de l\'administrateur'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}