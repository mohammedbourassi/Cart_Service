<?php
namespace App\Controller;

use App\Dto\Cart\AddItemRequestDto;
use App\Dto\Cart\DeleteItemRequestDto;
use App\Dto\Cart\UpdateItemQuantityRequestDto;
use App\Mapper\CartItemMapper;
use App\Model\CartItem;
use App\Service\CartServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/cart', name: 'api_cart_')]
class CartController extends AbstractController
{
    private CartServiceInterface $cartService;
    private CartItemMapper $cartItemMapper;
    private ValidatorInterface $validator;

    public function __construct(CartServiceInterface $cartService, ValidatorInterface $validator, CartItemMapper $cartItemMapper)
    {
        $this->cartService = $cartService;
        $this->validator = $validator;
        $this->cartItemMapper = $cartItemMapper;
    }

    #[Route('', name: 'get', methods: ['GET'])]
    public function getCart(Request $request): JsonResponse
    {
        $cart = $this->cartService->getCart($request);
        $total = 0;
        foreach ($cart as $key => $item) {
            $total += $item['total'] ;
        }
        
        return $this->json(['success' => true, 'cart' => $cart, 'total' => $total]);
    }

    #[Route('/{id}', name: 'get_item', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getItem(Request $request, int $id): JsonResponse
    {
        $cart = $this->cartService->getCart($request);
        $item = $this->cartService->getItem($cart, $id);
        if ($item) {
            
            return $this->json(['success' => true, 'item' => $item]);
        } else {
            return $this->json(['success' => false, 'error' => 'Item not found'], 404);
        }
    }

    #[Route('/add', name: 'add', methods: ['POST'])]
    public function addItem(#[MapRequestPayload()] AddItemRequestDto $dto, Request $request): JsonResponse
    {
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            }
            return $this->json(['success' => false, 'errors' => $errorMessages], 400);
        }

        $cart = $this->cartService->getCart($request);

        $item = $this->cartItemMapper->fromAddDto($dto);

        $cart = $this->cartService->addItem($cart, $item);

        $response = $this->json(['success' => true, 'cart' => $cart]);
        $this->cartService->saveCart($response, $cart);
        
        return $response;
    }

    #[Route('/update', name: 'update', methods: ['PUT'])]
    public function updateItem(#[MapRequestPayload()] UpdateItemQuantityRequestDto $dto, Request $request): JsonResponse
    {
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            }
            return $this->json(['success' => false, 'errors' => $errorMessages], 400);
        }

        $cart = $this->cartService->getCart($request);
        $item = $this->cartItemMapper->fromUpdateDto($dto, $this->cartService->getItem($cart, $dto->productId));
        $cart = $this->cartService->updateItemQuantity($cart, $item->getProductId(), $item->getQuantity());

        $response = $this->json(['success' => true, 'cart' => $cart]);
        $this->cartService->saveCart($response, $cart);

        return $response;
    }

    #[Route('/delete', name: 'delete_item', methods: ['DELETE'])]
    public function deleteItem(#[MapRequestPayload()] DeleteItemRequestDto $dto, Request $request): JsonResponse
    {
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            }
            return $this->json(['success' => false, 'errors' => $errorMessages], 400);
        }

        $cart = $this->cartService->getCart($request);
        $cart = $this->cartService->removeItem($cart, $dto->productId);

        $response = $this->json(['success' => true, 'cart' => $cart]);
        $this->cartService->saveCart($response, $cart);

        return $response;
    }

    #[Route('/clear', name: 'clear', methods: ['POST'])]
    public function clearCart(): JsonResponse
    {
        $cart = $this->cartService->clearCart();

        $response = $this->json(['success' => true, 'cart' => $cart]);
        $this->cartService->saveCart($response, $cart);

        return $response;
    }
}