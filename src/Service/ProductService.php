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
   public function getProduct(int $productId):Products
   {
       return $this->productRepository->find($productId);
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

   public function getProductName(Products $product):string
   {
       $product = $this->productRepository->find($product);
       return $product->getName();
   }
}