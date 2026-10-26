@extends('layouts.app')

@section('title', 'Kelola Pengguna Sistem')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Kelola Akun Pengguna</h1>
            <p class="text-xs text-slate-500 mt-1">Pengaturan hak akses role (Owner, Staff/Kasir, Pelanggan).</p>
        </div>
        <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white shadow-md transition">
            <i class="fa-solid fa-user-plus mr-2"></i> Tambah Akun Baru
        </a>
    </div>

    <!-- Search & Role Filter -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('users.index') }}" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau telepon..."
                class="flex-1 px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-1 focus:ring-indigo-500">
            <select name="role" class="px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-1 focus:ring-indigo-500">
                <option value="">Semua Role</option>
                <option value="owner" {{ request('role') == 'owner' ? 'selected' : '' }}>Owner</option>
                <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Staff / Kasir</option>
                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>Pelanggan (User)</option>
            </select>
            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition">
                Filter
            </button>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">Nama Lengkap</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4 text-center">Peran (Role)</th>
                        <th class="py-3 px-4">No. Telepon</th>
                        <th class="py-3 px-4">Terdaftar</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-4 font-bold text-slate-900 text-xs">
                                {{ $user->name }}
                                @if($user->id === auth()->id())
                                    <span class="ml-1 text-[10px] text-indigo-600 font-semibold">(Anda)</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-600">{{ $user->email }}</td>
                            <td class="py-3.5 px-4 text-center">
                                @if($user->isOwner())
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                        <i class="fa-solid fa-crown mr-1"></i> OWNER
                                    </span>
                                @elseif($user->isStaff())
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                        <i class="fa-solid fa-user-tie mr-1"></i> STAFF / KASIR
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        <i class="fa-solid fa-user mr-1"></i> PELANGGAN
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-mono text-xs text-slate-600">{{ $user->phone ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-xs text-slate-500">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center space-x-1">
                                    <a href="{{ route('users.edit', $user->id) }}" class="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-slate-100 rounded-lg transition" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('users.destroy', $user->id) }}" class="inline" onsubmit="return confirm('Hapus akun ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-slate-100 rounded-lg transition" title="Hapus">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-400 text-xs">Tidak ada data pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
