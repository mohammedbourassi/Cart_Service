<?php
namespace App\Dto\Cart;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateItemDto
{
    #[Assert\NotBlank]
    public ?int $productId = null;

    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]  // 0 pour supprimer l'article si quantité 0
    public ?int $quantity = null;
}
