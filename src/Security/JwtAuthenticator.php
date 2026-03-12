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
        // TODO: Implement supports() method.
        return $request->headers->has('Authorization');
    }

    public function authenticate(Request $request): Passport
    {
        // TODO: Implement authenticate() method.
         $authHeader = $request->headers->get('Authorization');
         

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            throw new AuthenticationException('Missing or invalid Authorization header');

        }

        $token = $matches[1];
        
        try {
            // ✅ Use your service to get the user ID
            $userId = $this->jwtService->getUserId($token, $this->jwtSecret, $this->jwtAlgorithm);
        } catch (\Exception $e) {
            throw new AuthenticationException('Invalid token: ' . $e->getMessage());
        }

        // Optional: attach userId to request attributes for controllers
        $request->attributes->set('userId', $userId);
        
        // Return a Passport with the UserBadge (Symfony internal)
        return new SelfValidatingPassport(
            new UserBadge($userId, function ($userIdentifier) {
                return new ApiUser($userIdentifier);
            })
        );
    
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // TODO: Implement onAuthenticationSuccess() method.
        return null; // continue to controller
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        // TODO: Implement onAuthenticationFailure() method.
        return new JsonResponse(['error' => $exception->getMessage()], 401);
    }

    //    public function start(Request $request, ?AuthenticationException $authException = null): Response
    //    {
    //        /*
    //         * If you would like this class to control what happens when an anonymous user accesses a
    //         * protected page (e.g. redirect to /login), uncomment this method and make this class
    //         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
    //         *
    //         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
    //         */
    //    }
}
