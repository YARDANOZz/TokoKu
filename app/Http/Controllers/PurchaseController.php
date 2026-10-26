<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    protected PurchaseService $purchaseService;

    public function __construct(PurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }

    public function index(Request $request)
    {
        $query = Purchase::with(['supplier', 'user', 'items.product']);

        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('invoice_number', 'like', "%{$keyword}%")
                  ->orWhereHas('supplier', function ($sq) use ($keyword) {
                      $sq->where('name', 'like', "%{$keyword}%");
                  });
            });
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('purchase_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('purchase_date', '<=', $request->end_date);
        }

        $purchases = $query->latest('purchase_date')->latest('id')->paginate(10)->withQueryString();
        $suppliers = Supplier::all();

        return view('purchases.index', compact('purchases', 'suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::orderBy('name')->get();

        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(StorePurchaseRequest $request)
    {
        try {
            $purchase = $this->purchaseService->createPurchase(
                $request->only(['supplier_id', 'purchase_date', 'notes']),
                $request->items,
                auth()->user()
            );

            return redirect()->route('purchases.show', $purchase->id)
                ->with('success', "Stok masuk berhasil dicatat! Invoice: {$purchase->invoice_number}. Stok produk telah bertambah secara otomatis.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memproses stok masuk: ' . $e->getMessage());
        }
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'user', 'items.product']);
        return view('purchases.show', compact('purchase'));
    }
}
