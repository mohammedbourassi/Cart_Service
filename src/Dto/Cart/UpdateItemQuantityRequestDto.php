<?php
namespace App\Dto\Cart;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateItemQuantityRequestDto
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $productId;
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $quantity;
}
