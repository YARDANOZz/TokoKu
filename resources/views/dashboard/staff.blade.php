@extends('layouts.app')

@section('title', 'Dashboard Operasional Staff & Kasir')

@section('content')
<div class="space-y-6">

    <!-- Top Banner -->
    <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 rounded-2xl p-6 text-white shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                    <i class="fa-solid fa-user-tie mr-1"></i> Staff / Kasir View
                </span>
                <span class="text-xs text-blue-200">Operasional Harian</span>
            </div>
            <h2 class="text-2xl font-black mt-2 tracking-tight">Halo, {{ auth()->user()->name }}</h2>
            <p class="text-sm text-slate-300 mt-0.5">Kelola transaksi kasir dan pencatatan stok masuk hari ini.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('pos.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-500 hover:bg-emerald-600 text-white shadow-lg shadow-emerald-500/30 transition">
                <i class="fa-solid fa-cash-register mr-2"></i> Buka Kasir POS
            </a>
            <a href="{{ route('purchases.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl text-xs font-bold bg-sky-500 hover:bg-sky-600 text-white shadow-lg shadow-sky-500/30 transition">
                <i class="fa-solid fa-boxes-packing mr-2"></i> Input Stok Masuk
            </a>
        </div>
    </div>

    <!-- Daily Operasional Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Penjualan Hari Ini</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-cash-register"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 tracking-tight">
                Rp {{ number_format($todaySalesAmount, 0, ',', '.') }}
            </div>
            <p class="text-xs text-slate-500 mt-2">
                {{ $todayTransactionsCount }} transaksi berhasil diproses
            </p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Stok Masuk Hari Ini</span>
                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 tracking-tight">
                {{ $todayPurchasesCount }} <span class="text-sm font-semibold text-slate-500">Penerimaan</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">
                Penerimaan barang dari pemasok
            </p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Produk Stok Rendah</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="text-2xl font-black {{ $lowStockProducts->count() > 0 ? 'text-amber-600' : 'text-slate-900' }} tracking-tight">
                {{ $lowStockProducts->count() }} <span class="text-sm font-semibold text-slate-500">Item</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">
                Perlu dicatat untuk stok masuk berikutnya
            </p>
        </div>
    </div>

    <!-- Quick Shortcuts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Kasir Shortcut Card -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-2xl mb-3">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Layani Pembeli (Kasir POS)</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm">
                    Pilih beberapa barang sekaligus, cek stok otomatis di database, dan hitung kembalian cepat.
                </p>
            </div>
            <a href="{{ route('pos.index') }}" class="px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider transition">
                Mulai Transaksi &rarr;
            </a>
        </div>

        <!-- Stok Masuk Shortcut Card -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center text-2xl mb-3">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Penerimaan Stok Masuk</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm">
                    Input pembelian dari pemasok. Stok produk akan bertambah otomatis dan tercatat di mutasi.
                </p>
            </div>
            <a href="{{ route('purchases.create') }}" class="px-5 py-3 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs uppercase tracking-wider transition">
                Tambah Stok &rarr;
            </a>
        </div>
    </div>

    <!-- Tables Row: Stok Rendah & Transaksi Terbaru -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Low Stock Table -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900">Daftar Stok Menipis</h3>
                <a href="{{ route('purchases.create') }}" class="text-xs font-bold text-sky-600 hover:underline">
                    + Restok Barang
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs font-bold text-slate-500 uppercase">
                            <th class="py-2">Produk</th>
                            <th class="py-2 text-center">Sisa</th>
                            <th class="py-2 text-center">Min</th>
                            <th class="py-2 text-right">Harga Jual</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($lowStockProducts as $lp)
                            <tr>
                                <td class="py-2.5">
                                    <div class="font-semibold text-slate-800 text-xs">{{ $lp->name }}</div>
                                    <div class="text-[11px] font-mono text-slate-400">{{ $lp->sku }}</div>
                                </td>
                                <td class="py-2.5 text-center font-bold {{ $lp->stock <= 0 ? 'text-rose-600' : 'text-amber-600' }}">
                                    {{ $lp->stock }}
                                </td>
                                <td class="py-2.5 text-center text-xs text-slate-500">{{ $lp->min_stock }}</td>
                                <td class="py-2.5 text-right font-medium text-xs text-slate-800">
                                    Rp {{ number_format($lp->selling_price, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-slate-400 text-xs">Semua stok produk aman.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Sales -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900">Transaksi Kasir Terakhir</h3>
                <a href="{{ route('sales.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">
                    Lihat Semua &rarr;
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs font-bold text-slate-500 uppercase">
                            <th class="py-2">Invoice</th>
                            <th class="py-2">Pelanggan</th>
                            <th class="py-2 text-right">Total</th>
                            <th class="py-2 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentSales as $sale)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-2.5">
                                    <a href="{{ route('sales.show', $sale->id) }}" class="font-bold text-indigo-600 hover:underline text-xs">
                                        {{ $sale->invoice_number }}
                                    </a>
                                    <div class="text-[10px] text-slate-400">{{ $sale->sale_date->format('d/m/Y H:i') }}</div>
                                </td>
                                <td class="py-2.5 text-xs text-slate-700">{{ $sale->customer_name }}</td>
                                <td class="py-2.5 text-right font-bold text-xs text-slate-900">
                                    Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="py-2.5 text-center">
                                    @if($sale->status === 'completed')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Selesai</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">Dibatalkan</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-slate-400 text-xs">Belum ada transaksi</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
