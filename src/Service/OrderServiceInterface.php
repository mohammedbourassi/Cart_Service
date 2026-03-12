<?php

namespace App\Service;

use App\Entity\Orders;
use App\Enum\OrderStatus;

interface OrderServiceInterface
{
    public function createOrder(int $userId, string $shipping_address);
    public function addOrder(Orders $order);
    public function getOrders(int $userId);
    public function getOrder(int $orderId);
    public function modifyOrderStatus(int $orderId, OrderStatus $status);
    public function cancelOrder(int $orderId);
    public function deleteOrder(Orders $order);
}