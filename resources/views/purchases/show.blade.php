@extends('layouts.app')

@section('title', 'Rincian Stok Masuk ' . $purchase->invoice_number)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between no-print">
        <a href="{{ route('purchases.index') }}" class="text-xs font-bold text-slate-600 hover:text-indigo-600 flex items-center">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Riwayat
        </a>
        <button type="button" onclick="window.print()" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-sm transition">
            <i class="fa-solid fa-print mr-1.5"></i> Cetak Bukti Penerimaan
        </button>
    </div>

    <!-- Purchase Receipt Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-md p-6 sm:p-10">
        <!-- Header -->
        <div class="flex justify-between items-start pb-6 border-b border-dashed border-slate-300">
            <div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[11px] font-bold bg-sky-100 text-sky-800 mb-2">
                    <i class="fa-solid fa-truck-ramp-box mr-1"></i> BUKTI PENERIMAAN STOK
                </span>
                <h2 class="text-xl font-black text-slate-900 font-mono">{{ $purchase->invoice_number }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">Tanggal Penerimaan: {{ $purchase->purchase_date->format('d F Y') }}</p>
            </div>
            <div class="text-right">
                <span class="text-xs font-bold text-slate-400 block uppercase">Pemasok:</span>
                <span class="text-base font-extrabold text-slate-800">{{ $purchase->supplier->name }}</span>
                <span class="text-xs text-slate-500 block">{{ $purchase->supplier->phone ?? '-' }}</span>
            </div>
        </div>

        @if($purchase->notes)
            <div class="py-3 px-4 bg-slate-50 rounded-xl my-4 text-xs text-slate-600 border border-slate-200">
                <span class="font-bold text-slate-800">Catatan:</span> {{ $purchase->notes }}
            </div>
        @endif

        <!-- Items Table -->
        <div class="py-6">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-2">Item Produk</th>
                        <th class="py-2 text-center">Jumlah Diterima</th>
                        <th class="py-2 text-right">Harga Beli Satuan</th>
                        <th class="py-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($purchase->items as $item)
                        <tr>
                            <td class="py-3">
                                <div class="font-bold text-slate-800">{{ $item->product->name ?? 'Produk Dihapus' }}</div>
                                <div class="text-[10px] font-mono text-slate-400">SKU: {{ $item->product->sku ?? '-' }}</div>
                            </td>
                            <td class="py-3 text-center font-bold text-slate-800">
                                +{{ $item->quantity }} pcs
                            </td>
                            <td class="py-3 text-right font-mono text-slate-600">
                                Rp {{ number_format($item->purchase_price, 0, ',', '.') }}
                            </td>
                            <td class="py-3 text-right font-bold text-slate-900 font-mono">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals & Operator -->
        <div class="pt-4 border-t border-dashed border-slate-300 flex justify-between items-center text-xs">
            <div>
                <span class="text-slate-400 block">Diterima oleh Operator:</span>
                <span class="font-bold text-slate-800">{{ $purchase->user->name }}</span>
            </div>
            <div class="text-right">
                <span class="text-slate-400 block uppercase font-bold text-[10px]">Total Nilai Pengadaan:</span>
                <span class="text-2xl font-black text-indigo-700 font-mono">
                    Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>
</div>
@endsection
