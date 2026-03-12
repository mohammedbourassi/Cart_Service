<?php
namespace App\Service;

interface JWTServiceInterface
{
    public function decodeToken(string $token, string $secretKey, string $algorithm): array;
    public function getUserId(string $token, string $secretKey, string $algorithm): int;
}