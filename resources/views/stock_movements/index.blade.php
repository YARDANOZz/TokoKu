@extends('layouts.app')

@section('title', 'Mutasi Stok & Log Audit Inventori')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Mutasi Stok & Log Audit</h1>
            <p class="text-xs text-slate-500 mt-1">Catatan permanen setiap perubahan fisik stok (Masuk, Penjualan, & Pembatalan).</p>
        </div>
        <div class="flex items-center space-x-2">
            <span class="px-3 py-1.5 rounded-xl bg-indigo-50 text-indigo-700 text-xs font-bold border border-indigo-200">
                <i class="fa-solid fa-lock mr-1.5"></i> Audit Trail Terkunci
            </span>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('stock-movements.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Filter Produk</label>
                <select name="product_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-1 focus:ring-indigo-500">
                    <option value="">Semua Produk</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }} ({{ $p->sku }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Jenis Mutasi</label>
                <select name="type" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-1 focus:ring-indigo-500">
                    <option value="">Semua Jenis</option>
                    <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Stok Masuk (Pembelian)</option>
                    <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Stok Keluar (Penjualan)</option>
                    <option value="cancellation" {{ request('type') == 'cancellation' ? 'selected' : '' }}>Pengembalian (Pembatalan)</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-1 focus:ring-indigo-500">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('stock-movements.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition" title="Reset">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Movements Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">Waktu Audit</th>
                        <th class="py-3 px-4">Produk</th>
                        <th class="py-3 px-4 text-center">Jenis</th>
                        <th class="py-3 px-4 text-center">Stok Awal</th>
                        <th class="py-3 px-4 text-center">Perubahan</th>
                        <th class="py-3 px-4 text-center">Stok Akhir</th>
                        <th class="py-3 px-4">Referensi / Keterangan</th>
                        <th class="py-3 px-4">Oleh Pengguna</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($movements as $m)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-4 text-xs font-mono text-slate-600">
                                {{ $m->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 text-xs">{{ $m->product->name ?? 'Produk Dihapus' }}</div>
                                <div class="text-[10px] font-mono text-slate-400">SKU: {{ $m->product->sku ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($m->type === 'in')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 text-sky-800">
                                        <i class="fa-solid fa-arrow-down mr-1"></i> Masuk
                                    </span>
                                @elseif($m->type === 'out')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        <i class="fa-solid fa-arrow-up mr-1"></i> Keluar (Jual)
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                        <i class="fa-solid fa-rotate-left mr-1"></i> Pembatalan
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono text-xs text-slate-500 font-bold">
                                {{ $m->before_stock }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-black text-xs {{ $m->type === 'out' ? 'text-rose-600' : 'text-emerald-600' }}">
                                {{ $m->type === 'out' ? '-' : '+' }}{{ $m->quantity }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-black text-xs text-slate-900">
                                {{ $m->after_stock }}
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-700">
                                <div class="font-medium">{{ $m->notes ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400 uppercase">Ref: {{ $m->reference_type }} #{{ $m->reference_id }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-600">
                                {{ $m->user->name ?? 'Sistem' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400 text-xs">
                                Tidak ada catatan mutasi stok yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $movements->links() }}
        </div>
    </div>
</div>
@endsection
