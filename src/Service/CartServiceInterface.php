<?php

namespace App\Service;

use App\Model\CartItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface CartServiceInterface
{

    public function getCart(Request $request);

    public function getItem(array $cart, int $productId): ?CartItem;

    public function addItem(array $cart, CartItem $dto): array;

    public function removeItem(array $cart, int $productId): array;

    public function clearCart(): array;

    public function updateItemQuantity(array $cart, int $productId, int $quantity): array;

    public function saveCart(Response $response, array $cart);

}
