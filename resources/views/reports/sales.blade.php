@extends('layouts.app')

@section('title', 'Laporan Penjualan & Laba Rugi')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 no-print">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Laporan Penjualan</h1>
            <p class="text-xs text-slate-500 mt-1">Rekapitulasi penjualan, omzet ritel, dan analisis laba kotor.</p>
        </div>
        <button type="button" onclick="window.print()" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-sm transition">
            <i class="fa-solid fa-print mr-1.5"></i> Cetak Laporan
        </button>
    </div>

    <!-- Date Range Filter Form (no-print) -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm no-print">
        <form method="GET" action="{{ route('reports.sales') }}" class="flex flex-col sm:flex-row gap-3 items-end">
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

    <!-- Report Header (For Print and Screen) -->
    <div class="p-6 bg-slate-900 text-white rounded-2xl shadow-sm flex justify-between items-center">
        <div>
            <span class="text-[11px] text-slate-400 font-bold uppercase tracking-wider block">Periode Laporan:</span>
            <span class="text-base font-extrabold text-amber-300">
                {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}
            </span>
        </div>
        <div class="text-right">
            <span class="text-xs text-slate-400 block">Dicetak Oleh:</span>
            <span class="text-sm font-bold text-white">{{ auth()->user()->name }} ({{ strtoupper(auth()->user()->role) }})</span>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block mb-1">Total Omzet Penjualan</span>
            <div class="text-xl font-black text-slate-900 font-mono">
                Rp {{ number_format($totalOmzet, 0, ',', '.') }}
            </div>
            <span class="text-[11px] text-slate-400">Dari transaksi selesai</span>
        </div>

        @if(auth()->user()->isOwner())
            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block mb-1">Total Keuntungan Kotor</span>
                <div class="text-xl font-black text-emerald-600 font-mono">
                    Rp {{ number_format($totalGrossProfit, 0, ',', '.') }}
                </div>
                <span class="text-[11px] text-slate-400">Omzet - Total HPP barang</span>
            </div>
        @endif

        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block mb-1">Transaksi Selesai</span>
            <div class="text-xl font-black text-indigo-700 font-mono">
                {{ $totalTransactions }}
            </div>
            <span class="text-[11px] text-slate-400">Struk berhasil</span>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block mb-1">Transaksi Dibatalkan</span>
            <div class="text-xl font-black text-rose-600 font-mono">
                {{ $cancelledCount }}
            </div>
            <span class="text-[11px] text-slate-400">Stok telah diretur otomatis</span>
        </div>
    </div>

    <!-- Transactions Detail Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">Invoice</th>
                        <th class="py-3 px-4">Tanggal & Waktu</th>
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4 text-center">Jumlah Item</th>
                        <th class="py-3 px-4 text-center">Metode</th>
                        <th class="py-3 px-4 text-right">Nilai Transaksi</th>
                        <th class="py-3 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sales as $s)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-mono font-bold text-indigo-600">
                                <a href="{{ route('sales.show', $s->id) }}" class="hover:underline">{{ $s->invoice_number }}</a>
                            </td>
                            <td class="py-3 px-4 text-slate-600">{{ $s->sale_date->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-4 font-medium text-slate-800">{{ $s->customer_name }}</td>
                            <td class="py-3 px-4 text-center">{{ $s->items->sum('quantity') }} unit</td>
                            <td class="py-3 px-4 text-center uppercase font-semibold text-slate-600">{{ $s->payment_method }}</td>
                            <td class="py-3 px-4 text-right font-black text-slate-900 font-mono">
                                Rp {{ number_format($s->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($s->status === 'completed')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Selesai</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">Batal</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Tidak ada transaksi pada rentang tanggal ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 no-print">
            {{ $sales->links() }}
        </div>
    </div>
</div>
@endsection
