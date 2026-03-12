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

    public function addOrderItem(Orders $order, Products $product, int $quantity): void
    {
        $orderItem = new OrderItems()
        ->setOrder($order)
        ->setProduct($product)
        ->setQuantity($quantity)
        ->setPrice($product->getPrice())
        ->setStatus(ItemStatus :: PENDING)
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

    public function cancelOrderItem(int $orderItemId) : void
    {
        $orderItem = $this->orderItemsRepository->find($orderItemId);
        if(!$orderItem) {
            throw new \Exception('Order item not found');
        }

        if($orderItem->getStatus() == ItemStatus::CANCELLED) {
            throw new \Exception('Order item already cancelled');
        }

        if($orderItem->getStatus() == ItemStatus::PENDING || $orderItem->getStatus() == ItemStatus::PROCESSING) {
            $orderItem->setStatus(ItemStatus::CANCELLED);
            $this->orderItemsRepository->add($orderItem);
        }
        else {
            throw new \Exception('Order item cannot be cancelled');
        }
    }

}