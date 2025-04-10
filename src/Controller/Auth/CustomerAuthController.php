<?php

namespace App\Controller\Auth;

use App\Application\Auth\Login\CustomerLoginUseCase;
use App\Application\Auth\Login\LoginRequest;
use App\Application\Auth\Register\CustomerRegisterUseCase;
use App\Application\Auth\Register\RegisterRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/customer')]
class CustomerAuthController extends AbstractController
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    #[Route('/register', name: 'customer_register', methods: ['POST'])]
    public function register(Request $request, CustomerRegisterUseCase $customerRegisterUseCase): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            // Vérifier les données obligatoires
            if (
                !isset($data['email']) || !isset($data['password']) ||
                !isset($data['firstName']) || !isset($data['lastName']) ||
                !isset($data['licenseObtainmentDate'])
            ) {
                return $this->json([
                    'error' => 'Tous les champs sont obligatoires: email, password, firstName, lastName, licenseObtainmentDate'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Convertir la date d'obtention de permis
            $licenseDate = null;
            try {
                $licenseDate = new \DateTime($data['licenseObtainmentDate']);
            } catch (\Exception $e) {
                return $this->json([
                    'error' => 'Format de date invalide pour licenseObtainmentDate. Utilisez le format YYYY-MM-DD.'
                ], Response::HTTP_BAD_REQUEST);
            }

            $registerRequest = new RegisterRequest(
                $data['email'],
                $data['password'],
                $data['firstName'],
                $data['lastName'],
                $licenseDate
            );

            $customer = $customerRegisterUseCase->execute($registerRequest);

            return $this->json([
                'id' => $customer->getId(),
                'email' => $customer->getEmail(),
                'firstName' => $customer->getFirstName(),
                'lastName' => $customer->getLastName(),
                'licenseObtainmentDate' => $customer->getLicenseObtainmentDate()->format('Y-m-d'),
                'message' => 'Compte client créé avec succès'
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Une erreur est survenue lors de la création du compte client',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/login', name: 'customer_login', methods: ['POST'])]
    public function login(Request $request, CustomerLoginUseCase $customerLoginUseCase): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['email']) || !isset($data['password'])) {
                return $this->json([
                    'error' => 'Email et mot de passe requis'
                ], Response::HTTP_BAD_REQUEST);
            }

            $loginRequest = new LoginRequest($data['email'], $data['password']);
            $customer = $customerLoginUseCase->execute($loginRequest);

            // Retourner une réponse JSON avec les informations du client
            return $this->json([
                'id' => $customer->getId(),
                'email' => $customer->getEmail(),
                'firstName' => $customer->getFirstName(),
                'lastName' => $customer->getLastName(),
                'roles' => $customer->getRoles(),
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

    #[Route('/logout', name: 'customer_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        // La déconnexion est gérée par le firewall de sécurité Symfony
        // Cette méthode ne sera normalement jamais exécutée
        return $this->json([
            'message' => 'Déconnexion réussie'
        ]);
    }
}