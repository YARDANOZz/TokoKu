@extends('layouts.app')

@section('title', 'Riwayat Transaksi Penjualan')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Riwayat Penjualan</h1>
            <p class="text-xs text-slate-500 mt-1">Daftar seluruh transaksi kasir ritel dan pesanan pelanggan.</p>
        </div>
        @if(auth()->user()->isOwner() || auth()->user()->isStaff())
            <a href="{{ route('pos.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-md transition">
                <i class="fa-solid fa-cash-register mr-2"></i> Transaksi Kasir Baru
            </a>
        @endif
    </div>

    <!-- Filters & Search Form -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('sales.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Cari Invoice / Nama</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="TRX-2026... atau nama pelanggan"
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Status Transaksi</label>
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">Semua Status</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan (Cancelled)</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('sales.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition" title="Reset">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Sales Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">Invoice</th>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Pelanggan / Kasir</th>
                        <th class="py-3 px-4">Item</th>
                        <th class="py-3 px-4 text-right">Total Transaksi</th>
                        <th class="py-3 px-4 text-center">Metode</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-indigo-600 text-xs">
                                <a href="{{ route('sales.show', $sale->id) }}" class="hover:underline">
                                    {{ $sale->invoice_number }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-600">
                                {{ $sale->sale_date->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800 text-xs">{{ $sale->customer_name }}</div>
                                <div class="text-[11px] text-slate-400">Kasir: {{ $sale->cashier->name ?? ($sale->user->name ?? 'Online') }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-600">
                                <span class="px-2 py-0.5 rounded-full bg-slate-100 font-semibold text-slate-700">
                                    {{ $sale->items->sum('quantity') }} pcs ({{ $sale->items->count() }} item)
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-slate-900 text-sm">
                                Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700">
                                    {{ $sale->payment_method }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($sale->status === 'completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                        <i class="fa-solid fa-circle-check text-[10px] mr-1"></i> Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800" title="{{ $sale->cancel_reason }}">
                                        <i class="fa-solid fa-ban text-[10px] mr-1"></i> Dibatalkan
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('sales.show', $sale->id) }}" class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">
                                    <i class="fa-solid fa-eye mr-1"></i> Detail / Struk
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400 text-xs">
                                Tidak ada transaksi yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $sales->links() }}
        </div>
    </div>
</div>
@endsection
