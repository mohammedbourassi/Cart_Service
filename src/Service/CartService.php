<?php

namespace App\Service;

use App\Dto\Cart\AddItemRequestDto;
use App\Model\CartItem;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CartService implements CartServiceInterface
{
    public function getCart(Request $request)
    {
        $cartCookie = $request->cookies->get('cart');
        if ($cartCookie) {
            return json_decode($cartCookie, true);
        }
        return [];
    }

    public function getItem(array $cart, int $productId): ?CartItem
    {
        if (isset($cart[$productId])) {
        $item = $cart[$productId];
        return new CartItem(
            $item['productId'],
            $item['name'],
            $item['quantity'],
            $item['price']
        );
    }
    return null;
    }

    public function addItem(array $cart, CartItem $item): array
    {
    
        $existingItem = $this->getItem($cart, $item->getProductId());

        if ($existingItem !== null) {
            $cart[$item->getProductId()]['quantity'] += $item->getQuantity();
        } else {
            $cart[$item->getProductId()] = $item->toArray();
        }
        return $cart;
    }

    public function removeItem(array $cart, int $productId): array
    {
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
        }
        return $cart;
    }

    public function clearCart(): array
    {
        return [];
    }

    public function updateItemQuantity(array $cart, int $productId, int $quantity): array
    {
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $quantity;
        }
        return $cart;
    }

    public function saveCart(Response $response, array $cart)
    {
        $cookie = Cookie::create(
            'cart',
            json_encode($cart),   // JSON string of cart
            new \DateTime('+30 days'), // expiration
            '/',                  // path
            null,                 // domain
            false,                // secure
            true,                 // httpOnly
            false,                // raw
            'lax'                 // SameSite
        );

        $response->headers->setCookie($cookie);
    }
}