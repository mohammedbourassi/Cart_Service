<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Enum\OrderStatus;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;

class OrderService{

    public function __construct(private OrderRepository $orderRepository, private ProductRepository $productRepository){}
    
    public function createOrder(int $userId, array $cart): Order
    {
        $order = new Order();
        $order->setUserId($userId);
        $order->setStatus(OrderStatus::PENDING);
        $order->setCreatedAt(new \DateTimeImmutable());
        $order->setUpdatedAt(new \DateTimeImmutable());

        $totalAmount = 0;

       foreach ($cart as $item) {
            $product = $this->productRepository->find($item['productId']);
            if (!$product) {
                throw new \Exception("Product ID {$item['productId']} not found");
            }

            $quantity = $item['quantity'];
            $itemTotal = $product->getPrice() * $quantity;

            $orderItem = new OrderItem();
            $orderItem->setOrderId($order)          // pass Order entity
                    ->setProductId($product)      // pass Product entity
                    ->setProductName($product->getName())
                    ->setPrice($product->getPrice())
                    ->setQuantity($quantity)
                    ->setTotal($itemTotal);

            $order->addOrderItem($orderItem);

            $totalAmount += $itemTotal;
        }

        // Set total and persist order (items cascade)
        $order->setTotalAmount($totalAmount);
        $entityManager = $this->orderRepository->getEntityManager();
        $entityManager->persist($order);
        $entityManager->flush();

        
        return $order;
    }
    public function getOrders(int $userId) : array
    {
        $orders = $this->orderRepository->findBy(['userId' => $userId], ['createdAt' => 'DESC']);

        $result = [];

        foreach ($orders as $order) {
            $items = [];
            foreach ($order->getOrderItems() as $item) {
                $items[] = [
                    'productId' => $item->getProductId(),
                    'productName' => $item->getProductName(),
                    'price' => $item->getPrice(),
                    'quantity' => $item->getQuantity(),
                    'total' => $item->getTotal(),
                ];
            }

            $result[] = [
                'orderId' => $order->getId(),
                'totalAmount' => $order->getTotalAmount(),
                'status' => $order->getStatus(),
                'createdAt' => $order->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $order->getUpdatedAt()->format('Y-m-d H:i:s'),
                'items' => $items,
            ];
        }

        return $result;
    }

    public function getOrderItems(int $orderId) : array
    {
        $order = $this->orderRepository->find($orderId);

        if (!$order) {
            throw new \Exception("Order ID {$orderId} not found");
        }

        $items = [];
        foreach ($order->getOrderItems() as $item) {
            $items[] = [
                'orderItemId' => $item->getId(),
                'productId' => $item->getProductId(),
                'productName' => $item->getProductName(),
                'price' => $item->getPrice(),
                'quantity' => $item->getQuantity(),
                'total' => $item->getTotal(),
            ];
        }

        return [
            'orderId' => $order->getId(),
            'userId' => $order->getUserId(),
            'status' => $order->getStatus(),
            'totalAmount' => $order->getTotalAmount(),
            'createdAt' => $order->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $order->getUpdatedAt()->format('Y-m-d H:i:s'),
            'items' => $items,
        ];
    }

    public function cancelOrder(int $orderId): Order
    {
        $order = $this->orderRepository->find($orderId);

        if (!$order) {
            throw new \Exception("Order ID {$orderId} not found");
        }

        // Only pending orders can be cancelled
        if ($order->getStatus() !== 'pending') {
            throw new \Exception("Only pending orders can be cancelled");
        }

        $order->setStatus('cancelled');
        $order->setUpdatedAt(new \DateTime());

        $this->orderRepository->save($order);

        return $order;
    }

    public function confirmOrder(int $orderId)
    {
        $order = $this->orderRepository->find($orderId);

        if (!$order) {
            throw new \Exception("Order ID {$orderId} not found");
        }

        // Only pending orders can be confirmed
        if ($order->getStatus() !== 'pending') {
            throw new \Exception("Only pending orders can be confirmed");
        }

        $order->setStatus(OrderStatus::CONFIRMED);
        $order->setUpdatedAt(new \DateTime());

        $this->orderRepository->save($order);

        return $order;
    }
    
}