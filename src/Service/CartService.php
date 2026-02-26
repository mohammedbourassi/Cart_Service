<?php
namespace App\Service;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CartService
{
    private CacheInterface $cache;

    public function __construct(CacheInterface $cartStorage)
    {
        $this->cache = $cartStorage;
    }

    private function getCartKey(string $userId): string
    {
        return "cart:{$userId}";
    }

    // Get the current cart for a user
    public function getCart(string $userId): array
    {
        return $this->cache->get($this->getCartKey($userId), function (ItemInterface $item) {
            $item->expiresAfter(1800); // 30 min
            return [];
        });
    }

    // Add an item to the cart
    public function addItem(string $userId, string $productId, int $quantity = 1): array
    {
        $cart = $this->getCart($userId);
        $cart[$productId] = ($cart[$productId] ?? 0) + $quantity;

        // Store updated cart in cache
        $this->cache->delete($this->getCartKey($userId));
        $this->cache->get($this->getCartKey($userId), function (ItemInterface $item) use ($cart) {
            $item->expiresAfter(1800);
            return $cart;
        });

        return $cart;
    }

    // Update the quantity of an item
    public function updateQuantity(string $userId, string $productId, int $quantity): array
    {
        $cart = $this->getCart($userId);

        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = $quantity;
        }

        $this->cache->delete($this->getCartKey($userId));
        $this->cache->get($this->getCartKey($userId), function (ItemInterface $item) use ($cart) {
            $item->expiresAfter(1800);
            return $cart;
        });

        return $cart;
    }

    // Remove an item from the cart
    public function removeItem(string $userId, string $productId): array
    {
        $cart = $this->getCart($userId);
        unset($cart[$productId]);

        $this->cache->delete($this->getCartKey($userId));
        $this->cache->get($this->getCartKey($userId), function (ItemInterface $item) use ($cart) {
            $item->expiresAfter(1800);
            return $cart;
        });

        return $cart;
    }

    // Clear the entire cart
    public function clearCart(string $userId): void
    {
        $this->cache->delete($this->getCartKey($userId));
    }
}
