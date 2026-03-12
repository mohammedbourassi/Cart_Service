<?php

namespace App\Service;

use App\Entity\Orders;
use App\Entity\Products;

interface OrderItemServicesInterface
{
    public function getOrderItems(Orders $order);
    public function addOrderItem(Orders $order, Products $product, int $quantity): void;
    public function modifyOrderItemQuantity(int $orderItemId, int $quantity);
    public function cancelOrderItem(int $orderItemId) : void;
}