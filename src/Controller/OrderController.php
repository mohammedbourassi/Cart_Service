<?php
namespace App\Controller;

use App\Enum\ProductType;
use App\Exception\OrderCannotBeCancelledException;
use App\Exception\ProductOutOfStockException;
use App\Mapper\OrderItemMapper;
use App\Mapper\OrderMapper;
use App\Service\JWTServiceInterface;
use App\Service\OrderItemServicesInterface;
use App\Service\OrderServiceInterface;
use App\Service\ProductServiceInterface;
use App\Service\VerifyUserInputServicesInterface;
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
        private ProductServiceInterface $productService,
        private JWTServiceInterface $jwtService,
        private VerifyUserInputServicesInterface $verifieUserInputServices
    ){}

    //=========================================//
    //         Costumer Methods Bellow
    //=========================================//

    #[Route('/{id}', name: 'get_order', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getOrder(Request $request, int $id): Response
    {
        

        $order = $this->orderService->getOrder($id);
        $items = $this->orderItemService->getOrderItems($order);
        $array = [];
        foreach ($items as $item) {
            $productName = $this->productService->getProductName($item->getProduct());
            $array[] = OrderItemMapper::mapOrderItemToArray($item, $productName);
        }
        return $this->json($array);
    }

    #[Route('/add', name: 'add_order', methods: ['POST'])]
    public function addOrder(Request $request): Response
    {
        $userId = (int) $this->getUser()->getUserIdentifier();

        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json(['message' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }
        $cart = $data['cart'];
        $shipping_addresse = $data['shipping_addresse'];
        $this->verifieUserInputServices->verifyShippingAddress($shipping_addresse);
        $order = $this->orderService->createOrder($userId, $shipping_addresse);
        $this->orderService->addOrder($order);
        $total_ammount = 0;
        try {
            foreach ($cart as $item) {
                $this->verifieUserInputServices->verifyItem($item);
                $product = $this->productService->getProduct($item['productId']);
                if($product == null){
                    $this->orderService->deleteOrder($order);
                    return $this->json(['message' => 'Product not found'], Response::HTTP_NOT_FOUND); 
                }
                if($product->getStock() < $item['quantity']){
                    $this->orderService->deleteOrder($order);
                    return $this->json(['message' => 'Product out of stock'], Response::HTTP_CONFLICT);
                    continue;
                }
                if($product->getType() == ProductType::PHYSICAL){
                    $this->productService->decreaseStock($item['productId'], $item['quantity']);
                }
                $this->orderItemService->addOrderItem($order, $product, $item['quantity']);
                $total_ammount += $item['quantity'] * $product->getPrice();

            }
            $order->setTotalAmount($total_ammount);
            $this->orderService->addOrder($order);
        } catch (ProductOutOfStockException $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_CONFLICT);
        }

        return $this->json(['message' => 'Order added successfully']);
    }

    #[Route('/{orderId}/cancel', name: 'cancel_order', methods: ['POST'], requirements: ['orderId' => '\d+'])]
    public function cancelOrder(Request $request, int $orderId): Response
    {
        try {
            $order = $this->orderService->getOrder($orderId);
            $items = $this->orderItemService->getOrderItems($order);
            foreach ($items as $item) {
                $product = $this->productService->getProduct($item->getProduct()->getId());
                if($product->getType() == ProductType::PHYSICAL){
                    $this->productService->increaseStock($item->getProduct(), $item->getQuantity());
                }
                $this->orderItemService->cancelOrderItem($item->getId());  
            }
            
            $this->orderService->cancelOrder($orderId);
        } catch (\Exception $e) {
            if ($e instanceof OrderCannotBeCancelledException) {
                return $this->json(['message' => $e->getMessage()], Response::HTTP_CONFLICT);
            }
            return $this->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->json(['message' => 'Order cancelled successfully']);
    }

    #[Route('/items/{orderItemId}/cancel', name: 'cancel_order_item', methods: ['POST'], requirements: ['orderItemId' => '\d+'])]
    public function cancelOrderItem(int $orderItemId): Response
    {
        try {
            $this->orderItemService->cancelOrderItem($orderItemId);
            return $this->json(['message' => 'Order item cancelled successfully']);
        } catch (\Exception $e) {
            if ($e instanceof OrderCannotBeCancelledException) {
                return $this->json(['message' => $e->getMessage()], Response::HTTP_CONFLICT);
            }
            return $this->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    //=========================================//
    //         Freelancer Methods Bellow
    //=========================================//

    

    
}