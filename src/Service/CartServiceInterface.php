<?php

namespace App\Service;

use App\Model\CartItem;

interface CartServiceInterface
{

    public function addItem(CartItem $item);
    public function removeItem(int $userId, int $productId);
    public function clearCart(int $userId);
    public function getCart(int $userId);
    public function updateItem(int $userId, CartItem $item);

}
