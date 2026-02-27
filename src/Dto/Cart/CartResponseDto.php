<?php
namespace App\Dto\Cart;

class CartResponseDto
{
    /** @var CartItemResponseDto[] */
    public array $items;
    public float $total;

    public function __construct(array $items)
    {
        $this->items = $items;
        $this->total = array_sum(array_map(fn($item) => $item->subtotal, $items));
    }
}
