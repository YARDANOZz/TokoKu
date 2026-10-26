@extends('layouts.public')

@section('title', 'Login Pengguna')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-6">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-slate-100 p-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center text-white text-2xl mx-auto shadow-lg shadow-indigo-500/30 mb-4">
                <i class="fa-solid fa-lock"></i>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Masuk ke TokoKu</h2>
            <p class="text-sm text-slate-500 mt-1">Sistem Inventori, Penjualan, & Kasir Ritel</p>
        </div>

        <!-- Form Login -->
        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-regular fa-envelope"></i>
                    </div>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition"
                        placeholder="nama@email.com">
                </div>
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <input type="password" id="password" name="password" required
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition"
                        placeholder="••••••••">
                </div>
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center text-slate-600 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500 mr-2">
                    Ingat saya di perangkat ini
                </label>
            </div>

            <button type="submit" class="w-full py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-lg shadow-indigo-600/30 transition duration-150 flex items-center justify-center">
                <i class="fa-solid fa-arrow-right-to-bracket mr-2"></i> Masuk Sekarang
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-slate-500">
            Belum punya akun pelanggan?
            <a href="{{ route('register') }}" class="font-bold text-indigo-600 hover:text-indigo-800">Daftar Akun Baru</a>
        </div>
    </div>
</div>

<script>
    function fillLogin(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }
</script>
@endsection
