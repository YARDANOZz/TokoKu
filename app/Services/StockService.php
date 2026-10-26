<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use InvalidArgumentException;

class StockService
{
    /**
     * Record stock movement and atomically update product stock.
     */
    public function recordMovement(
        Product $product,
        string $type,
        int $quantity,
        string $referenceType,
        ?int $referenceId = null,
        ?int $userId = null,
        ?string $notes = null
    ): StockMovement {
        if ($quantity <= 0) {
            throw new InvalidArgumentException("Jumlah stok harus lebih dari 0.");
        }

        if (!in_array($type, ['in', 'out', 'cancellation'])) {
            throw new InvalidArgumentException("Tipe pergerakan stok tidak valid.");
        }

        $beforeStock = $product->stock;

        if ($type === 'in' || $type === 'cancellation') {
            $afterStock = $beforeStock + $quantity;
        } else { // 'out'
            $afterStock = $beforeStock - $quantity;
        }

        // Update product stock
        $product->stock = $afterStock;
        $product->save();

        // Record immutable stock movement log
        return StockMovement::create([
            'product_id' => $product->id,
            'type' => $type,
            'quantity' => $quantity,
            'before_stock' => $beforeStock,
            'after_stock' => $afterStock,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'user_id' => $userId,
            'notes' => $notes,
        ]);
    }
}
