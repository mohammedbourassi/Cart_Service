<?php

namespace App\Service;

use App\Entity\Orders;
use App\Entity\Products;
use App\Enum\ItemStatus;

interface OrderItemServicesInterface
{
    public function getOrderItems(Orders $order);
    public function addOrderItem(Orders $order, Products $product, int $quantity, ?ItemStatus $status): void;
    public function modifyOrderItemQuantity(int $orderItemId, int $quantity);
    public function cancelOrderItem(int $orderItemId, int $userId) : void;
    public function cancelOrderItemBySeller(int $orderItemId, int $userId, int $sellerId) : void;
    public function getOrderItemsBySellerId(int $sellerId) : array;
    public function getOrdersOfMyItemsByUser(int $userId, int $sellerId) : array;
    public function acceptOrderItem(int $orderItemId, int $userId, int $sellerId) : void;
    public function countTheNumberOfItemsInOrder(int $orderId) : int;
    public function countTheNumberOfItemsInOrderByStatus(int $orderId, string $status) : int;
    public function shipOrderItem(int $orderItemId, int $userId) : void;
    public function deliverOrderItem(int $orderItemId, int $userId) : void;
}