@extends('layouts.app')

@section('title', 'Laporan Nilai Stok & Valuasi Aset')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 no-print">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Laporan Nilai Stok & Aset</h1>
            <p class="text-xs text-slate-500 mt-1">Valuasi inventori gudang berdasarkan harga modal (HPP) dan estimasi nilai jual ritel.</p>
        </div>
        <button type="button" onclick="window.print()" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-sm transition">
            <i class="fa-solid fa-print mr-1.5"></i> Cetak Valuasi Stok
        </button>
    </div>

    <!-- Valuations Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block mb-1">Total Kuantitas Fisik</span>
            <div class="text-2xl font-black text-slate-900 font-mono">
                {{ number_format($totalStockQty) }} <span class="text-sm font-semibold text-slate-500">Unit</span>
            </div>
            <span class="text-[11px] text-slate-400">Total barang siap jual</span>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block mb-1">Nilai Aset Modal (HPP)</span>
            <div class="text-2xl font-black text-indigo-700 font-mono">
                Rp {{ number_format($totalAssetValue, 0, ',', '.') }}
            </div>
            <span class="text-[11px] text-slate-400">Modal tertahan di gudang</span>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block mb-1">Potensi Omzet Ritel</span>
            <div class="text-2xl font-black text-emerald-600 font-mono">
                Rp {{ number_format($totalRetailValue, 0, ',', '.') }}
            </div>
            <span class="text-[11px] text-slate-400">Estimasi nilai bila seluruh stok terjual</span>
        </div>
    </div>

    <!-- Inventory Valuation Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">SKU / Nama Produk</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4 text-center">Sisa Stok</th>
                        <th class="py-3 px-4 text-right">Harga Modal (HPP)</th>
                        <th class="py-3 px-4 text-right">Harga Jual Ritel</th>
                        <th class="py-3 px-4 text-right">Total Nilai Modal</th>
                        <th class="py-3 px-4 text-right">Total Potensi Nilai Jual</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $p)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4">
                                <div class="font-mono text-[10px] text-indigo-600 font-bold">{{ $p->sku }}</div>
                                <div class="font-bold text-slate-800 text-xs">{{ $p->name }}</div>
                            </td>
                            <td class="py-3 px-4 text-slate-600">{{ $p->category->name ?? '-' }}</td>
                            <td class="py-3 px-4 text-center font-bold text-slate-800">{{ $p->stock }} pcs</td>
                            <td class="py-3 px-4 text-right font-mono text-slate-600">Rp {{ number_format($p->purchase_price, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-mono text-slate-600">Rp {{ number_format($p->selling_price, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-indigo-700">
                                Rp {{ number_format($p->stock * $p->purchase_price, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-black text-emerald-700">
                                Rp {{ number_format($p->stock * $p->selling_price, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Belum ada data produk inventori.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 no-print">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
