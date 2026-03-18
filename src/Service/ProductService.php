<?php

namespace App\Service;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use App\Service\ProductServiceInterface;


class ProductService implements ProductServiceInterface
{
    public function __construct(
        private ProductsRepository $productRepository
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

   public function addProduct(Products $product): void
   {
       $this->productRepository->add($product, true);
   }

   public function getProductsOfSeller(int $sellerId) 
   {
        return $this->productRepository->findBy(['seller_id' => $sellerId]);    
   }

   public function changeProductStock(int $productId, int $quantity, int $sellerId): void
   {
       $product = $this->productRepository->getOneProduct($productId, $sellerId);
        if (!$product) {
            throw new \Exception('Product not found');
        }
       $product->setStock($quantity);
       $this->productRepository->add($product, true);
   }

   public function removeProduct(int $productId, int $sellerId): void
   {
       $product = $this->productRepository->getOneProduct($productId, $sellerId);
        if (!$product) {
            throw new \Exception('Product not found');
        }
       $this->productRepository->remove($product, true);
   }
   
}