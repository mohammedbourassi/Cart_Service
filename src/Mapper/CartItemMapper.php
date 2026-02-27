<?php
namespace App\Mapper;

use App\Dto\Cart\CartItemResponseDto;
use App\Dto\Cart\CartResponseDto;
use App\Dto\Cart\AddItemRequestDto;
use App\Dto\Cart\UpdateItemRequestDto;
use App\Model\CartItem;

class CartMapper
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
    public static function toCartResponseDto(array $items): CartResponseDto
    {
        $itemsDto = array_map(
            fn(CartItem $item) => self::toCartItemResponseDto($item),
            $items
        );

        return new CartResponseDto($itemsDto);
    }

    /**
     * Map AddItemRequestDto to CartItem domain model
     */
    public static function fromAddDto(AddItemRequestDto $dto, string $userId): CartItem
    {
        return new CartItem(
            (int) $dto->productId,
            $dto->name,
            (int) $dto->quantity, 
            (float) $dto->price,
            $userId                // string
        );
    }

    /**
     * Map UpdateItemRequestDto to CartItem domain model
     */
    public static function fromUpdateDto(UpdateItemRequestDto $dto, string $userId): CartItem
    {
        return new CartItem(
            (int) $dto->productId,
            $dto->name,
            (int) $dto->quantity, 
            (float) $dto->price,
            $userId            
        );
    }
}