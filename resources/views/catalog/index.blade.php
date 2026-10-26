@extends('layouts.public')

@section('title', 'Katalog Produk Ritel')

@section('content')
<div class="space-y-8">

    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-indigo-900 via-indigo-800 to-slate-900 rounded-3xl p-8 sm:p-10 text-white shadow-xl relative overflow-hidden">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-indigo-500/30 text-indigo-200 border border-indigo-400/30 mb-3">
                <i class="fa-solid fa-sparkles mr-1.5 text-amber-300"></i> Stok Sinkron & Otomatis
            </span>
            <h1 class="text-3xl sm:text-4xl font-black tracking-tight leading-tight">
                Katalog Produk Ritel TokoKu
            </h1>
            <p class="text-slate-300 text-sm sm:text-base mt-2 leading-relaxed">
                Belanja kebutuhan harian, elektronik, pakaian, dan alat tulis dengan jaminan stok nyata dan harga terbaik.
            </p>
        </div>
        <div class="absolute -right-10 -bottom-10 opacity-10 text-9xl text-white pointer-events-none hidden md:block">
            <i class="fa-solid fa-cart-shopping"></i>
        </div>
    </div>

    <!-- Search & Filters Bar -->
    <div class="bg-white rounded-2xl p-4 sm:p-6 border border-slate-200 shadow-sm flex flex-col md:flex-row gap-4 justify-between items-center">
        <!-- Search form -->
        <form method="GET" action="{{ route('catalog.index') }}" class="w-full md:w-96 flex">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama barang atau SKU..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-l-xl border border-r-0 border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-r-xl text-sm font-bold transition">
                Cari
            </button>
        </form>

        <!-- Category Pills -->
        <div class="w-full md:w-auto flex items-center gap-2 overflow-x-auto pb-2 md:pb-0">
            <a href="{{ route('catalog.index', ['search' => request('search')]) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ !request('category') ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua Kategori
            </a>
            @foreach($categories as $category)
                <a href="{{ route('catalog.index', ['category' => $category->id, 'search' => request('search')]) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ request('category') == $category->id ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    {{ $category->name }} ({{ $category->products_count }})
                </a>
            @endforeach
        </div>
    </div>

    <!-- Active Filters Badge -->
    @if(request('search') || request('category'))
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <span>Filter Aktif:</span>
            @if(request('search'))
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-semibold">
                    Kata kunci: "{{ request('search') }}"
                </span>
            @endif
            @if(request('category'))
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-semibold">
                    Kategori ID: {{ request('category') }}
                </span>
            @endif
            <a href="{{ route('catalog.index') }}" class="text-rose-600 hover:underline font-bold ml-2">
                <i class="fa-solid fa-xmark"></i> Reset Filter
            </a>
        </div>
    @endif

    <!-- Product Grid (Only stock > 0) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($products as $product)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-lg transition-all duration-200 flex flex-col overflow-hidden group">
                <!-- Product Image / Icon placeholder -->
                <div class="h-44 bg-slate-100 flex items-center justify-center relative overflow-hidden">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    @else
                        <div class="w-16 h-16 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-3xl group-hover:scale-110 transition duration-200">
                            <i class="fa-solid fa-box-open"></i>
                        </div>
                    @endif

                    <!-- Stock Counter Badge -->
                    <span class="absolute top-3 right-3 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-emerald-500 text-white shadow-sm">
                        Stok: {{ $product->stock }}
                    </span>
                    <span class="absolute top-3 left-3 px-2.5 py-0.5 rounded-md text-[10px] font-mono font-bold bg-slate-900/80 text-white">
                        {{ $product->sku }}
                    </span>
                </div>

                <!-- Product Info -->
                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider block mb-1">
                            {{ $product->category->name ?? 'Umum' }}
                        </span>
                        <h3 class="font-bold text-slate-900 text-base line-clamp-2 group-hover:text-indigo-600 transition">
                            <a href="{{ route('catalog.show', $product->id) }}">{{ $product->name }}</a>
                        </h3>
                        <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                            {{ $product->description ?? 'Deskripsi produk tidak tersedia.' }}
                        </p>
                    </div>

                    <!-- Price & Action -->
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 block font-semibold">Harga Ritel</span>
                            <span class="text-lg font-black text-slate-900">
                                Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                            </span>
                        </div>

                        <!-- Add to Cart Form -->
                        <form method="POST" action="{{ route('cart.add') }}">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="w-10 h-10 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white flex items-center justify-center shadow-md shadow-indigo-600/20 transition active:scale-95" title="Tambah ke Keranjang">
                                <i class="fa-solid fa-cart-plus"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 text-2xl mx-auto mb-3">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <h4 class="text-base font-bold text-slate-800">Tidak ada produk yang tersedia</h4>
                <p class="text-xs text-slate-500 mt-1">Produk mungkin sedang habis stok atau tidak cocok dengan filter pencarian.</p>
                <div class="mt-4">
                    <a href="{{ route('catalog.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 text-white">
                        Lihat Semua Produk
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $products->links() }}
    </div>
</div>
@endsection
