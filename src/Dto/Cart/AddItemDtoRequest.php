<?php
namespace App\Dto\Cart;

use Symfony\Component\Validator\Constraints as Assert;

class AddItemRequestDto
{
    #[Assert\NotBlank]
    public int $productId;

    #[Assert\NotBlank]
    public string $name;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public float $price;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $quantity;
}
