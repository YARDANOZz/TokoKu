<?php

namespace App\Exceptions;

use Exception;

class TransactionAlreadyCancelledException extends Exception
{
    public function __construct(string $invoiceNumber)
    {
        parent::__construct("Transaksi dengan invoice {$invoiceNumber} sudah dibatalkan sebelumnya dan tidak dapat dibatalkan lagi.");
    }
}
