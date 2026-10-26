@extends('layouts.app')

@section('title', 'Struk Invoice ' . $sale->invoice_number)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Top Action Navigation (No-print) -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('sales.index') }}" class="text-xs font-bold text-slate-600 hover:text-indigo-600 flex items-center">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Riwayat
        </a>
        <div class="flex items-center space-x-2">
            <!-- Print Button -->
            <button type="button" onclick="window.print()" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-sm transition">
                <i class="fa-solid fa-print mr-1.5"></i> Cetak Struk
            </button>

            <!-- Cancellation Button (Owner Only) -->
            @can('cancel', $sale)
                <button type="button" onclick="openCancelModal()" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-sm transition">
                    <i class="fa-solid fa-ban mr-1.5"></i> Batalkan Transaksi
                </button>
            @endcan
        </div>
    </div>

    <!-- Cancellation Status Banner (If Cancelled) -->
    @if($sale->isCancelled())
        <div class="p-5 bg-rose-50 border-2 border-rose-300 rounded-2xl shadow-sm space-y-2">
            <div class="flex items-center space-x-2 text-rose-700 font-extrabold text-sm uppercase tracking-wider">
                <i class="fa-solid fa-circle-xmark text-lg"></i>
                <span>Transaksi Ini Telah Dibatalkan (Cancelled)</span>
            </div>
            <div class="text-xs text-rose-800 space-y-1">
                <p><strong>Dibatalkan Oleh:</strong> {{ $sale->cancelledBy->name ?? 'Owner TokoKu' }} ({{ $sale->cancelledBy->email ?? '-' }})</p>
                <p><strong>Waktu Pembatalan:</strong> {{ $sale->cancelled_at ? $sale->cancelled_at->format('d/m/Y H:i:s') : '-' }}</p>
                <p><strong>Alasan Pembatalan:</strong> <span class="italic text-rose-900 bg-rose-100/80 px-2 py-0.5 rounded">"{{ $sale->cancel_reason }}"</span></p>
                <p class="text-[11px] text-rose-600 font-medium pt-1">
                    <i class="fa-solid fa-info-circle mr-1"></i> Stok produk telah dikembalikan ke inventori secara otomatis pada saat pembatalan.
                </p>
            </div>
        </div>
    @endif

    <!-- Printable Invoice Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-md p-6 sm:p-10" id="printableReceipt">
        <!-- Receipt Header -->
        <div class="text-center pb-6 border-b border-dashed border-slate-300">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-indigo-600 text-white text-2xl font-black mb-2">
                <i class="fa-solid fa-store"></i>
            </div>
            <h2 class="text-xl font-black text-slate-900 tracking-tight">TokoKu Retail Store</h2>
            <p class="text-xs text-slate-500">Sistem Inventori & Kasir Ritel Modern</p>
            <p class="text-[11px] text-slate-400">Jl. Utama Ritel Indonesia • Telp: (021) 7788-9900</p>
        </div>

        <!-- Invoice Details -->
        <div class="py-5 grid grid-cols-2 gap-4 text-xs border-b border-dashed border-slate-300">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">No. Invoice:</span>
                <span class="font-mono font-extrabold text-slate-900 text-sm">{{ $sale->invoice_number }}</span>

                <span class="text-slate-400 block text-[10px] uppercase font-bold mt-2">Waktu Transaksi:</span>
                <span class="text-slate-700 font-semibold">{{ $sale->sale_date->format('d F Y, H:i') }} WIB</span>
            </div>
            <div class="text-right">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Pelanggan:</span>
                <span class="font-bold text-slate-800">{{ $sale->customer_name }}</span>

                <span class="text-slate-400 block text-[10px] uppercase font-bold mt-2">Kasir / Operator:</span>
                <span class="text-slate-700 font-semibold">{{ $sale->cashier->name ?? ($sale->user->name ?? 'Sistem Checkout') }}</span>
            </div>
        </div>

        <!-- Items Table -->
        <div class="py-6">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-2">Item Barang</th>
                        <th class="py-2 text-right">Harga</th>
                        <th class="py-2 text-center">Qty</th>
                        <th class="py-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($sale->items as $item)
                        <tr>
                            <td class="py-3">
                                <div class="font-bold text-slate-800">{{ $item->product->name ?? 'Produk Dihapus' }}</div>
                                <div class="text-[10px] font-mono text-slate-400">SKU: {{ $item->product->sku ?? '-' }}</div>
                            </td>
                            <td class="py-3 text-right text-slate-600 font-mono">
                                Rp {{ number_format($item->selling_price, 0, ',', '.') }}
                            </td>
                            <td class="py-3 text-center font-bold text-slate-800">
                                {{ $item->quantity }}
                            </td>
                            <td class="py-3 text-right font-bold text-slate-900 font-mono">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals & Payment Summary -->
        <div class="pt-4 border-t border-dashed border-slate-300 space-y-2 text-xs">
            <div class="flex justify-between items-center text-slate-600">
                <span>Total Kuantitas:</span>
                <span class="font-bold text-slate-800">{{ $sale->items->sum('quantity') }} unit</span>
            </div>
            <div class="flex justify-between items-center text-sm font-bold pt-2 border-t border-slate-100">
                <span class="text-slate-900 uppercase">Total Belanja:</span>
                <span class="font-black text-indigo-700 text-lg font-mono">
                    Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                </span>
            </div>
            <div class="flex justify-between items-center text-slate-600">
                <span>Metode Pembayaran:</span>
                <span class="font-bold uppercase text-slate-800">{{ $sale->payment_method }}</span>
            </div>
            <div class="flex justify-between items-center text-slate-600">
                <span>Jumlah Bayar:</span>
                <span class="font-mono text-slate-800">Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center text-slate-600">
                <span>Kembalian:</span>
                <span class="font-mono font-bold text-emerald-700">Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Receipt Footer Note -->
        <div class="mt-8 pt-6 border-t border-dashed border-slate-300 text-center text-[11px] text-slate-400 space-y-1">
            <p>Terima kasih telah berbelanja di TokoKu.</p>
            <p>Barang yang sudah dibeli dapat ditukar jika terdapat cacat produksi sesuai syarat & ketentuan.</p>
        </div>
    </div>

    <!-- Owner Cancellation Modal (No-print) -->
    @can('cancel', $sale)
        <div id="cancelModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                <div class="flex items-center space-x-3 text-rose-600 mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-rose-100 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900">Konfirmasi Pembatalan</h3>
                        <p class="text-xs text-slate-500">Khusus Hak Akses Owner</p>
                    </div>
                </div>

                <div class="p-3.5 bg-rose-50 rounded-xl border border-rose-200 text-xs text-rose-800 mb-4 leading-relaxed">
                    <strong>Peringatan Sistem:</strong> Pembatalan transaksi akan mengembalikan seluruh stok item ke inventori secara otomatis, mencatat audit riwayat stok (cancellation), dan status transaksi tidak dapat diubah kembali.
                </div>

                <form method="POST" action="{{ route('sales.cancel', $sale->id) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="cancel_reason" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                            Alasan Pembatalan <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="cancel_reason" name="cancel_reason" rows="3" required minlength="5"
                            placeholder="Contoh: Pelanggan salah membawa barang / komplain transaksi..."
                            class="w-full p-3 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-rose-500"></textarea>
                    </div>

                    <div class="flex gap-2">
                        <button type="button" onclick="closeCancelModal()" class="flex-1 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                            Tutup
                        </button>
                        <button type="submit" class="flex-1 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-md shadow-rose-600/30 transition">
                            Konfirmasi Batalkan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endcan
</div>

@push('scripts')
<script>
    function openCancelModal() {
        const modal = document.getElementById('cancelModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }
    function closeCancelModal() {
        const modal = document.getElementById('cancelModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }
</script>
@endpush
@endsection
