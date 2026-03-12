<?php
namespace App\Exception;

class ProductOutOfStockException extends \Exception
{
    // optional: you can add custom properties if needed
    private int $requestedQuantity;
    private int $availableStock;

    public function __construct(string $message, int $requestedQuantity = 0, int $availableStock = 0)
    {
        parent::__construct($message);
        $this->requestedQuantity = $requestedQuantity;
        $this->availableStock = $availableStock;
    }

    public function getRequestedQuantity(): int
    {
        return $this->requestedQuantity;
    }

    public function getAvailableStock(): int
    {
        return $this->availableStock;
    }
}