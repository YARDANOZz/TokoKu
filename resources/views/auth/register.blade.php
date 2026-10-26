@extends('layouts.public')

@section('title', 'Daftar Akun Baru')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-6">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-slate-100 p-8">
        <div class="text-center mb-6">
            <div class="w-14 h-14 bg-emerald-600 rounded-2xl flex items-center justify-center text-white text-2xl mx-auto shadow-lg shadow-emerald-500/30 mb-4">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Daftar Akun Pelanggan</h2>
            <p class="text-sm text-slate-500 mt-1">Belanja produk ritel lebih mudah dan hemat</p>
        </div>

        <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm"
                    placeholder="Nama Anda">
            </div>

            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm"
                    placeholder="nama@email.com">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">No. WhatsApp</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm"
                        placeholder="0812xxxx">
                </div>
                <div>
                    <label for="address" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kota / Alamat</label>
                    <input type="text" id="address" name="address" value="{{ old('address') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm"
                        placeholder="Jakarta">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Password</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm"
                        placeholder="Min. 6 karakter">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Konfirmasi</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm"
                        placeholder="Ulangi password">
                </div>
            </div>

            <button type="submit" class="w-full py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-lg shadow-indigo-600/30 transition duration-150 flex items-center justify-center mt-2">
                <i class="fa-solid fa-user-check mr-2"></i> Buat Akun Baru
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-slate-500">
            Sudah memiliki akun?
            <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-800">Masuk di Sini</a>
        </div>
    </div>
</div>
@endsection
