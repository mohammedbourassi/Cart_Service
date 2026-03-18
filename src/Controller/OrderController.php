<?php
namespace App\Controller;

use App\Enum\OrderStatus;
use App\Exception\OrderCannotBeCancelledException;
use App\Service\OrderItemServicesInterface;
use App\Service\OrderServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/api/orders', name: 'order')]
class OrderController extends AbstractController
{
    public function __construct(
        private OrderServiceInterface $orderService,
        private OrderItemServicesInterface $orderItemService,
    ){}
    
    //==============================================//
    //            Client Methods Bellow
    //=============================================//
    #[Route('', name: 'get_orders', methods: ['GET'])]
    public function getOrders(): Response
    {
        
        $userId =(int) $this->getUser()->getUserIdentifier();
        $orders = $this->orderService->getOrders($userId);
        return $this->json($orders);
    }

    #[Route('/{id}', name: 'get_order', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getOrder(int $id): Response
    {
        $userId = (int) $this->getUser()->getUserIdentifier();
        $array = $this->orderService->getOrder($id, $userId);
        return $this->json($array);
    }

    #[Route('/add', name: 'add_order', methods: ['POST'])]
    public function saveOrder(Request $request): Response
    {
        $userId = (int) $this->getUser()->getUserIdentifier();
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['message' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }

        try {
            // The service handles everything: order + items + stock + total
            $this->orderService->placeOrder($userId, $data);

            return $this->json(['message' => 'Order added successfully']);
        } catch (\Exception $e) {
            return $this->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{orderId}/cancel', name: 'cancel_order', methods: ['POST'], requirements: ['orderId' => '\d+'])]
    public function cancelOrder( int $orderId): Response
    {
        try {

            $userId = (int) $this->getUser()->getUserIdentifier();
            $this->orderService->cancelOrder($orderId, $userId);
            return $this->json(['message' => 'Order cancelled successfully']);

        } catch (\Exception $e) {
            return $this->json([
                'message' => $e->getMessage()
            ], Response::HTTP_CONFLICT);
        }
    }

    #[Route('/{orderId}/items/{orderItemId}/cancel', name: 'cancel_order_item', methods: ['POST'], requirements: ['orderItemId' => '\d+', 'orderId' => '\d+'])]
    public function cancelOrderItem(int $orderId , int $orderItemId): Response
    {
        try {
            $userId = (int) $this->getUser()->getUserIdentifier();

            $this->orderService->cancelOrderItem($orderId, $orderItemId, $userId);

            return $this->json(['message' => 'Order item cancelled successfully']);
        } catch (\Exception $e) {
            return $this->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    //==============================================//
    //            Freelancer Methods Bellow         
    //==============================================//

    #[Route('/my-items', name: 'get_orders_of_my_items', methods: ['GET'])]
    public function getOrdersOfMyItems()
    {
        $sellerId = (int) $this->getUser()->getUserIdentifier();
        $items = $this->orderItemService->getOrderItemsBySellerId($sellerId);
        
        return $this->json($items);
    }

    
    #[Route('/my-items/user/{id}', name: 'get_orders_of_my_items_by_user', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getOrdersOfMyItemsByUser(int $id)
    {
        $sellerId = (int) $this->getUser()->getUserIdentifier();
        $items = $this->orderItemService->getOrdersOfMyItemsByUser($id, $sellerId);
        
        return $this->json($items);
    }

    #[Route("/my-items/{itemId}/user/{clientId}/accept" , name: "accept_order_item", methods: ["POST"], requirements: ['itemId' => '\d+', 'clientId' => '\d+'])]
    public function acceptOrderItem(int $itemId, int $clientId) : Response
    {
        try {
            $sellerId = (int) $this->getUser()->getUserIdentifier();
            $this->orderItemService->acceptOrderItem($itemId, $clientId, $sellerId);
            $count = $this->orderItemService->countTheNumberOfItemsInOrder($itemId);
            $countProcessing = $this->orderItemService->countTheNumberOfItemsInOrderByStatus($itemId, 'PROCESSING');
            if($count == $countProcessing){
                $this->orderService->acceptOrder($itemId);
            }
            else {
                $this->orderService->modifyOrderStatus($itemId, OrderStatus::PROCESSING);
            }
            return $this->json(['message' => 'Order item accepted successfully']);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    #[Route('/my-items/{itemId}/user/{clientId}/cancel', name: 'cancel_order_item_by_freelancer', methods: ['POST'], requirements: ['itemId' => '\d+'])]
    public function cancelOrderItemByFreelancer(int $itemId, int $clientId) : Response
    {        
        try {
            $userId = (int) $this->getUser()->getUserIdentifier();
            $this->orderItemService->cancelOrderItemBySeller($itemId, $clientId, $userId);
            $count = $this->orderItemService->countTheNumberOfItemsInOrder($itemId);
            $countCancelled = $this->orderItemService->countTheNumberOfItemsInOrderByStatus($itemId, 'CANCELLED');
            if($count == $countCancelled){
                $this->orderService->cancelOrder($itemId, $userId);
            }
            return $this->json(['message' => 'Order item cancelled successfully']);
        } catch (\Exception $e) {
            if ($e instanceof OrderCannotBeCancelledException) {
                return $this->json(['message' => $e->getMessage()], Response::HTTP_CONFLICT);
            }
            return $this->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    //==============================================//
    //             Shipper Methods Bellow         
    //==============================================//

    #[Route('/{orderId}/items/{orderItemId}/ship', name: 'ship_item', methods: ['POST'], requirements: ['orderId' => '\d+', 'orderItemId' => '\d+'])]
    public function shipOrder( int $orderId, int $orderItemId): Response
    {
        try {
            $userId = (int) $this->getUser()->getUserIdentifier();
            $role = $this->getUser()->getRoles();
            if(!in_array('ROLE_SHIPPER', $role)) {
                throw new \Exception('You are not authorized to perform this action');
            }
            $this->orderItemService->shipOrderItem($orderItemId, $userId);
            return $this->json(['message' => 'Order item shipped successfully']);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{orderId}/items/{orderItemId}/deliver', name: 'deliver_item', methods: ['POST'], requirements: ['orderId' => '\d+', 'orderItemId' => '\d+'])]
    public function deliverOrder( int $orderId, int $orderItemId): Response
    {
        try {
            $userId = (int) $this->getUser()->getUserIdentifier();
            $role = $this->getUser()->getRoles();
            if(!in_array('ROLE_SHIPPER', $role)) {
                throw new \Exception('You are not authorized to perform this action');
            }
            $this->orderItemService->deliverOrderItem($orderItemId, $userId);
            return $this->json(['message' => 'Order item delivered successfully']);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}