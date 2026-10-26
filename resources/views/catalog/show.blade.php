@extends('layouts.public')

@section('title', $product->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Breadcrumb -->
    <div class="flex items-center space-x-2 text-xs text-slate-500 mb-6">
        <a href="{{ route('catalog.index') }}" class="hover:text-indigo-600">Katalog</a>
        <span>/</span>
        <span class="text-slate-400">{{ $product->category->name ?? 'Produk' }}</span>
        <span>/</span>
        <span class="text-slate-800 font-semibold">{{ $product->name }}</span>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden grid grid-cols-1 md:grid-cols-2 gap-8 p-6 sm:p-8">
        <!-- Product Image -->
        <div class="h-80 bg-slate-100 rounded-2xl flex items-center justify-center relative overflow-hidden">
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
            @else
                <div class="w-24 h-24 rounded-3xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-5xl">
                    <i class="fa-solid fa-box-open"></i>
                </div>
            @endif
            <span class="absolute top-4 left-4 px-3 py-1 rounded-lg text-xs font-mono font-bold bg-slate-900 text-white">
                SKU: {{ $product->sku }}
            </span>
        </div>

        <!-- Details -->
        <div class="flex flex-col justify-between">
            <div>
                <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider block mb-1">
                    {{ $product->category->name ?? 'Kategori' }}
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    {{ $product->name }}
                </h1>

                <div class="mt-4 flex items-center space-x-3">
                    <span class="text-3xl font-black text-slate-900">
                        Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                        <i class="fa-solid fa-circle-check mr-1"></i> Stok Tersedia: {{ $product->stock }} unit
                    </span>
                </div>

                <div class="mt-6 border-t border-slate-100 pt-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Deskripsi Produk</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        {{ $product->description ?? 'Tidak ada deskripsi detail untuk produk ini.' }}
                    </p>
                </div>
            </div>

            <!-- Add to Cart Form -->
            <form method="POST" action="{{ route('cart.add') }}" class="mt-8 border-t border-slate-100 pt-6">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <div class="flex items-center space-x-4 mb-4">
                    <label class="text-xs font-bold text-slate-700 uppercase">Jumlah:</label>
                    <div class="flex items-center border border-slate-200 rounded-xl overflow-hidden w-32">
                        <button type="button" onclick="decrementQty()" class="w-10 h-10 bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center font-bold">
                            -
                        </button>
                        <input type="number" id="quantity" name="quantity" value="1" min="1" max="{{ $product->stock }}"
                            class="w-12 h-10 text-center text-sm font-bold border-none focus:outline-none">
                        <button type="button" onclick="incrementQty({{ $product->stock }})" class="w-10 h-10 bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center font-bold">
                            +
                        </button>
                    </div>
                    <span class="text-xs text-slate-400">Maks. {{ $product->stock }}</span>
                </div>

                <button type="submit" class="w-full py-3.5 px-6 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-lg shadow-indigo-600/30 transition flex items-center justify-center">
                    <i class="fa-solid fa-cart-plus mr-2 text-base"></i> Masukkan ke Keranjang Belanja
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function incrementQty(max) {
        const input = document.getElementById('quantity');
        let val = parseInt(input.value) || 1;
        if (val < max) input.value = val + 1;
    }
    function decrementQty() {
        const input = document.getElementById('quantity');
        let val = parseInt(input.value) || 1;
        if (val > 1) input.value = val - 1;
    }
</script>
@endsection
