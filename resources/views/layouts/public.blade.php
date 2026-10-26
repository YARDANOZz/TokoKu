<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Katalog TokoKu') - TokoKu Ritel</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">

    <!-- Navigation Header -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Logo -->
                <a href="{{ route('catalog.index') }}" class="flex items-center space-x-3 group">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-indigo-500/20 group-hover:bg-indigo-500 transition">
                        <i class="fa-solid fa-store"></i>
                    </div>
                    <div>
                        <span class="text-xl font-extrabold text-slate-900 tracking-tight">Toko<span class="text-indigo-600">Ku</span></span>
                        <span class="block text-[10px] text-slate-400 uppercase tracking-wider font-semibold">Katalog Ritel & Belanja</span>
                    </div>
                </a>

                <!-- Nav Links & Cart -->
                <div class="flex items-center space-x-3 sm:space-x-5">
                    <a href="{{ route('catalog.index') }}" class="text-sm font-semibold text-slate-700 hover:text-indigo-600 transition">
                        Katalog
                    </a>

                    <!-- Cart button with counter -->
                    @php $cartCount = count(session('cart', [])); @endphp
                    <a href="{{ route('cart.index') }}" class="relative inline-flex items-center px-3 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-semibold transition">
                        <i class="fa-solid fa-cart-shopping mr-2 text-base"></i>
                        <span>Keranjang</span>
                        @if($cartCount > 0)
                            <span class="ml-1.5 px-2 py-0.5 text-xs font-extrabold bg-indigo-600 text-white rounded-full">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Auth button -->
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold bg-slate-900 hover:bg-slate-800 text-white shadow-sm transition">
                            <i class="fa-solid fa-gauge-high mr-2"></i> Dashboard ({{ strtoupper(auth()->user()->role) }})
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold bg-slate-900 hover:bg-slate-800 text-white shadow-sm transition">
                            <i class="fa-solid fa-right-to-bracket mr-2"></i> Masuk
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Body -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @include('partials.alerts')
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-slate-500">
            <div class="flex items-center space-x-2">
                <span class="font-bold text-slate-800">TokoKu</span>
                <span>• Ritel & Manajemen Inventori Laravel 10</span>
            </div>
            <div>
                UTS Web Programming 2 — Sistem Inventori Terintegrasi
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
