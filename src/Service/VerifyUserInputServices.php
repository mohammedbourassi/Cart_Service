<?php

namespace App\Service;

use App\Exception\UserInputInvalideException;
use App\Service\VerifyUserInputServicesInterface;

class VerifyUserInputServices implements VerifyUserInputServicesInterface
{
    public function verifyItem($item)
    {
        if (!isset($item['productId']) || !isset($item['quantity'])) {
            throw new UserInputInvalideException('Product id and quantity are required');
        }
        if (!is_numeric($item['productId']) || !is_numeric($item['quantity'])) {
            throw new UserInputInvalideException('Product id and quantity must be numeric');
        }
        if ($item['quantity'] <= 0) {
            throw new UserInputInvalideException('Quantity must be greater than 0');
        }
    }

    public function verifyShippingAddress(string $shipping_address)
    {
        if (!\preg_match("/^[a-zA-Z0-9\s,'-]*$/", $shipping_address)) {
            throw new UserInputInvalideException('Shipping address is invalid');
        }
        
    }

    public function verifyProduct(array $product)
    {
        if(!isset($product['name']) || !isset($product['description']) || !isset($product['price']) || !isset($product['stock']) || !isset($product['type'])) {
            throw new UserInputInvalideException('All fields are required');
        }
        if(!\preg_match("/^[a-zA-Z0-9\s,'-]*$/", $product['name'])|| !\preg_match("/^[a-zA-Z0-9\s,'-]*$/", $product['name']) || !is_string($product['description']) || !\preg_match("/^[a-zA-Z0-9\s,'-]*$/", $product['description']) || !is_numeric($product['price']) || !is_numeric($product['stock']) || !\preg_match("/^[a-zA-Z]+$/", $product['type'])) {
            throw new UserInputInvalideException('Invalid input type');
        }
         if ($product['price'] <= 0) {  
            throw new UserInputInvalideException('Price must be greater than 0');
        }
        if ($product['stock'] <= 0) {  
            throw new UserInputInvalideException('Stock must be greater than 0');
        }
         if (!in_array($product['type'], ['physical', 'digital'])) {
            throw new UserInputInvalideException('Invalid product type');
        }
    }

    public function verifyStock(int $stock)
    {
        if (!is_numeric($stock) || $stock < 0) {
            throw new UserInputInvalideException('Stock must be a non-negative number');
        }
    }
}