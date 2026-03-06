<?php
namespace App\Mapper;

use App\Dto\Cart\CartItemResponseDto;
use App\Dto\Cart\AddItemRequestDto;
use App\Dto\Cart\UpdateItemQuantityRequestDto;
use App\Model\CartItem;

class CartItemMapper
{
    /**
     * Map CartItem domain model to CartItemResponseDto
     */
    public static function toCartItemResponseDto(CartItem $item): CartItemResponseDto
    {
        return new CartItemResponseDto(
            (int) $item->getProductId(),
            $item->getName(),
            (float) $item->getPrice(),
            (int) $item->getQuantity()
        );
    }

    /**
     * Map array of CartItem domain models to CartResponseDto
     *
     * @param CartItem[] $items
     */
    
    /**
     * Map AddItemRequestDto to CartItem domain model
     */
    public static function fromAddDto(AddItemRequestDto $dto): CartItem
    {
        return new CartItem(
            (int) $dto->productId,
            $dto->name,
            (int) $dto->quantity, 
            (float) $dto->price           
        );
    }

    /**
     * Map UpdateItemRequestDto to CartItem domain model
     */
    public static function fromUpdateDto(UpdateItemQuantityRequestDto $dto, CartItem $item): CartItem
    {
        $item->setQuantity((int) $dto->quantity);
        return $item;
    }
}