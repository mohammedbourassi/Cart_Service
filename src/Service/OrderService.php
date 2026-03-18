<?php

namespace App\Service;

use App\Entity\Orders;
use App\Enum\ItemStatus;
use App\Enum\OrderStatus;
use App\Enum\ProductType;
use App\Exception\OrderCannotBeCancelledException;
use App\Repository\OrdersRepository;
use App\Service\OrderServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

class OrderService implements OrderServiceInterface
{
    public function __construct(
         private OrdersRepository $ordersRepository,
         private OrderItemServicesInterface $orderItemService,
         private ProductServiceInterface $productService,
         private VerifyUserInputServicesInterface $verifieUserInputServices,
         private EntityManagerInterface $entityManager
     ){}

     public function createOrder(int $userId, string $shipping_address): Orders
     {
        $order = new Orders()
        ->setUserId($userId)
        ->setTotalAmount(0)
        ->setCreatedAt(new \DateTimeImmutable())
        ->setStatus(OrderStatus::PENDING)
        ->setShippingAddress($shipping_address);
        return $order;
     }

     public function placeOrder(int $userId, array $data): void
      {
         // Start transaction
         $this->entityManager->beginTransaction();

         try {
            if (!isset($data['cart']) || !isset($data['shipping_addresse'])) {
                  throw new \InvalidArgumentException('Cart or shipping address missing');
            }

            $cart = $data['cart'];
            $shippingAddress = $data['shipping_addresse'];
            $this->verifieUserInputServices->verifyShippingAddress($shippingAddress);

            $order = $this->createOrder($userId, $shippingAddress);

            $this->saveOrder($order);

            $totalAmount = 0;

            foreach ($cart as $item) {
                  $this->verifieUserInputServices->verifyItem($item);

                  $product = $this->productService->getProduct($item['productId']);

                  if ($product === null) {
                     throw new \Exception("Product not found: ID {$item['productId']}");
                  }

                  if ($product->getStock() < $item['quantity']) {
                     throw new \Exception("Product out of stock: {$product->getName()}");
                  }

                  if ($product->getType() === ProductType::PHYSICAL) {
                     $this->productService->decreaseStock($product->getId(), $item['quantity']);
                     $this->orderItemService->addOrderItem($order, $product, $item['quantity'], null);
                  }
                  if($product->getType() === ProductType::DIGITAL) {
                     $this->orderItemService->addOrderItem($order, $product, $item['quantity'], ItemStatus::DELIVERED);
                     $digital = true;
                  }
                  $totalAmount += $item['quantity'] * $product->getPrice();
            }

            if (isset($digital) && $digital) {
               $order->setStatus(OrderStatus::PARTIAL);
            }

            $order->setTotalAmount($totalAmount);
            $this->saveOrder($order);

            $this->entityManager->flush();
            $this->entityManager->commit();
         } catch (\Exception $e) {
            
            $this->entityManager->rollback();
            throw $e;
         }
      }
     public function saveOrder(Orders $order)
     {
        $this->ordersRepository->save($order);
     }

     public function getOrders(int $userId) : array
     {
        return $this->ordersRepository->getOrders($userId);
     }

     public function getOrder(int $orderId, int $userId) : array
     {
        return $this->ordersRepository->findOrderFullByUser($orderId, $userId);
     }

     public function findOrderById(int $orderId) : ?Orders
     {
        $order = $this->ordersRepository->find($orderId);
        if (!$order) {
            throw new \Exception('Order not found');
        }
        return $order;
     }

     public function modifyOrderStatus(int $orderId, OrderStatus $status)
     {
        $order = $this->findOrderById($orderId);
        $order->setStatus($status);
        $this->ordersRepository->save($order);
     }

     public function cancelOrder(int $orderId, int $userId)
     {
        $order = $this->findOrderById($orderId);
        if (!$order || $order->getUserId() !== $userId) {
            throw new \Exception('This order does not belong to you.');
         }
        if($order->getStatus() == OrderStatus::CANCELLED){
            throw new OrderCannotBeCancelledException('Order is already cancelled');
         }
        if($order->getStatus() == OrderStatus::PENDING){
            $order->setStatus(OrderStatus::CANCELLED);
            $this->ordersRepository->save($order);
        }
        else{
            throw new OrderCannotBeCancelledException('Order cannot be cancelled');
        }
     }

     public function acceptOrder(int $orderId)
     {
        $order = $this->findOrderById($orderId);
        if($order->getStatus() == OrderStatus::PENDING){
            $order->setStatus(OrderStatus::PROCESSING);
            $this->ordersRepository->save($order);
        }
        else{
            throw new \Exception('Order cannot be accepted');
        }
     }

     public function deleteOrder(Orders $order)
     {
        $this->ordersRepository->remove($order, true);
     }
   
     public function updateStatusBasedOnItems(int $orderId): void
    {
        $order = $this->findOrderById($orderId);

        $totalItems = $this->orderItemService->countTheNumberOfItemsInOrder($orderId);
        $cancelledItems = $this->orderItemService->countTheNumberOfItemsInOrderByStatus($orderId, 'CANCELLED');

        if ($totalItems === $cancelledItems) {
            $order->setStatus(OrderStatus::CANCELLED);
        } else {
            $order->setStatus(OrderStatus::PROCESSING);
        }

        $this->saveOrder($order);
    }

    public function cancelOrderItem(int $orderId, int $orderItemId, int $userId): void
    {
        $this->orderItemService->cancelOrderItem($orderItemId, $userId);
        $this->updateStatusBasedOnItems($orderId);
    }
    
    

}