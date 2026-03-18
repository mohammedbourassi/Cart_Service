<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class ApiUser implements UserInterface
{
    private int|string $userId;
    private array $roles;

    public function __construct($userId, array $roles = [])
    {
        $this->userId = $userId;
        $this->roles = $roles;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->userId;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void {}

    public function getUserId()
    {
        return $this->userId;
    }
}