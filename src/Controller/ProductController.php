<?php

namespace App\Controller;


use App\Service\ProductServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/products', name: 'product')]
final class ProductController extends AbstractController
{
    public function __construct(
        private ProductServiceInterface $productService
    ){}

    #[Route('', name: 'get_products', methods: ['GET'])]
    public function getProducts(): Response
    {
        $sellerId = (int) $this->getUser()->getUserIdentifier();
        $products = $this->productService->getProductsOfSeller($sellerId);
        
        return $this->json($products);
    }

    #[Route('/{id}', name: 'get_product', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getProduct(int $id): Response
    {
        $sellerId = (int) $this->getUser()->getUserIdentifier();
        $product = $this->productService->getProductsOfSellerById($id, $sellerId);
        return $this->json($product);
    }

    #[Route('/add', name: 'add_product', methods: ['POST'])]
    public function addProduct(Request $request): Response
    {
        $role = $this->getUser()->getRoles();
        if (!in_array('ROLE_SELLER', $role)) {
            return $this->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }
        $sellerId = (int) $this->getUser()->getUserIdentifier();
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json(['message' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }
        $this->productService->addProduct($data, $sellerId);
        return $this->json(['message' => 'Product added successfully'], Response::HTTP_CREATED);
    }

    #[Route('/{id}/change-stock', name: 'change_stock', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function changeStock(int $id, Request $request): Response
    {
        $role = $this->getUser()->getRoles();
        if (!in_array('ROLE_SELLER', $role)) {
            return $this->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }
        $data = json_decode($request->getContent(), true);
        if (!$data || !isset($data['stock'])) {
            return $this->json(['message' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }
        $stock = (int) $data['stock'];
        $sellerId = (int) $this->getUser()->getUserIdentifier();
        try {
            $this->productService->changeProductStock($id, $stock, $sellerId);
            return $this->json(['message' => 'Stock updated successfully']);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}/remove', name: 'remove', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function removeProduct(int $id): Response
    {
        $role = $this->getUser()->getRoles();
        if (!in_array('ROLE_SELLER', $role)) {
            return $this->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }
        $sellerId = (int) $this->getUser()->getUserIdentifier();
        try {
            $product = $this->productService->getProduct($id, $sellerId);
            if (!$product) {
                return $this->json(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
            }
            $this->productService->removeProduct($id, $sellerId);
            return $this->json(['message' => 'Product removed successfully']);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

}
