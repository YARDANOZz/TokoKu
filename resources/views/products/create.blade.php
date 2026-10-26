@extends('layouts.app')

@section('title', 'Tambah Produk Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Tambah Produk Baru</h1>
            <p class="text-xs text-slate-500 mt-1">Daftarkan data master produk baru ke katalog TokoKu.</p>
        </div>
        <a href="{{ route('products.index') }}" class="text-xs font-bold text-slate-600 hover:text-indigo-600">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <!-- Informational Note about Dynamic Stock -->
    <div class="p-4 bg-indigo-50 border border-indigo-200 rounded-2xl flex items-start space-x-3 text-xs text-indigo-900">
        <i class="fa-solid fa-shield-halved text-indigo-600 text-lg mt-0.5"></i>
        <div>
            <strong class="font-bold">Ketentuan Integritas Stok Ritel:</strong>
            <p class="text-indigo-800 mt-0.5 leading-relaxed">
                Stok awal produk baru otomatis diinisialisasi sebesar <strong>0</strong>. Sesuai aturan sistem, stok tidak dapat diedit secara manual dan hanya berubah melalui transaksi <strong>Stok Masuk (Pembelian)</strong>, <strong>Penjualan Kasir</strong>, atau <strong>Pembatalan</strong>.
            </p>
        </div>
    </div>

    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-5">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="sku" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    SKU Produk (Unik) <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="sku" id="sku" value="{{ old('sku') }}" required placeholder="Contoh: ELK-009"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 font-mono text-xs focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="category_id" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    Kategori <span class="text-rose-500">*</span>
                </label>
                <select name="category_id" id="category_id" required
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label for="name" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                Nama Produk <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Kabel Fast Charging 65W Type-C"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label for="description" class="block text-xs font-bold text-slate-700 uppercase mb-1">Deskripsi Produk</label>
            <textarea name="description" id="description" rows="3" placeholder="Spesifikasi atau informasi produk..."
                class="w-full px-4 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-indigo-500">{{ old('description') }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label for="purchase_price" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    Harga Beli / HPP (Rp) <span class="text-rose-500">*</span>
                </label>
                <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', 0) }}" min="0" required
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 font-mono text-xs focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="selling_price" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    Harga Jual Ritel (Rp) <span class="text-rose-500">*</span>
                </label>
                <input type="number" name="selling_price" id="selling_price" value="{{ old('selling_price', 0) }}" min="0" required
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 font-mono text-xs focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="min_stock" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    Batas Minimum Stok <span class="text-rose-500">*</span>
                </label>
                <input type="number" name="min_stock" id="min_stock" value="{{ old('min_stock', 5) }}" min="0" required
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div>
            <label for="image" class="block text-xs font-bold text-slate-700 uppercase mb-1">Foto / Gambar Produk (Opsional)</label>
            <input type="file" name="image" id="image" accept="image/*"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('products.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/30 transition">
                <i class="fa-solid fa-floppy-disk mr-1.5"></i> Simpan Produk
            </button>
        </div>
    </form>
</div>
@endsection
