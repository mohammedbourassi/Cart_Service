<?php

namespace App\Service;

interface VerifyUserInputServicesInterface
{
    public function verifyItem($item);
    public function verifyShippingAddress(string $shipping_address);
    public function verifyProduct(array $product);
    public function verifyStock(int $stock);
}