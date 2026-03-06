<?php

namespace App\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
class JWTService
{
    private $secretKey;
    private $algorithm;

    public function __construct(string $secretKey, string $algorithm = 'HS256')
    {
        $this->secretKey = $secretKey;
        $this->algorithm = $algorithm;
    }

    public function generateToken(array $payload, int $expiration = 3600): string
    {
        $payload['exp'] = time() + $expiration;
        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    public function decodeToken(string $token): array
    {
        try {
            return (array) JWT::decode($token, new Key($this->secretKey, $this->algorithm));
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Invalid token: ' . $e->getMessage());
        }
    }
}