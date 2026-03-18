<?php

namespace App\Mapper;

use App\Entity\Products;
use App\Enum\ProductType;

class ProductMapper
{
    public static function toEntity(array $data, int $sellerId): Products
    {
        $product = new Products();
        $product->setName($data['name']);
        $product->setDescription($data['description']);
        $product->setPrice($data['price']);
        $product->setStock($data['stock']);
        $product->setType(ProductType ::from($data['type']));
        $product->setSellerId($sellerId);
        $product->setCreatedAt(new \DateTimeImmutable());
        return $product;
    }

    public static function toEntityFromArray(array $data): Products
    {
        $product = new Products();
        $product->setName($data['name']);
        $product->setDescription($data['description']);
        $product->setPrice($data['price']);
        $product->setStock($data['stock']);
        $product->setType($data['type']);
        $product->setSellerId($data['sellerId']);
        $product->setCreatedAt($data['created_at']);
        return $product;
    }

    
}