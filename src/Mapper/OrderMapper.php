<?php

namespace App\Mapper;

use App\Entity\Orders;

class OrderMapper
{
    public static function mapOrderToArray(Orders $order): array
    {
        return [
            'userId' => $order->getUserId(),
            'shippingAddress' => $order->getShippingAddress(),
            'status' => $order->getStatus(),
            'totalAmount' => $order->getTotalAmount(),
            'createdAt' => $order->getCreatedAt(),
        ];
    }

    public static function mapOrdersToArray(array $order): array
    {
        foreach ($order as $order) {
            $orders[] = self::mapOrderToArray($order);
        }
        return $orders;
    }
}