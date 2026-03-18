<?php

namespace App\Service;

use App\Entity\Orders;
use App\Enum\OrderStatus;

interface OrderServiceInterface
{
    public function createOrder(int $userId, string $shipping_address);
    public function saveOrder(Orders $order);
    public function getOrders(int $orderId);
    public function getOrder(int $orderId, int $userId);
    public function findOrderById(int $orderId);
    public function placeOrder(int $userId, array $data): void;
    public function modifyOrderStatus(int $orderId, OrderStatus $status);
    public function cancelOrder(int $orderId, int $userId);
    public function acceptOrder(int $orderId);
    public function deleteOrder(Orders $order);
    public function updateStatusBasedOnItems(int $orderId);
    public function cancelOrderItem(int $orderId, int $orderItemId, int $userId);
}