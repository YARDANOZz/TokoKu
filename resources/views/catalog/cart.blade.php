@extends('layouts.public')

@section('title', 'Keranjang Belanja')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Keranjang Belanja</h1>
            <p class="text-xs text-slate-500 mt-1">Periksa kembali barang belanjaan Anda sebelum checkout.</p>
        </div>
        <a href="{{ route('catalog.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
            <i class="fa-solid fa-arrow-left mr-1"></i> Lanjut Belanja
        </a>
    </div>

    @if(count($items) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Items Table (2 cols) -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3">Produk</th>
                                <th class="py-3">Harga</th>
                                <th class="py-3 text-center">Jumlah</th>
                                <th class="py-3 text-right">Subtotal</th>
                                <th class="py-3 text-center">Hapus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($items as $item)
                                <tr>
                                    <td class="py-4">
                                        <div class="font-bold text-slate-900">{{ $item['product']->name }}</div>
                                        <div class="text-[11px] font-mono text-slate-400">SKU: {{ $item['product']->sku }}</div>
                                        <div class="text-[11px] text-emerald-600">Sisa stok: {{ $item['product']->stock }} unit</div>
                                    </td>
                                    <td class="py-4 text-xs font-semibold text-slate-700">
                                        Rp {{ number_format($item['product']->selling_price, 0, ',', '.') }}
                                    </td>
                                    <td class="py-4 text-center">
                                        <form method="POST" action="{{ route('cart.update') }}" class="inline-flex items-center space-x-1">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $item['product']->id }}">
                                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="{{ $item['product']->stock }}"
                                                onchange="this.form.submit()"
                                                class="w-16 py-1 px-2 text-center text-xs font-bold rounded-lg border border-slate-200 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        </form>
                                    </td>
                                    <td class="py-4 text-right font-bold text-slate-900 text-sm">
                                        Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                    </td>
                                    <td class="py-4 text-center">
                                        <form method="POST" action="{{ route('cart.remove', $item['product']->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-slate-400 hover:text-rose-600 transition" title="Hapus item">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Checkout Box (1 col) -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 h-fit space-y-4">
                <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Ringkasan Pesanan</h3>

                <div class="space-y-2 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Total Jumlah Item</span>
                        <span class="font-bold text-slate-800">{{ array_sum(array_column($items, 'quantity')) }} pcs</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Pajak (PPN)</span>
                        <span class="font-bold text-emerald-600">Termasuk</span>
                    </div>
                    <div class="border-t border-slate-100 pt-3 flex justify-between items-center text-base">
                        <span class="font-black text-slate-900">Total Belanja</span>
                        <span class="font-black text-indigo-600 text-xl">
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- Checkout Form -->
                <form method="POST" action="{{ route('checkout.process') }}" class="pt-4 border-t border-slate-100 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Metode Bayar</label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="flex flex-col items-center p-2.5 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50">
                                <input type="radio" name="payment_method" value="cash" checked class="hidden">
                                <i class="fa-solid fa-money-bill-wave text-indigo-600 mb-1"></i>
                                <span class="text-[10px] font-bold text-slate-800">Tunai</span>
                            </label>
                            <label class="flex flex-col items-center p-2.5 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50">
                                <input type="radio" name="payment_method" value="transfer" class="hidden">
                                <i class="fa-solid fa-building-columns text-indigo-600 mb-1"></i>
                                <span class="text-[10px] font-bold text-slate-800">Transfer</span>
                            </label>
                            <label class="flex flex-col items-center p-2.5 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50">
                                <input type="radio" name="payment_method" value="qris" class="hidden">
                                <i class="fa-solid fa-qrcode text-indigo-600 mb-1"></i>
                                <span class="text-[10px] font-bold text-slate-800">QRIS</span>
                            </label>
                        </div>
                    </div>

                    @auth
                        <div class="p-3 bg-slate-50 rounded-xl text-xs text-slate-600">
                            <span class="font-bold text-slate-800">Pelanggan:</span> {{ auth()->user()->name }} ({{ auth()->user()->email }})
                        </div>
                        <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-lg shadow-emerald-600/30 transition flex items-center justify-center">
                            <i class="fa-solid fa-bag-shopping mr-2"></i> Konfirmasi & Checkout
                        </button>
                    @else
                        <div class="p-3 bg-amber-50 rounded-xl text-xs text-amber-800 border border-amber-200">
                            <i class="fa-solid fa-circle-exclamation mr-1"></i> Silakan masuk untuk menyelesaikan pesanan Anda.
                        </div>
                        <a href="{{ route('login') }}" class="w-full py-3.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-lg shadow-indigo-600/30 transition flex items-center justify-center">
                            <i class="fa-solid fa-right-to-bracket mr-2"></i> Masuk untuk Checkout
                        </a>
                    @endauth
                </form>
            </div>
        </div>
    @else
        <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center shadow-sm">
            <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center text-indigo-600 text-3xl mx-auto mb-4">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
            <h2 class="text-xl font-bold text-slate-800">Keranjang Belanja Anda Kosong</h2>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                Temukan berbagai barang ritel berkualitas dengan stok yang selalu sinkron dan akurat.
            </p>
            <div class="mt-6">
                <a href="{{ route('catalog.index') }}" class="inline-flex items-center px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md transition">
                    <i class="fa-solid fa-bag-shopping mr-2"></i> Mulai Belanja Sekarang
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
