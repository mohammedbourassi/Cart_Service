<?php

namespace App\Security;

use App\Security\ApiUser;
use App\Service\JWTServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class JwtAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private JWTServiceInterface $jwtService,
        private string $jwtSecret, 
        private string $jwtAlgorithm
    ) {}

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization');
    }

    public function authenticate(Request $request): Passport
    {
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            throw new AuthenticationException('Missing or invalid Authorization header');
        }

        $token = $matches[1];

        try {
            // Decode token payload
            $payload = $this->jwtService->decodeToken($token, $this->jwtSecret, $this->jwtAlgorithm);
            $userId = $payload['userId'];
            $roles = isset($payload['role']) ? [$payload['role']] : []; // wrap as array
        } catch (\Exception $e) {
            throw new AuthenticationException('Invalid token: ' . $e->getMessage());
        }

        // Attach to request for controllers
        $request->attributes->set('userId', $userId);
        $request->attributes->set('roles', $roles);

        // Pass userId + roles to ApiUser 
        return new SelfValidatingPassport(
            new UserBadge($userId, fn($id) => new ApiUser($id, $roles))
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null; // continue to controller
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_UNAUTHORIZED);
    }
}