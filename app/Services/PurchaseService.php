<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Process incoming stock (purchase) in a single transaction.
     *
     * @param array $purchaseData [supplier_id, purchase_date, notes]
     * @param array $items Array of ['product_id', 'quantity', 'purchase_price']
     * @param User $user
     * @return Purchase
     */
    public function createPurchase(array $purchaseData, array $items, User $user): Purchase
    {
        return DB::transaction(function () use ($purchaseData, $items, $user) {
            $totalAmount = 0;
            $preparedItems = [];

            foreach ($items as $item) {
                $productId = $item['product_id'];
                $quantity = (int) $item['quantity'];
                $price = (float) $item['purchase_price'];

                if ($quantity <= 0) {
                    continue;
                }

                $product = Product::lockForUpdate()->find($productId);
                if (!$product) {
                    throw new \InvalidArgumentException("Produk dengan ID {$productId} tidak ditemukan.");
                }

                $subtotal = $quantity * $price;
                $totalAmount += $subtotal;

                $preparedItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'purchase_price' => $price,
                    'subtotal' => $subtotal,
                ];
            }

            if (empty($preparedItems)) {
                throw new \InvalidArgumentException("Transaksi pembelian harus memiliki minimal 1 produk.");
            }

            // Generate Invoice Number
            $datePrefix = Carbon::now()->format('Ymd');
            $latestPurchase = Purchase::whereDate('created_at', Carbon::today())->latest('id')->first();
            $nextSequence = $latestPurchase ? ((int) substr($latestPurchase->invoice_number, -4)) + 1 : 1;
            $invoiceNumber = 'PB-' . $datePrefix . '-' . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);

            while (Purchase::where('invoice_number', $invoiceNumber)->exists()) {
                $nextSequence++;
                $invoiceNumber = 'PB-' . $datePrefix . '-' . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
            }

            // Create Purchase Header
            $purchase = Purchase::create([
                'invoice_number' => $invoiceNumber,
                'supplier_id' => $purchaseData['supplier_id'],
                'user_id' => $user->id,
                'purchase_date' => $purchaseData['purchase_date'] ?? Carbon::today(),
                'total_amount' => $totalAmount,
                'notes' => $purchaseData['notes'] ?? null,
            ]);

            // Create Purchase Items, update product purchase price, increment stock
            foreach ($preparedItems as $prep) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $prep['product']->id,
                    'quantity' => $prep['quantity'],
                    'purchase_price' => $prep['purchase_price'],
                    'subtotal' => $prep['subtotal'],
                ]);

                // Update product purchase price to the latest cost
                $prep['product']->purchase_price = $prep['purchase_price'];
                $prep['product']->save();

                // Increment stock and log movement
                $this->stockService->recordMovement(
                    $prep['product'],
                    'in',
                    $prep['quantity'],
                    'purchase',
                    $purchase->id,
                    $user->id,
                    "Stok masuk supplier invoice: {$purchase->invoice_number}"
                );
            }

            return $purchase;
        });
    }
}
