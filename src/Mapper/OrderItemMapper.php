<?php

namespace App\Mapper;

use App\Entity\OrderItems;

class OrderItemMapper
{
    public static function mapOrderItemToArray(OrderItems $item, string $productName): array
    {
        return [
            'product_name' => $productName,
            'status' => $item->getStatus(),
            'quantity' => $item->getQuantity(),
            'price' => $item->getPrice(),
            'status' => $item->getStatus(),
            'seller_id' => $item->getSellerId(),
            'createdAt' => $item->getCreatedAt(),
        ];
    }

    public static function mapSellerOrderItemToArray(OrderItems $item, string $productName, int $userId): array
    {
        return [
            'product_name' => $productName,
            'status' => $item->getStatus(),
            'quantity' => $item->getQuantity(),
            'price' => $item->getPrice(),
            'status' => $item->getStatus(),
            'client_id' => $userId,
            'createdAt' => $item->getCreatedAt(),
        ];
    }
    
}