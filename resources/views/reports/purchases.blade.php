@extends('layouts.app')

@section('title', 'Laporan Pembelian & Stok Masuk')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 no-print">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Laporan Pembelian & Stok Masuk</h1>
            <p class="text-xs text-slate-500 mt-1">Rekapitulasi pengeluaran dan pasokan inventori dari mitra pemasok.</p>
        </div>
        <button type="button" onclick="window.print()" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-sm transition">
            <i class="fa-solid fa-print mr-1.5"></i> Cetak Laporan
        </button>
    </div>

    <!-- Filter (no-print) -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm no-print">
        <form method="GET" action="{{ route('reports.purchases') }}" class="flex flex-col sm:flex-row gap-3 items-end">
            <div class="flex-1">
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-1 focus:ring-indigo-500">
            </div>

            <div class="flex-1">
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-1 focus:ring-indigo-500">
            </div>

            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition">
                <i class="fa-solid fa-magnifying-glass mr-1"></i> Tampilkan
            </button>
        </form>
    </div>

    <!-- Summary Box -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block mb-1">Total Biaya Pengadaan</span>
            <div class="text-2xl font-black text-slate-900 font-mono">
                Rp {{ number_format($totalSpent, 0, ',', '.') }}
            </div>
            <span class="text-[11px] text-slate-400">Total belanja stok dari supplier</span>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block mb-1">Total Transaksi Masuk</span>
            <div class="text-2xl font-black text-indigo-700 font-mono">
                {{ $totalPurchases }} Transaksi
            </div>
            <span class="text-[11px] text-slate-400">Penerimaan pasokan barang</span>
        </div>
    </div>

    <!-- Purchases Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">Invoice</th>
                        <th class="py-3 px-4">Tanggal Terima</th>
                        <th class="py-3 px-4">Pemasok</th>
                        <th class="py-3 px-4 text-center">Jumlah Barang</th>
                        <th class="py-3 px-4 text-right">Total Nilai</th>
                        <th class="py-3 px-4">Pencatat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($purchases as $p)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-mono font-bold text-indigo-600">
                                <a href="{{ route('purchases.show', $p->id) }}" class="hover:underline">{{ $p->invoice_number }}</a>
                            </td>
                            <td class="py-3 px-4 text-slate-600">{{ $p->purchase_date->format('d/m/Y') }}</td>
                            <td class="py-3 px-4 font-bold text-slate-800">{{ $p->supplier->name }}</td>
                            <td class="py-3 px-4 text-center font-bold text-slate-700">{{ $p->items->sum('quantity') }} unit</td>
                            <td class="py-3 px-4 text-right font-black text-slate-900 font-mono">
                                Rp {{ number_format($p->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-slate-600">{{ $p->user->name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Tidak ada transaksi stok masuk pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 no-print">
            {{ $purchases->links() }}
        </div>
    </div>
</div>
@endsection
