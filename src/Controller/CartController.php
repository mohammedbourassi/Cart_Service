<?php
namespace App\Controller;

use App\Service\CartService;
use App\Dto\Cart\AddItemRequestDto;
use App\Dto\Cart\UpdateItemRequestDto;
use App\Dto\Cart\DeleteItemRequestDto;
use App\Dto\Cart\DeleteItemResponseDto;
use App\Mapper\CartMapper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

    private function validateDto(object $dto): ?JsonResponse
    {
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            }
            return $this->json(['errors' => $messages], 400);
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
        $userId = (int) $this->getCurrentUserId();
        $cartItems = $this->cartService->getCart($userId);

        $cartResponse = CartMapper::toCartResponseDto($cartItems);
        return $this->json($cartResponse);
    }

    #[Route('/add', name: 'add', methods: ['POST'])]
    public function addItem(Request $request): JsonResponse
    {
        $dto = $this->serializer->deserialize($request->getContent(), AddItemRequestDto::class, 'json');
        if ($response = $this->validateDto($dto)) return $response;

        $userId = (int) $this->getCurrentUserId();
        $cartItem = CartMapper::fromAddDto($dto, $userId);

        $this->cartService->addItem($cartItem);

        return $this->getCart();
    }

    #[Route('/update', name: 'update', methods: ['PUT'])]
    public function updateItem(Request $request): JsonResponse
    {
        $dto = $this->serializer->deserialize($request->getContent(), UpdateItemRequestDto::class, 'json');
        if ($response = $this->validateDto($dto)) return $response;

        $userId = (int) $this->getCurrentUserId();
        $cartItem = CartMapper::fromUpdateDto($dto, $userId);

        $this->cartService->updateItem($userId, $cartItem);

        return $this->getCart();
    }

    #[Route('/delete', name: 'delete_item', methods: ['DELETE'])]
    public function deleteItem(Request $request): JsonResponse
    {
        $dto = $this->serializer->deserialize($request->getContent(), DeleteItemRequestDto::class, 'json');
        if ($response = $this->validateDto($dto)) return $response;

        $userId = (int) $this->getCurrentUserId();
        $this->cartService->removeItem($userId, $dto->productId);

        $deleteResponse = new DeleteItemResponseDto();
        return $this->json($deleteResponse);
    }

    #[Route('/clear', name: 'clear', methods: ['POST'])]
    public function clearCart(): JsonResponse
    {
        $userId = (int) $this->getCurrentUserId();
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
            '123',          // This becomes user_identifier
            ['ROLE_USER']        // Optional roles
        );

        return $this->json([
            'token' => $jwtManager->create($user)
        ]);
    }
}
