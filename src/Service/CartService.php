<?php

namespace App\Service;


use App\Repository\CartRepositoryInterface;
use App\Model\CartItem;

class CartService implements CartServiceInterface
{
    private CartRepositoryInterface $cartRepository;

    public function __construct(CartRepositoryInterface $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    // Ajoute ou met à jour un produit dans le panier
    public function addItem(CartItem $item): void
    {
        if ($item->getQuantity() <= 0) {
            throw new \InvalidArgumentException('Quantity must be greater than zero.');
        }

        $userId = $item->getUserId();
        $productId = $item->getProductId();

        $items = $this->cartRepository->getCart($userId);

        if (isset($items[$productId])) {
            $existingItem = $items[$productId];
            $existingItem->setQuantity(
                $existingItem->getQuantity() + $item->getQuantity()
            );
            $this->cartRepository->updateCart($userId, $existingItem);
        } else {
            $this->cartRepository->addToCart($userId, $item);
        }
    }

    // Supprime un produit du panier
    public function removeItem(int $userId, int $productId): void
    {
        $existing = $this->cartRepository->getItem($userId, $productId);

        if ($existing === null) {
            throw new \RuntimeException('Item not found in cart.');
        }
        $this->cartRepository->removeFromCart($userId, $productId);
    }

    // Vide tout le panier
    public function clearCart(int $userId): void
    {
        $this->cartRepository->clearCart($userId);
    }

    // Retourne tous les items du panier
    public function getCart(int $userId): array
    {
        return $this->cartRepository->getCart($userId);
    }

    // Met à jour un item spécifique du panier
    public function updateItem(int $userId, CartItem $item): void
    {
        $existing = $this->cartRepository->getItem($userId, $item->getProductId());

        if ($existing === null) {
            throw new \RuntimeException('Item not found in cart.');
        }

        $this->cartRepository->updateCart($userId, $item);
    }
}
