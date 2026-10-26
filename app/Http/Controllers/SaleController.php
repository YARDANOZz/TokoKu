<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Exceptions\TransactionAlreadyCancelledException;
use App\Http\Requests\CancelSaleRequest;
use App\Http\Requests\StoreSaleRequest;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    protected SaleService $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    public function index(Request $request)
    {
        $query = Sale::with(['cashier', 'user', 'items.product']);

        // Non-staff and non-owner (regular users) only see their own sales
        if (auth()->user()->isUser()) {
            $query->where('user_id', auth()->id());
        }

        // Search by invoice or customer name
        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('invoice_number', 'like', "%{$keyword}%")
                  ->orWhere('customer_name', 'like', "%{$keyword}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('sale_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('sale_date', '<=', $request->end_date);
        }

        $sales = $query->latest('sale_date')->latest('id')->paginate(10)->withQueryString();

        return view('sales.index', compact('sales'));
    }

    /**
     * POS Kasir Screen (Owner & Staff)
     */
    public function create()
    {
        // Only owner and staff can access POS Kasir
        if (auth()->user()->isUser()) {
            return redirect()->route('catalog.index');
        }

        $products = Product::availableForCatalog()
            ->with('category')
            ->orderBy('name')
            ->get();

        return view('sales.pos', compact('products'));
    }

    /**
     * Store sale transaction (Multi-item)
     */
    public function store(StoreSaleRequest $request)
    {
        try {
            $saleData = [
                'customer_name' => $request->input('customer_name', 'Umum / Walk-in'),
                'payment_method' => $request->payment_method,
                'paid_amount' => $request->paid_amount,
            ];

            $user = null;
            $cashier = null;

            if (auth()->user()->isUser()) {
                $user = auth()->user();
            } else {
                $cashier = auth()->user();
            }

            $sale = $this->saleService->createSale(
                $saleData,
                $request->items,
                $user,
                $cashier
            );

            return redirect()->route('sales.show', $sale->id)
                ->with('success', "Transaksi penjualan berhasil disimpan! Invoice: {$sale->invoice_number}. Stok berkurang otomatis.");
        } catch (InsufficientStockException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem saat memproses transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Display Sale details & printable receipt
     */
    public function show(Sale $sale)
    {
        $this->authorize('view', $sale);

        $sale->load(['cashier', 'user', 'cancelledBy', 'items.product']);
        return view('sales.show', compact('sale'));
    }

    /**
     * Cancel an existing completed sale (Owner ONLY)
     * Restores stock, logs movements, prevents double cancellation.
     */
    public function cancel(CancelSaleRequest $request, Sale $sale)
    {
        // Enforce Server-Side Gate/Policy Authorization (Owner only!)
        $this->authorize('cancel', $sale);

        try {
            $this->saleService->cancelSale(
                $sale,
                auth()->user(),
                $request->cancel_reason
            );

            return redirect()->route('sales.show', $sale->id)
                ->with('success', "Transaksi {$sale->invoice_number} berhasil DIBATALKAN. Stok semua item telah dikembalikan ke inventori dan riwayat audit telah dicatat.");
        } catch (TransactionAlreadyCancelledException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }
}
