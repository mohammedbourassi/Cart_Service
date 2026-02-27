<?php
namespace App\Dto\Cart;

class DeleteItemResponseDto
{
    public string $message;

    public function __construct(string $message = 'Item deleted successfully')
    {
        $this->message = $message;
    }
}
