<?php

namespace App\Mapper;

use App\Entity\Products;

class ProductMapper
{
    public static function toEntity(array $data): Products
    {
        $product = new Products();
        $product->setName($data['name']);
        $product->setDescription($data['description']);
        $product->setPrice($data['price']);
        $product->setStock($data['stock']);
        $product->setType($data['type']);
        $product->setSellerId($data['seller_id']);
        return $product;
    }
}