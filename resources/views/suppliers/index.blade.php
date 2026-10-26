@extends('layouts.app')

@section('title', 'Data Pemasok (Suppliers)')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Data Pemasok (Suppliers)</h1>
            <p class="text-xs text-slate-500 mt-1">Daftar mitra pemasok dan distributor untuk pengadaan stok ritel.</p>
        </div>

        @if(auth()->user()->isOwner())
            <a href="{{ route('suppliers.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white shadow-md transition">
                <i class="fa-solid fa-plus mr-2"></i> Tambah Pemasok
            </a>
        @endif
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm max-w-md">
        <form method="GET" action="{{ route('suppliers.index') }}" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau no. telepon..."
                class="flex-1 px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-1 focus:ring-indigo-500">
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition">
                Cari
            </button>
        </form>
    </div>

    <!-- Suppliers Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">Nama Pemasok</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4">Telepon / WhatsApp</th>
                        <th class="py-3 px-4">Alamat Gudang / Kantor</th>
                        <th class="py-3 px-4 text-center">Riwayat Stok Masuk</th>
                        @if(auth()->user()->isOwner())
                            <th class="py-3 px-4 text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($suppliers as $supplier)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-4 font-bold text-slate-900 text-xs">
                                {{ $supplier->name }}
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-600">
                                {{ $supplier->email ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 font-mono text-xs text-slate-700">
                                {{ $supplier->phone ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-500">
                                {{ $supplier->address ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-sky-50 text-sky-700">
                                    {{ $supplier->purchases_count }} kali
                                </span>
                            </td>
                            @if(auth()->user()->isOwner())
                                <td class="py-3.5 px-4 text-center">
                                    <div class="inline-flex items-center space-x-1">
                                        <a href="{{ route('suppliers.edit', $supplier->id) }}" class="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-slate-100 rounded-lg transition" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form method="POST" action="{{ route('suppliers.destroy', $supplier->id) }}" class="inline" onsubmit="return confirm('Hapus pemasok ini?')">
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
                            <td colspan="6" class="py-6 text-center text-slate-400 text-xs">Belum ada data pemasok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $suppliers->links() }}
        </div>
    </div>
</div>
@endsection
