<?php
namespace App\Controller;

use App\Service\CartService;
use App\Dto\Cart\AddItemDto;
use App\Dto\Cart\UpdateItemDto;
use App\Dto\Cart\RemoveItemDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/cart', name: 'api_cart_')]
class CartController extends AbstractController
{
    private CartService $cartService;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(
        CartService $cartService,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->cartService = $cartService;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    private function handleValidation($dto): ?JsonResponse
    {
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], 400);
        }
        return null;
    }

     private function getCurrentUserId(): string
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('User not authenticated');
        }

        return $user->getUserIdentifier();
    }

    #[Route('', name: 'get', methods: ['GET'])]
    public function getCart(): JsonResponse
    {
        $userId = $this->getCurrentUserId();
        return $this->json($this->cartService->getCart($userId));
    }

    #[Route('/add', name: 'add', methods: ['POST'])]
    public function addItem(Request $request): JsonResponse
    {
        $content = $request->getContent();
        if (empty($content)) {
            return $this->json(['error' => 'Empty body'], 400);
        }

        // Deserialize into DTO
        $dto = $this->serializer->deserialize($content, AddItemDto::class, 'json');

        // Validate DTO
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            }
            return $this->json(['errors' => $messages], 400);
        }

        // Add item using CartService
        $userId = $this->getCurrentUserId();
        $cart = $this->cartService->addItem($userId, $dto->productId, $dto->quantity);

        return $this->json([
            'message' => "Item added to cart",
            'cart' => $cart
        ]);
    }

    #[Route('/update', name: 'update', methods: ['PUT'])]
    public function updateItem(Request $request): JsonResponse
    {
        $dto = $this->serializer->deserialize($request->getContent(), UpdateItemDto::class, 'json');

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            }
            return $this->json(['errors' => $messages], 400);
        }

        $userId = $this->getCurrentUserId();
        $cart = $this->cartService->updateQuantity($userId, $dto->productId, $dto->quantity);

        return $this->json([
            'message' => 'Item updated in cart',
            'cart' => $cart
        ]);
}

    #[Route('/{productId}', name: 'delete_item', methods: ['DELETE'])]
    public function deleteItem(int $productId): JsonResponse
    {
        $this->cartService->removeItem($this->getCurrentUserId(), $productId);

        return $this->json([
            'message' => 'Item removed from cart',
            'productId' => $productId
        ]);
    }

    #[Route('/clear', name: 'clear', methods: ['POST'])]
    public function clearCart(): JsonResponse
    {
        $userId = $this->getCurrentUserId();
        $this->cartService->clearCart($userId);
        return $this->json(['message' => 'Cart cleared']);
    }

    #[Route('/test', name: 'api_cart_test', methods: ['GET'])]
    public function testJwt(Request $request): JsonResponse
    {
        $authHeader = $request->headers->get('Authorization');
        return $this->json([
            'Authorization header' => $authHeader,
            'user' => $this->getUser() ? $this->getUser()->getUserIdentifier() : null
        ]);
    }
    #[Route('/dev-token', name: 'api_cart_devtoken', methods: ['GET'])]
    public function devToken(\Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface $jwtManager)
    {
        $user = new \Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUser(
            'testuser',          // This becomes user_identifier
            ['ROLE_USER']        // Optional roles
        );

        return $this->json([
            'token' => $jwtManager->create($user)
        ]);
    }
}
