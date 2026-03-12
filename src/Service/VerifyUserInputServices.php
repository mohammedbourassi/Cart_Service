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
}