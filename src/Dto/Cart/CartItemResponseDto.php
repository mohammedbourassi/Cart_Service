<?php
namespace App\Dto\Cart;

class CartItemResponseDto
{
    public int $productId;
    public string $name;
    public float $price;
    public int $quantity;
    public float $subtotal;

    public function __construct(int $productId, string $name, float $price, int $quantity)
    {
        $this->productId = $productId;
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->subtotal = $price * $quantity;
    }
    
}
