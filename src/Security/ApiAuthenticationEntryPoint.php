<?php
// src/Security/ApiAuthenticationEntryPoint.php
namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class ApiAuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        $message = $authException?->getMessage() 
                 ?? 'Full authentication is required to access this resource.';

        return new JsonResponse(['error' => $message], 401);
    }
}