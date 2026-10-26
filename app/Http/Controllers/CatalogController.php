<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Models\Category;
use App\Models\Product;
use App\Services\SaleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CatalogController extends Controller
{
    protected SaleService $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    /**
     * Display public catalog: ONLY products with stock > 0
     */
    public function index(Request $request)
    {
        $query = Product::availableForCatalog()->with('category');

        // Search by keyword
        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('sku', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $products = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::withCount(['products' => function ($q) {
            $q->where('stock', '>', 0);
        }])->get();

        return view('catalog.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        if ($product->stock <= 0) {
            return redirect()->route('catalog.index')->with('error', 'Produk ini sedang habis stok.');
        }

        return view('catalog.show', compact('product'));
    }

    public function cart()
    {
        $cart = Session::get('cart', []);
        $items = [];
        $total = 0;

        foreach ($cart as $productId => $qty) {
            $product = Product::find($productId);
            if ($product && $product->stock > 0) {
                // Ensure cart qty does not exceed available stock
                $actualQty = min($qty, $product->stock);
                $subtotal = $actualQty * $product->selling_price;
                $total += $subtotal;

                $items[] = [
                    'product' => $product,
                    'quantity' => $actualQty,
                    'subtotal' => $subtotal,
                ];
            }
        }

        return view('catalog.cart', compact('items', 'total'));
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $productId = $request->product_id;
        $qtyToAdd = $request->input('quantity', 1);

        $product = Product::find($productId);

        if (!$product || $product->stock <= 0) {
            return back()->with('error', 'Produk tidak tersedia atau stok habis.');
        }

        $cart = Session::get('cart', []);
        $currentCartQty = $cart[$productId] ?? 0;
        $newQty = $currentCartQty + $qtyToAdd;

        if ($newQty > $product->stock) {
            return back()->with('error', "Stok tidak mencukupi! Tersedia hanya {$product->stock} unit.");
        }

        $cart[$productId] = $newQty;
        Session::put('cart', $cart);

        return back()->with('success', "'{$product->name}' berhasil ditambahkan ke keranjang belanja!");
    }

    public function updateCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
        ]);

        $productId = $request->product_id;
        $qty = $request->quantity;
        $cart = Session::get('cart', []);

        if ($qty <= 0) {
            unset($cart[$productId]);
            Session::put('cart', $cart);
            return back()->with('success', 'Produk dihapus dari keranjang.');
        }

        $product = Product::find($productId);
        if ($product && $qty > $product->stock) {
            return back()->with('error', "Stok produk hanya tersisa {$product->stock} unit.");
        }

        $cart[$productId] = $qty;
        Session::put('cart', $cart);

        return back()->with('success', 'Keranjang belanja berhasil diperbarui.');
    }

    public function removeFromCart($productId)
    {
        $cart = Session::get('cart', []);
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            Session::put('cart', $cart);
        }

        return back()->with('success', 'Item berhasil dihapus dari keranjang.');
    }

    public function checkout(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk menyelesaikan pesanan.');
        }

        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect()->route('catalog.index')->with('error', 'Keranjang belanja Anda masih kosong.');
        }

        $items = [];
        foreach ($cart as $productId => $qty) {
            $items[] = [
                'product_id' => $productId,
                'quantity' => $qty,
            ];
        }

        $request->validate([
            'payment_method' => 'required|in:cash,transfer,qris',
        ]);

        try {
            $saleData = [
                'customer_name' => auth()->user()->name,
                'payment_method' => $request->payment_method,
            ];

            $sale = $this->saleService->createSale($saleData, $items, auth()->user());

            // Clear session cart
            Session::forget('cart');

            return redirect()->route('sales.show', $sale->id)
                ->with('success', 'Pesanan Anda berhasil dibuat! Nomor Invoice: ' . $sale->invoice_number);
        } catch (InsufficientStockException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }
}
