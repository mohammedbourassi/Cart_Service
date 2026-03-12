<?php

namespace App\Service;

interface VerifyUserInputServicesInterface
{
    public function verifyItem($item);
    public function verifyShippingAddress(string $shipping_address);
}