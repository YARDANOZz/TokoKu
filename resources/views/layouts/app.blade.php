<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - TokoKu Ritel & Inventori</title>

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
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
        }

        /* Custom scrollbars */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        @media print {
            aside, header, .no-print {
                display: none !important;
            }
            main {
                margin: 0 !important;
                padding: 0 !important;
            }
            .print-only {
                display: block !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">

    <!-- Sidebar Component -->
    @include('partials.sidebar')

    <!-- Main Content Wrapper -->
    <div class="md:pl-64 flex flex-col flex-1 min-h-screen">
        <!-- Top Navbar -->
        @include('partials.navbar')

        <!-- Page Main Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
            <!-- Flash Message Alerts -->
            @include('partials.alerts')

            <!-- Yield Page Content -->
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="mt-auto py-4 px-6 border-t border-slate-200 bg-white text-center text-xs text-slate-500 no-print">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
                <span>&copy; {{ date('Y') }} <strong>TokoKu</strong> - UTS Web Programming 2 (Laravel 10 & PHP 8.1).</span>
                <span class="text-slate-400">Sistem Inventori, Multi-Barang, & Kasir Ritel</span>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
