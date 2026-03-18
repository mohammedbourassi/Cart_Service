<?php

namespace App\Service;

use App\Service\JWTServiceInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTService implements JWTServiceInterface
{
    public function decodeToken(string $token, string $secretKey, string $algorithm): array
    {
        try {
            return (array) JWT::decode($token, new Key($secretKey, $algorithm));
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Invalid token: ' . $e->getMessage());
        }
    }

    public function getUserId(string $token , string $secretKey, string $algorithm): int
    {
        $decodedToken = $this->decodeToken($token, $secretKey, $algorithm);
        return $decodedToken['userId'];
    }

    public function getUserRole(string $token, string $secretKey, string $algorithm): string
    {
        $decodedToken = $this->decodeToken($token, $secretKey, $algorithm);

        if (!isset($decodedToken['role'])) {
            throw new \InvalidArgumentException('Token does not contain a role');
        }

        return $decodedToken['role'];
    }
}