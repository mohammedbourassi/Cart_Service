<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class ApiUser implements UserInterface
{
    private int|string $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->userId;
    }

    public function getRoles(): array
    {
        return [];
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserId()
    {
        return $this->userId;
    }
}