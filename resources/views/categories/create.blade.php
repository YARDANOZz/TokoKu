@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Tambah Kategori Baru</h1>
            <p class="text-xs text-slate-500 mt-1">Buat nama kategori baru untuk mengelompokkan produk.</p>
        </div>
        <a href="{{ route('categories.index') }}" class="text-xs font-bold text-slate-600 hover:text-indigo-600">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('categories.store') }}" class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                Nama Kategori (Unik) <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Perlengkapan Olahraga"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label for="description" class="block text-xs font-bold text-slate-700 uppercase mb-1">Deskripsi Kategori</label>
            <textarea name="description" id="description" rows="3" placeholder="Keterangan singkat..."
                class="w-full px-4 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-indigo-500">{{ old('description') }}</textarea>
        </div>

        <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
            <a href="{{ route('categories.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/30 transition">
                Simpan Kategori
            </button>
        </div>
    </form>
</div>
@endsection
