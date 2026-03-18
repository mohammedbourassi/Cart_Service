<?php

namespace App\Service;

use App\Entity\Products;
use App\Mapper\ProductMapper;
use App\Repository\ProductsRepository;
use App\Service\ProductServiceInterface;


class ProductService implements ProductServiceInterface
{
    public function __construct(
        private ProductsRepository $productRepository,
        private VerifyUserInputServicesInterface $verifyUserInputServices
    ){}

    public function getProduct(int $productId)
    {
        $product = $this->productRepository->find($productId);
        return $product;
    }
   public function getProductBySeller(int $productId, int $sellerId)
   {
        
         $product = $this->productRepository->getOneProduct($productId, $sellerId);
         return $product;
   }
   public function getProductsOfSellerById(int $productId, int $sellerId)
   {
        return $this->productRepository->getProductOfSellerById($productId, $sellerId);
   }

   public function decreaseStock(int $productId, int $quantity):void
   {
       $product = $this->productRepository->find($productId);
       $product->setStock($product->getStock() - $quantity);
       $this->productRepository->add($product, true);
   }

   public function increaseStock(int $productId, int $quantity):void
   {
       $product = $this->productRepository->find($productId);
       $product->setStock($product->getStock() + $quantity);
       $this->productRepository->add($product, true);
   }

   public function saveProduct(Products $product): void
   {
       $this->productRepository->save($product, true);
   }

   public function getProductsOfSeller(int $sellerId) 
   {
        return $this->productRepository->getProductsOfSeller($sellerId);    
   }

   public function addProduct(array $data, int $sellerId): void
   {
        try {
            $this->verifyUserInputServices->verifyProduct($data);
            $product = ProductMapper::toEntity($data, $sellerId);
            $this->productRepository->save($product, true);
        } catch (\Exception $e) {
            throw new \Exception('Failed to add product: ' . $e->getMessage());
        }
   }

   public function changeProductStock(int $productId, int $stock, int $sellerId): void
   {
        $this->verifyUserInputServices->verifyStock($stock);
        $product = $this->productRepository->getProduct($productId, $sellerId);
        if (!$product) {
            throw new \Exception('Product not found');
        }
        $product->setStock($stock);
        $this->productRepository->save($product, true);
   }

   public function removeProduct(int $productId, int $sellerId): void
   {
       $product = $this->productRepository->getProduct($productId, $sellerId);
        if (!$product) {
            throw new \Exception('Product not found');
        }
       $this->productRepository->remove($product, true);
   }
   
}