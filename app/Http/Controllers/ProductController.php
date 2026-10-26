<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Search by name or SKU
        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('sku', 'like', "%{$keyword}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by stock status
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->whereRaw('stock <= min_stock AND stock > 0');
            } elseif ($request->stock_status === 'out') {
                $query->where('stock', '<=', 0);
            } elseif ($request->stock_status === 'available') {
                $query->where('stock', '>', 0);
            }
        }

        $products = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::all();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $this->authorize('create', Product::class);

        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request)
    {
        $this->authorize('create', Product::class);

        $data = $request->validated();

        // Image upload handling
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = $imagePath;
        }

        // Initial stock is 0 (stock only modified via Purchases/Stok Masuk, Sales, or Cancellation!)
        $data['stock'] = 0;

        $product = Product::create($data);

        return redirect()->route('products.index')
            ->with('success', "Produk '{$product->name}' (SKU: {$product->sku}) berhasil ditambahkan dengan stok awal 0. Lakukan 'Stok Masuk' untuk mengisi stok barang.");
    }

    public function show(Product $product)
    {
        $product->load(['category', 'stockMovements' => function ($q) {
            $q->with('user')->latest()->take(20);
        }]);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);

        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->authorize('update', $product);

        $data = $request->validated();

        // Image update handling
        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        // Notice: 'stock' is intentionally NEVER updated here!
        $product->update($data);

        return redirect()->route('products.index')
            ->with('success', "Data produk '{$product->name}' berhasil diperbarui.");
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        // Soft delete the product so past transaction history remains intact
        $productName = $product->name;
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', "Produk '{$productName}' berhasil dinonaktifkan (soft delete). Riwayat transaksi tetap tersimpan.");
    }
}
