<?php
namespace App\Repository;

use App\Model\CartItem;
use Predis\Client;

class CartRepository implements CartRepositoryInterface
{
    private Client $redis;

    public function __construct(Client $redis)
    {
        $this->redis = $redis;
    }

    public function getCartKey(int $userId): string
    {
        return 'cart:' . $userId;
    }

    /**
     * Get the cart as an array of CartItem objects
     */
    public function getCart(int $userId): array
    {
        $itemsJson = $this->redis->hGetAll($this->getCartKey($userId));
        $cartItems = [];

        foreach ($itemsJson as $productId => $json) {
            $data = json_decode($json, true);
            if ($data) {
                $cartItems[] = new CartItem(
                    (int)$productId,         // use hash key
                    $data['name'] ?? '',
                    (int)($data['quantity'] ?? 0),
                    (float)($data['price'] ?? 0),
                    (int)$userId             // userId passed as argument
                );
            }
        }

        return $cartItems;
    }

    /**
     * Get cart items as an array of CartItem objects
     * (Same as getCart; can keep for interface compatibility)
     */
    public function getItem(int $userId, int $productId): ?CartItem
    {
        $json = $this->redis->hGet($this->getCartKey($userId), $productId);

        if (!$json) {
            return null;
        }

        $data = json_decode($json, true);

        return new CartItem(
            $productId,
            $data['name'] ?? '',
            (int)($data['quantity'] ?? 0),
            (float)($data['price'] ?? 0),
            $userId
        );
}

    /**
     * Add item to cart (or update if exists)
     */
    public function addToCart(int $userId, CartItem $cartItem): void
    {
        $this->redis->hSet(
            $this->getCartKey($userId),
            $cartItem->getProductId(),
            $cartItem->toJson()
        );
    }

    /**
     * Update item in cart (overwrite)
     */
    public function updateCart(int $userId, CartItem $cartItem): void
    {
        $this->addToCart($userId, $cartItem);
    }

    /**
     * Remove a product from cart
     */
    public function removeFromCart(int $userId, int $productId): void
    {
        $this->redis->hDel($this->getCartKey($userId), $productId);
    }

    /**
     * Clear the entire cart
     */
    public function clearCart(int $userId): void
    {
        $this->redis->del($this->getCartKey($userId));
    }
}