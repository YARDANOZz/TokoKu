@extends('layouts.app')

@section('title', 'Katalog Data Produk')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Katalog Produk</h1>
            <p class="text-xs text-slate-500 mt-1">
                @if(auth()->user()->isOwner())
                    Kelola data produk, harga jual/beli, dan pantau stok minimum inventori.
                @else
                    Daftar produk dan inventori stok ritel (Mode Lihat Saja).
                @endif
            </p>
        </div>

        @can('create', App\Models\Product::class)
            <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white shadow-md transition">
                <i class="fa-solid fa-plus mr-2"></i> Tambah Produk Baru
            </a>
        @endcan
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Cari Nama / SKU</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik kata kunci..."
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Kategori</label>
                <select name="category_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Status Stok</label>
                <select name="stock_status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">Semua Kondisi</option>
                    <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Stok Menipis (<= Min)</option>
                    <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Stok Habis (0)</option>
                    <option value="available" {{ request('stock_status') == 'available' ? 'selected' : '' }}>Tersedia (> 0)</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('products.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition" title="Reset">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">SKU / Nama</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4 text-right">Harga Beli</th>
                        <th class="py-3 px-4 text-right">Harga Jual</th>
                        <th class="py-3 px-4 text-center">Stok Sisa</th>
                        <th class="py-3 px-4 text-center">Batas Min</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $product)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-mono text-[10px] font-bold text-indigo-600">{{ $product->sku }}</div>
                                <a href="{{ route('products.show', $product->id) }}" class="font-bold text-slate-900 text-xs hover:text-indigo-600">
                                    {{ $product->name }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-600">
                                {{ $product->category->name ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono text-xs text-slate-500">
                                Rp {{ number_format($product->purchase_price, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono text-xs font-bold text-slate-900">
                                Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-black text-sm {{ $product->stock <= $product->min_stock ? ($product->stock <= 0 ? 'text-rose-600' : 'text-amber-600') : 'text-slate-800' }}">
                                {{ $product->stock }}
                            </td>
                            <td class="py-3.5 px-4 text-center text-xs text-slate-500">
                                {{ $product->min_stock }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($product->stock <= 0)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                        Habis
                                    </span>
                                @elseif($product->stock <= $product->min_stock)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                        Menipis
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        Aman
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center space-x-1">
                                    <a href="{{ route('products.show', $product->id) }}" class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition" title="Riwayat Stok">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                    </a>

                                    @can('update', $product)
                                        <a href="{{ route('products.edit', $product->id) }}" class="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-slate-100 rounded-lg transition" title="Edit Produk">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    @endcan

                                    @can('delete', $product)
                                        <form method="POST" action="{{ route('products.destroy', $product->id) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan produk ini? (Soft delete, riwayat transaksi tetap aman)')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-slate-100 rounded-lg transition" title="Hapus (Soft Delete)">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400 text-xs">
                                Tidak ada data produk yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
