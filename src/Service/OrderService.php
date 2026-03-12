<?php

namespace App\Service;

use App\Entity\Orders;
use App\Enum\OrderStatus;
use App\Exception\OrderCannotBeCancelledException;
use App\Repository\OrdersRepository;
use App\Service\OrderServiceInterface;

class OrderService implements OrderServiceInterface
{
    public function __construct(
        private OrdersRepository $ordersRepository,
        
     ){}

     public function createOrder(int $userId, string $shipping_address): Orders
     {
        $order = new Orders();
        $order
        ->setUserId($userId)
        ->setTotalAmount(0)
        ->setCreatedAt(new \DateTimeImmutable())
        ->setStatus(OrderStatus::PENDING)
        ->setShippingAddress($shipping_address);
        $this->ordersRepository->add($order);
        return $order;
     }
     public function addOrder(Orders $order)
     {
        $this->ordersRepository->add($order);
     }

     public function getOrders(int $userId) : array
     {
        return $this->ordersRepository->findBy(['user_id' => $userId]);
     }

     public function getOrder(int $orderId) : Orders
     {
        return $this->ordersRepository->find($orderId);
     }

     public function modifyOrderStatus(int $orderId, OrderStatus $status)
     {
        $order = $this->ordersRepository->find($orderId);
        $order->setStatus($status);
        $this->ordersRepository->add($order);
     }

     public function cancelOrder(int $orderId)
     {
        $order = $this->ordersRepository->find($orderId);
        if($order->getStatus() == OrderStatus::PENDING){
            $order->setStatus(OrderStatus::CANCELLED);
            $this->ordersRepository->add($order);
        }
        else{
            throw new OrderCannotBeCancelledException('Order cannot be cancelled');
        }
     }

     public function deleteOrder(Orders $order)
     {
        $this->ordersRepository->remove($order, true);
     }

     
}