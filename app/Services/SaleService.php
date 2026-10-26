<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Exceptions\TransactionAlreadyCancelledException;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleService
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Process a multi-item sale in a single database transaction.
     *
     * @param array $saleData [customer_name, payment_method, paid_amount]
     * @param array $items Array of ['product_id' => int, 'quantity' => int]
     * @param User|null $user Customer (if authenticated)
     * @param User|null $cashier Staff/Owner who processed transaction
     * @return Sale
     * @throws InsufficientStockException
     */
    public function createSale(array $saleData, array $items, ?User $user = null, ?User $cashier = null): Sale
    {
        return DB::transaction(function () use ($saleData, $items, $user, $cashier) {
            $totalAmount = 0;
            $preparedItems = [];

            // Step 1: Validate stock and calculate total using fresh DB prices
            foreach ($items as $item) {
                $productId = $item['product_id'];
                $quantity = (int) $item['quantity'];

                if ($quantity <= 0) {
                    continue;
                }

                // Lock row for update to avoid concurrency race conditions
                $product = Product::lockForUpdate()->find($productId);

                if (!$product) {
                    throw new \InvalidArgumentException("Produk dengan ID {$productId} tidak ditemukan.");
                }

                if ($product->stock < $quantity) {
                    throw new InsufficientStockException(
                        $product->name,
                        $quantity,
                        $product->stock
                    );
                }

                $sellingPrice = (float) $product->selling_price;
                $purchasePrice = (float) $product->purchase_price;
                $subtotal = $sellingPrice * $quantity;
                $totalAmount += $subtotal;

                $preparedItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'selling_price' => $sellingPrice,
                    'purchase_price' => $purchasePrice,
                    'subtotal' => $subtotal,
                ];
            }

            if (empty($preparedItems)) {
                throw new \InvalidArgumentException("Transaksi harus memiliki minimal 1 produk.");
            }

            $paidAmount = isset($saleData['paid_amount']) ? (float) $saleData['paid_amount'] : $totalAmount;
            if ($paidAmount < $totalAmount) {
                throw new \InvalidArgumentException("Jumlah bayar (Rp " . number_format($paidAmount, 0, ',', '.') . ") kurang dari total belanja (Rp " . number_format($totalAmount, 0, ',', '.') . ").");
            }
            $changeAmount = $paidAmount - $totalAmount;

            // Step 2: Generate Invoice Number
            $datePrefix = Carbon::now()->format('Ymd');
            $latestSale = Sale::whereDate('created_at', Carbon::today())->latest('id')->first();
            $nextSequence = $latestSale ? ((int) substr($latestSale->invoice_number, -4)) + 1 : 1;
            $invoiceNumber = 'TRX-' . $datePrefix . '-' . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);

            // Ensure invoice uniqueness
            while (Sale::where('invoice_number', $invoiceNumber)->exists()) {
                $nextSequence++;
                $invoiceNumber = 'TRX-' . $datePrefix . '-' . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
            }

            // Step 3: Create Sale Header
            $sale = Sale::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => $user?->id,
                'cashier_id' => $cashier?->id,
                'customer_name' => $saleData['customer_name'] ?? ($user ? $user->name : 'Umum / Walk-in'),
                'sale_date' => Carbon::now(),
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount,
                'payment_method' => $saleData['payment_method'] ?? 'cash',
                'status' => 'completed',
            ]);

            // Step 4: Create Sale Items & Reduce Stock with Movement Logs
            foreach ($preparedItems as $prep) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $prep['product']->id,
                    'quantity' => $prep['quantity'],
                    'selling_price' => $prep['selling_price'],
                    'purchase_price' => $prep['purchase_price'],
                    'subtotal' => $prep['subtotal'],
                ]);

                // Reduce stock and log
                $this->stockService->recordMovement(
                    $prep['product'],
                    'out',
                    $prep['quantity'],
                    'sale',
                    $sale->id,
                    $cashier?->id ?? $user?->id,
                    "Penjualan invoice: {$sale->invoice_number}"
                );
            }

            return $sale;
        });
    }

    /**
     * Cancel an existing sale (Owner only), restoring stock and recording movements.
     *
     * @param Sale $sale
     * @param User $cancelledBy
     * @param string $reason
     * @return Sale
     * @throws TransactionAlreadyCancelledException
     */
    public function cancelSale(Sale $sale, User $cancelledBy, string $reason): Sale
    {
        return DB::transaction(function () use ($sale, $cancelledBy, $reason) {
            // Lock sale record to prevent simultaneous cancellations
            $sale = Sale::lockForUpdate()->find($sale->id);

            if ($sale->status === 'cancelled') {
                throw new TransactionAlreadyCancelledException($sale->invoice_number);
            }

            // Update status & metadata
            $sale->update([
                'status' => 'cancelled',
                'cancelled_by' => $cancelledBy->id,
                'cancelled_at' => Carbon::now(),
                'cancel_reason' => $reason,
            ]);

            // Restore stock for all items
            $sale->load('items.product');
            foreach ($sale->items as $item) {
                if ($item->product) {
                    $this->stockService->recordMovement(
                        $item->product,
                        'cancellation',
                        $item->quantity,
                        'sale_cancellation',
                        $sale->id,
                        $cancelledBy->id,
                        "Pembatalan transaksi {$sale->invoice_number}. Alasan: {$reason}"
                    );
                }
            }

            return $sale;
        });
    }
}
