@extends('layouts.app')

@section('title', 'Edit Pengguna: ' . $user->name)

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Edit Akun Pengguna</h1>
            <p class="text-xs text-slate-500 mt-1">Perbarui profil, role, atau ubah password.</p>
        </div>
        <a href="{{ route('users.index') }}" class="text-xs font-bold text-slate-600 hover:text-indigo-600">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('users.update', $user->id) }}" class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                Nama Lengkap <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label for="email" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                Email Pengguna <span class="text-rose-500">*</span>
            </label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-indigo-500">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    Ganti Password (Opsional)
                </label>
                <input type="password" name="password" id="password" placeholder="Kosongkan jika tidak diubah"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="role" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    Peran (Role) <span class="text-rose-500">*</span>
                </label>
                <select name="role" id="role" required
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-2 focus:ring-indigo-500">
                    <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>Staff / Kasir</option>
                    <option value="owner" {{ old('role', $user->role) == 'owner' ? 'selected' : '' }}>Owner (Pemilik)</option>
                    <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>Pelanggan (User)</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="phone" class="block text-xs font-bold text-slate-700 uppercase mb-1">No. WhatsApp</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="address" class="block text-xs font-bold text-slate-700 uppercase mb-1">Alamat Singkat</label>
                <input type="text" name="address" id="address" value="{{ old('address', $user->address) }}"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
            <a href="{{ route('users.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/30 transition">
                Perbarui Akun
            </button>
        </div>
    </form>
</div>
@endsection
