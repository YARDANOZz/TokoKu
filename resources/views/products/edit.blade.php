@extends('layouts.app')

@section('title', 'Edit Produk: ' . $product->name)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Edit Data Produk</h1>
            <p class="text-xs text-slate-500 mt-1">Perbarui informasi, harga, dan batas stok minimum.</p>
        </div>
        <a href="{{ route('products.index') }}" class="text-xs font-bold text-slate-600 hover:text-indigo-600">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <!-- Current Stock Banner (Read-only) -->
    <div class="p-4 bg-slate-900 text-white rounded-2xl flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div>
                <span class="text-xs text-slate-400 block font-semibold uppercase">Stok Inventori Saat Ini:</span>
                <span class="text-xl font-black text-amber-300 font-mono">{{ $product->stock }} unit</span>
            </div>
        </div>
        <div class="text-right text-xs text-slate-300 max-w-xs">
            <span class="font-bold text-white"><i class="fa-solid fa-lock mr-1"></i> Stok Terkunci:</span>
            Hanya berubah lewat Stok Masuk (Pembelian), Penjualan Kasir, atau Pembatalan.
        </div>
    </div>

    <form method="POST" action="{{ route('products.update', $product->id) }}" enctype="multipart/form-data" class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="sku" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    SKU Produk (Unik) <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 font-mono text-xs focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="category_id" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    Kategori <span class="text-rose-500">*</span>
                </label>
                <select name="category_id" id="category_id" required
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-indigo-500">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label for="name" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                Nama Produk <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label for="description" class="block text-xs font-bold text-slate-700 uppercase mb-1">Deskripsi Produk</label>
            <textarea name="description" id="description" rows="3"
                class="w-full px-4 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-indigo-500">{{ old('description', $product->description) }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label for="purchase_price" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    Harga Beli / HPP (Rp) <span class="text-rose-500">*</span>
                </label>
                <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}" min="0" required
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 font-mono text-xs focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="selling_price" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    Harga Jual Ritel (Rp) <span class="text-rose-500">*</span>
                </label>
                <input type="number" name="selling_price" id="selling_price" value="{{ old('selling_price', $product->selling_price) }}" min="0" required
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 font-mono text-xs focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="min_stock" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    Batas Minimum Stok <span class="text-rose-500">*</span>
                </label>
                <input type="number" name="min_stock" id="min_stock" value="{{ old('min_stock', $product->min_stock) }}" min="0" required
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div>
            <label for="image" class="block text-xs font-bold text-slate-700 uppercase mb-1">Ganti Foto Produk (Opsional)</label>
            @if($product->image)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $product->image) }}" alt="Preview" class="w-16 h-16 rounded-xl object-cover border border-slate-200">
                </div>
            @endif
            <input type="file" name="image" id="image" accept="image/*"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('products.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/30 transition">
                <i class="fa-solid fa-floppy-disk mr-1.5"></i> Perbarui Produk
            </button>
        </div>
    </form>
</div>
@endsection
