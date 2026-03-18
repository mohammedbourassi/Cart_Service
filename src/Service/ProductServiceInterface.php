<?php

namespace App\Service;

use App\Entity\Products;


interface ProductServiceInterface
{
    public function getProduct(int $productId);
    public function getProductBySeller(int $productId, int $sellerId);
    public function getProductsOfSeller(int $sellerId);
    public function getProductsOfSellerById(int $productId, int $sellerId);
    public function decreaseStock(int $productId, int $quantity): void;
    public function increaseStock(int $productId, int $quantity): void;
    public function saveProduct(Products $product): void;
    public function addProduct(array $data, int $sellerId): void;
    public function changeProductStock(int $productId, int $quantity, int $sellerId): void;
    public function removeProduct(int $productId, int $sellerId): void;
}