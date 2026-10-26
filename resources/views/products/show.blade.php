@extends('layouts.app')

@section('title', 'Detail Produk & Audit Stok: ' . $product->name)

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('products.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-indigo-600">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <span class="text-xs font-mono font-bold text-indigo-600 uppercase">{{ $product->sku }}</span>
                <h1 class="text-xl font-black text-slate-900 tracking-tight">{{ $product->name }}</h1>
            </div>
        </div>

        @can('update', $product)
            <a href="{{ route('products.edit', $product->id) }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-sm transition">
                <i class="fa-solid fa-pen-to-square mr-1.5"></i> Edit Produk
            </a>
        @endcan
    </div>

    <!-- Product Summary Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 grid grid-cols-1 md:grid-cols-4 gap-6">
        <div>
            <span class="text-xs text-slate-400 block font-semibold uppercase">Kategori</span>
            <span class="text-sm font-bold text-slate-800">{{ $product->category->name ?? '-' }}</span>
        </div>
        <div>
            <span class="text-xs text-slate-400 block font-semibold uppercase">Harga Beli / HPP</span>
            <span class="text-base font-extrabold text-slate-700 font-mono">
                Rp {{ number_format($product->purchase_price, 0, ',', '.') }}
            </span>
        </div>
        <div>
            <span class="text-xs text-slate-400 block font-semibold uppercase">Harga Jual Ritel</span>
            <span class="text-base font-extrabold text-emerald-700 font-mono">
                Rp {{ number_format($product->selling_price, 0, ',', '.') }}
            </span>
        </div>
        <div>
            <span class="text-xs text-slate-400 block font-semibold uppercase">Sisa Stok Fisik</span>
            <span class="text-xl font-black {{ $product->stock <= $product->min_stock ? 'text-amber-600' : 'text-slate-900' }} font-mono">
                {{ $product->stock }} unit
            </span>
            <span class="text-[11px] text-slate-400 block">Batas Min: {{ $product->min_stock }}</span>
        </div>
    </div>

    <!-- Product Specific Stock Movement Audit Log -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h3 class="text-base font-bold text-slate-900">Riwayat Mutasi Stok Produk Ini</h3>
                <p class="text-xs text-slate-500">Bukti historis perubahan kuantitas fisik (masuk, keluar, pembatalan).</p>
            </div>
            <a href="{{ route('stock-movements.index', ['product_id' => $product->id]) }}" class="text-xs font-bold text-indigo-600 hover:underline">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 font-bold text-slate-500 uppercase">
                        <th class="py-2.5 px-3">Waktu</th>
                        <th class="py-2.5 px-3 text-center">Jenis Mutasi</th>
                        <th class="py-2.5 px-3 text-center">Stok Awal</th>
                        <th class="py-2.5 px-3 text-center">Perubahan</th>
                        <th class="py-2.5 px-3 text-center">Stok Akhir</th>
                        <th class="py-2.5 px-3">Referensi / Keterangan</th>
                        <th class="py-2.5 px-3">Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($product->stockMovements as $mov)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-3 text-slate-600">{{ $mov->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="py-3 px-3 text-center">
                                @if($mov->type === 'in')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-100 text-sky-800">
                                        <i class="fa-solid fa-arrow-down mr-0.5"></i> Stok Masuk
                                    </span>
                                @elseif($mov->type === 'out')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        <i class="fa-solid fa-arrow-up mr-0.5"></i> Penjualan
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">
                                        <i class="fa-solid fa-rotate-left mr-0.5"></i> Pembatalan
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-center font-mono text-slate-500">{{ $mov->before_stock }}</td>
                            <td class="py-3 px-3 text-center font-mono font-bold {{ $mov->type === 'out' ? 'text-rose-600' : 'text-emerald-600' }}">
                                {{ $mov->type === 'out' ? '-' : '+' }}{{ $mov->quantity }}
                            </td>
                            <td class="py-3 px-3 text-center font-mono font-bold text-slate-800">{{ $mov->after_stock }}</td>
                            <td class="py-3 px-3 text-slate-700 font-medium">{{ $mov->notes ?? '-' }}</td>
                            <td class="py-3 px-3 text-slate-600">{{ $mov->user->name ?? 'Sistem' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-slate-400">Belum ada mutasi stok untuk produk ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
