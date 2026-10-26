@extends('layouts.app')

@section('title', 'Pesanan Saya & Profil')

@section('content')
<div class="space-y-6">

    <!-- Top Banner -->
    <div class="bg-gradient-to-r from-emerald-800 to-teal-900 rounded-2xl p-6 text-white shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/20 text-white">
                    <i class="fa-solid fa-user mr-1"></i> Akun Pelanggan
                </span>
                <span class="text-xs text-emerald-200">Riwayat Belanja</span>
            </div>
            <h2 class="text-2xl font-black mt-2 tracking-tight">Halo, {{ auth()->user()->name }}</h2>
            <p class="text-sm text-emerald-100 mt-0.5">Kelola riwayat belanja dan pesanan produk ritel Anda.</p>
        </div>
        <a href="{{ route('catalog.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl text-xs font-bold bg-white hover:bg-emerald-50 text-emerald-800 shadow-md transition">
            <i class="fa-solid fa-bag-shopping mr-2"></i> Belanja Lagi di Katalog
        </a>
    </div>

    <!-- User Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Belanjaan Selesai</span>
                <div class="text-2xl font-black text-slate-900 mt-1">
                    Rp {{ number_format($totalSpent, 0, ',', '.') }}
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-receipt"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Jumlah Transaksi Selesai</span>
                <div class="text-2xl font-black text-slate-900 mt-1">
                    {{ $completedOrdersCount }} Pesanan
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-bag-shopping"></i>
            </div>
        </div>
    </div>

    <!-- Orders History Table -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-slate-900">Daftar Transaksi Saya</h3>
            <span class="text-xs text-slate-500">Menampilkan pesanan yang pernah Anda lakukan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3">No. Invoice</th>
                        <th class="py-3">Tanggal</th>
                        <th class="py-3">Metode Bayar</th>
                        <th class="py-3 text-right">Total Belanja</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 font-bold text-indigo-600 text-xs">
                                {{ $order->invoice_number }}
                            </td>
                            <td class="py-3 text-xs text-slate-600">
                                {{ $order->sale_date->format('d M Y, H:i') }}
                            </td>
                            <td class="py-3 text-xs uppercase font-semibold text-slate-700">
                                {{ $order->payment_method }}
                            </td>
                            <td class="py-3 text-right font-bold text-slate-900">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3 text-center">
                                @if($order->status === 'completed')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        Selesai
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                        Dibatalkan
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 text-center">
                                <a href="{{ route('sales.show', $order->id) }}" class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-slate-100 hover:bg-indigo-50 text-indigo-600 hover:text-indigo-800 transition">
                                    <i class="fa-solid fa-receipt mr-1"></i> Struk
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 text-sm">
                                <i class="fa-solid fa-basket-shopping text-3xl block mb-2 text-slate-300"></i>
                                Anda belum memiliki riwayat transaksi belanja.
                                <div class="mt-2">
                                    <a href="{{ route('catalog.index') }}" class="text-indigo-600 font-bold hover:underline">
                                        Mulai Belanja Sekarang &rarr;
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
