<?php
namespace App\Dto\Cart;

use Symfony\Component\Validator\Constraints as Assert;

class AddItemDto
{
    #[Assert\NotBlank]
    public ?int $productId;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $quantity;
}
