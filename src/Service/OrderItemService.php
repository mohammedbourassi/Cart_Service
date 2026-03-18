<?php
namespace App\Service;

use App\Entity\OrderItems;
use App\Entity\Orders;
use App\Entity\Products;
use App\Enum\ItemStatus;
use App\Repository\OrderItemsRepository;
use App\Service\OrderItemServicesInterface;

class OrderItemService implements OrderItemServicesInterface
{
    public function __construct(
        private OrderItemsRepository $orderItemsRepository,
    ){}

    public function addOrderItem(Orders $order, Products $product, int $quantity, ?ItemStatus $status): void
    {
        $orderItem = new OrderItems()
        ->setOrder($order)
        ->setProduct($product)
        ->setQuantity($quantity)
        ->setPrice($product->getPrice())
        ->setStatus($status ?? ItemStatus :: PENDING)
        ->setSellerId($product->getSellerId())
        ->setCreatedAt(new \DateTimeImmutable());
        $this->orderItemsRepository->add($orderItem, true);
    }

    public function getOrderItems(Orders $order)
    {
        return $this->orderItemsRepository->findBy(['order_' => $order]);
    }

    public function modifyOrderItemQuantity(int $orderItemId, int $quantity)
    {
        $orderItem = $this->orderItemsRepository->find($orderItemId);
        $orderItem->setQuantity($quantity);
        $this->orderItemsRepository->add($orderItem);
    }

    public function cancelOrderItem(int $orderItemId, int $userId): void
    {
        $orderItem = $this->orderItemsRepository->find($orderItemId);

        if (!$orderItem) {
            throw new \Exception('Order item not found');
        }

        // Ownership check: either the seller or the buyer can cancel
        $buyerId = $orderItem->getOrder()->getUserId();
        $sellerId = $orderItem->getSellerId();
        if ($userId !== $buyerId && $userId !== $sellerId) {
            throw new \Exception('This order item does not belong to you.');
        }

        if ($orderItem->getStatus() === ItemStatus::CANCELLED) {
            throw new \Exception('Order item already cancelled');
        }

        if (in_array($orderItem->getStatus(), [ItemStatus::PENDING, ItemStatus::PROCESSING])) {
            $orderItem->setStatus(ItemStatus::CANCELLED);
            $this->orderItemsRepository->add($orderItem, true);
        } else {
            throw new \Exception('Order item cannot be cancelled');
        }
    }

    public function getOrderItemsBySellerId(int $sellerId) : array
    {
        return $this->orderItemsRepository->getSellerOrderItemsBySellerId($sellerId);
    }

    public function getOrdersOfMyItemsByUser(int $userId, int $sellerId) : array
    {
        return $this->orderItemsRepository->getOrdersOfMyItemsByUser($userId, $sellerId);
    }

    public function acceptOrderItem(int $orderItemId, int $userId, int $sellerId) : void
    {
        $orderItem = $this->orderItemsRepository->getOneItemOfUser($orderItemId, $userId, $sellerId);
        if(!$orderItem) {
            throw new \Exception('Order item not found');
        }

        if($orderItem->getStatus() == ItemStatus::PROCESSING) {
            throw new \Exception('Order item already accepted');
        }

        if($orderItem->getStatus() == ItemStatus::PENDING) {
            $orderItem->setStatus(ItemStatus::PROCESSING);
            $this->orderItemsRepository->add($orderItem);
        }
        else {
            throw new \Exception('Order item cannot be accepted');
        }
    }

    public function cancelOrderItemBySeller(int $orderItemId, int $userId, int $sellerId) : void
    {
        $orderItem = $this->orderItemsRepository->getOneItemOfUser($orderItemId, $userId, $sellerId);
        if(!$orderItem) {
            throw new \Exception('Order item not found');
        }

        if($orderItem->getStatus() == ItemStatus::PROCESSING) {
            throw new \Exception('Order item already accepted');
        }

        if($orderItem->getStatus() == ItemStatus::PENDING || $orderItem->getStatus() == ItemStatus::PROCESSING) {
            $orderItem->setStatus(ItemStatus::CANCELLED);
            $this->orderItemsRepository->add($orderItem);
        }
        else {
            throw new \Exception('Order item cannot be accepted');
        }
    }

    public function countTheNumberOfItemsInOrder(int $orderId) : int
    {
        return $this->orderItemsRepository->countTheNumberOfItemsInOrder($orderId);
    }

    public function countTheNumberOfItemsInOrderByStatus(int $orderId, string $status) : int
    {
        return $this->orderItemsRepository->countTheNumberOfItemsInOrderByStatus($orderId, $status);
    }

    public function shipOrderItem(int $orderItemId, int $userId) : void
    {
        $orderItem = $this->orderItemsRepository->find($orderItemId);
        if(!$orderItem) {
            throw new \Exception('Order item not found');
        }

        if($orderItem->getStatus() == ItemStatus::PENDING || $orderItem->getStatus() == ItemStatus::PROCESSING) {
            $orderItem->setStatus(ItemStatus::SHIPPED);
            $this->orderItemsRepository->add($orderItem);
        }
        else {
            throw new \Exception('Order item cannot be shipped');
        }
    }

    public function deliverOrderItem(int $orderItemId, int $userId) : void
    {
        $orderItem = $this->orderItemsRepository->find($orderItemId);
        if(!$orderItem) {
            throw new \Exception('Order item not found');
        }

        if($orderItem->getStatus() == ItemStatus::SHIPPED) {
            $orderItem->setStatus(ItemStatus::DELIVERED);
            $this->orderItemsRepository->add($orderItem);
        }
        else {
            throw new \Exception('Order item cannot be delivered');
        }
    }
}