<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    protected string $productName;
    protected int $requestedQty;
    protected int $availableStock;

    public function __construct(string $productName, int $requestedQty, int $availableStock)
    {
        $this->productName = $productName;
        $this->requestedQty = $requestedQty;
        $this->availableStock = $availableStock;

        $message = "Stok untuk produk '{$productName}' tidak mencukupi. Diminta: {$requestedQty}, Tersedia: {$availableStock}.";
        parent::__construct($message);
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getRequestedQty(): int
    {
        return $this->requestedQty;
    }

    public function getAvailableStock(): int
    {
        return $this->availableStock;
    }
}
