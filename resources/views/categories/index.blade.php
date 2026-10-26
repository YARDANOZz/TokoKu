@extends('layouts.app')

@section('title', 'Kategori Produk')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Kategori Produk</h1>
            <p class="text-xs text-slate-500 mt-1">Klasifikasi kategori untuk memudahkan pengelolaan dan pencarian barang.</p>
        </div>

        @if(auth()->user()->isOwner())
            <a href="{{ route('categories.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white shadow-md transition">
                <i class="fa-solid fa-plus mr-2"></i> Tambah Kategori
            </a>
        @endif
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm max-w-md">
        <form method="GET" action="{{ route('categories.index') }}" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kategori..."
                class="flex-1 px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-1 focus:ring-indigo-500">
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition">
                Cari
            </button>
        </form>
    </div>

    <!-- Categories Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">Nama Kategori</th>
                        <th class="py-3 px-4">Slug URL</th>
                        <th class="py-3 px-4">Deskripsi</th>
                        <th class="py-3 px-4 text-center">Jumlah Produk</th>
                        @if(auth()->user()->isOwner())
                            <th class="py-3 px-4 text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $category)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-4 font-bold text-slate-900 text-xs">
                                {{ $category->name }}
                            </td>
                            <td class="py-3.5 px-4 font-mono text-xs text-slate-500">
                                {{ $category->slug }}
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-600">
                                {{ $category->description ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700">
                                    {{ $category->products_count }} produk
                                </span>
                            </td>
                            @if(auth()->user()->isOwner())
                                <td class="py-3.5 px-4 text-center">
                                    <div class="inline-flex items-center space-x-1">
                                        <a href="{{ route('categories.edit', $category->id) }}" class="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-slate-100 rounded-lg transition" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form method="POST" action="{{ route('categories.destroy', $category->id) }}" class="inline" onsubmit="return confirm('Hapus kategori ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-slate-100 rounded-lg transition" title="Hapus">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-400 text-xs">Belum ada kategori terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $categories->links() }}
        </div>
    </div>
</div>
@endsection
