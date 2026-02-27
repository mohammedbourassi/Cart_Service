<?php
namespace App\Dto\Cart;

use Symfony\Component\Validator\Constraints as Assert;

class DeleteItemRequestDto
{
    #[Assert\NotBlank]
    public int $productId;
}
