<?php

namespace App\Service;

use App\Entity\Products;

interface ProductServiceInterface
{
    public function getProduct(int $productId): Products;
    public function decreaseStock(int $productId, int $quantity): void;
    public function increaseStock(int $productId, int $quantity): void;
    public function getProductName(Products $product):string;
}