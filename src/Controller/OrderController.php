<?php
// src/Controller/OrderController.php

namespace App\Controller;

use App\Service\OrderService;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/orders')]
class OrderController extends AbstractController
{
    private string $jwtSecret = 'a-string-secret-at-least-256-bits-long'; // for testing

    public function __construct(private OrderService $orderService) {}

   
    #[Route('', name: 'create_order', methods: ['POST'])]
    public function createOrder(Request $request): JsonResponse
    {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('Authorization'));
            $payload = JWT::decode($jwt, new Key($this->jwtSecret, 'HS256'));
            $userId = $payload->userId ?? null;

            $data = json_decode($request->getContent(), true);
            $order = $this->orderService->createOrder($userId, $data['cart']);

            return $this->json([
                'success' => true,
                'orderId' => $order->getId(),
                'status' => $order->getStatus(),
                'totalAmount' => $order->getTotalAmount(),
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}