<?php
namespace App\Dto\Cart;

use Symfony\Component\Validator\Constraints as Assert;

class RemoveItemDto
{
    #[Assert\NotBlank]
    public string $productId;
}
