<?php

namespace App\Repository;

use App\Model\CartItem;

interface CartRepositoryInterface
{
    public function getCartKey(int $userId): string;

    public function getCart(int $userId): array;

    public function getItem(int $userId, int $productId): ?CartItem;
    
    public function addToCart(int $userId, CartItem $cartItem): void;

    public function updateCart(int $userId, CartItem $cartItem): void;

    public function removeFromCart(int $userId, int $productId): void;

    public function clearCart(int $userId): void;

}
