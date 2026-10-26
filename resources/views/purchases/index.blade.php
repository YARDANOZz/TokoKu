@extends('layouts.app')

@section('title', 'Riwayat Stok Masuk (Pembelian)')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Stok Masuk (Pembelian)</h1>
            <p class="text-xs text-slate-500 mt-1">Penerimaan pasokan barang dari pemasok / distributor untuk menambah stok.</p>
        </div>
        <a href="{{ route('purchases.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white shadow-md transition">
            <i class="fa-solid fa-plus mr-2"></i> Input Stok Masuk Baru
        </a>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('purchases.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Cari Invoice / Pemasok</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="PB-2026... atau nama pemasok"
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Pemasok</label>
                <select name="supplier_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">Semua Pemasok</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>
                            {{ $sup->name }}
                        </option>
                    @endforeach
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
                <a href="{{ route('purchases.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition" title="Reset">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Purchases Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">No. Invoice</th>
                        <th class="py-3 px-4">Tanggal Terima</th>
                        <th class="py-3 px-4">Pemasok</th>
                        <th class="py-3 px-4 text-center">Jumlah Barang</th>
                        <th class="py-3 px-4 text-right">Total Biaya</th>
                        <th class="py-3 px-4">Pencatat</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($purchases as $purchase)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-indigo-600 text-xs">
                                <a href="{{ route('purchases.show', $purchase->id) }}" class="hover:underline">
                                    {{ $purchase->invoice_number }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-600">
                                {{ $purchase->purchase_date->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800 text-xs">{{ $purchase->supplier->name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $purchase->supplier->phone ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-sky-50 text-sky-700">
                                    {{ $purchase->items->sum('quantity') }} unit ({{ $purchase->items->count() }} item)
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-slate-900 text-sm">
                                Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-700">
                                {{ $purchase->user->name }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('purchases.show', $purchase->id) }}" class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-slate-100 hover:bg-indigo-50 text-indigo-700 transition">
                                    <i class="fa-solid fa-eye mr-1"></i> Rincian
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 text-xs">
                                Belum ada riwayat stok masuk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $purchases->links() }}
        </div>
    </div>
</div>
@endsection
