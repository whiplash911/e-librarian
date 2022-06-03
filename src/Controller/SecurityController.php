<?php

namespace App\Controller;

use App\Service\UserService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class SecurityController extends AbstractController 
{
    public function __construct(
        private UserService $userService,
        private LoggerInterface $logger
    ){}

    /**
     * Register a user and return token, so they are able to send subsequent requests
     * 
     * @Route("/api/register", name="api_register", methods={"POST"})
     */
    public function register(Request $request, JWTTokenManagerInterface $JWTManager): JsonResponse
    {
        try {
            $user = $this->userService->create($request->getContent());

            return $this->json([
                'token' => $JWTManager->create($user),
            ]);
        } catch (\Throwable $e) {
            return $this->json([
                'error' => 'Unable to register the user. Please try again later.',
                'status' => 500,
            ], 500);
        }   
    }
}
