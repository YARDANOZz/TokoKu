@extends('layouts.app')

@section('title', 'Dashboard Owner & Forecasting')

@section('content')
<div class="space-y-6">

    <!-- Top Welcome Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-2xl p-6 text-white shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                    <i class="fa-solid fa-crown mr-1"></i> Owner View
                </span>
                <span class="text-xs text-indigo-200">Performa Toko Real-Time</span>
            </div>
            <h2 class="text-2xl font-black mt-2 tracking-tight">Selamat Datang, {{ auth()->user()->name }}</h2>
            <p class="text-sm text-slate-300 mt-0.5">Semua metrik dan grafik dihitung langsung dari query database dengan auto-polling.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('pos.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold bg-emerald-500 hover:bg-emerald-600 text-white shadow-lg shadow-emerald-500/30 transition">
                <i class="fa-solid fa-cash-register mr-2"></i> Kasir POS
            </a>
            <a href="{{ route('purchases.create') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white shadow-lg shadow-indigo-600/30 transition">
                <i class="fa-solid fa-boxes-packing mr-2"></i> Stok Masuk
            </a>
        </div>
    </div>

    <!-- Stat Cards (Omzet, Profit, Transaksi, Stok Menipis) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Card 1: Omzet -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Omzet</span>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
            </div>
            <div id="stat-omzet" class="text-2xl font-black text-slate-900 tracking-tight">
                Rp {{ number_format($totalOmzet, 0, ',', '.') }}
            </div>
            <p class="text-xs text-slate-500 mt-2 flex items-center">
                <span class="text-emerald-600 font-semibold mr-1"><i class="fa-solid fa-circle-check"></i> Dari transaksi</span> status selesai
            </p>
        </div>

        <!-- Card 2: Laba Kotor -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Keuntungan Kotor</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-arrow-trend-up"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-emerald-600 tracking-tight">
                Rp {{ number_format($grossProfit, 0, ',', '.') }}
            </div>
            <p class="text-xs text-slate-500 mt-2">
                Omzet dikurangi HPP produk
            </p>
        </div>

        <!-- Card 3: Transaksi Selesai & Dibatalkan -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Transaksi</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-receipt"></i>
                </div>
            </div>
            <div id="stat-transactions" class="text-2xl font-black text-slate-900 tracking-tight">
                {{ number_format($totalCompletedSales) }} <span class="text-sm font-semibold text-slate-500">Sukses</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">
                <span class="text-rose-500 font-bold">{{ $totalCancelledSales }} transaksi</span> dibatalkan
            </p>
        </div>

        <!-- Card 4: Stok Rendah / Habis -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Stok Menipis</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div id="stat-low-stock" class="text-2xl font-black {{ $lowStockProducts->count() > 0 ? 'text-amber-600' : 'text-slate-900' }} tracking-tight">
                {{ $lowStockProducts->count() }} <span class="text-sm font-semibold text-slate-500">Produk</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">
                Perlu segera restok ke pemasok
            </p>
        </div>
    </div>

    <!-- Charts Row (Auto Polling via Chart.js) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sales Trend Chart (2 columns) -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Tren Penjualan & Margin (7 Hari Terakhir)</h3>
                    <p class="text-xs text-slate-500">Diperbarui otomatis tiap 10 detik tanpa reload halaman</p>
                </div>
                <div class="flex items-center space-x-2 text-xs text-slate-500">
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Live Polling</span>
                </div>
            </div>
            <div class="h-72">
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>

        <!-- Category Distribution (1 column) -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
            <h3 class="text-base font-bold text-slate-900 mb-1">Distribusi Penjualan Kategori</h3>
            <p class="text-xs text-slate-500 mb-4">Proporsi kuantitas produk terjual per kategori</p>
            <div class="h-64 flex items-center justify-center">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Forecasting & Top Selling Products -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Forecasting Widget (Simple Moving Average) -->
        <div class="bg-gradient-to-br from-indigo-900 to-slate-900 text-white rounded-2xl p-6 shadow-md">
            <div class="flex items-center justify-between mb-4">
                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-indigo-500/30 text-indigo-200 border border-indigo-400/30 uppercase tracking-wider">
                    <i class="fa-solid fa-chart-line mr-1"></i> Forecasting Penjualan
                </span>
                <span class="text-xs text-slate-400">Model SMA-7</span>
            </div>
            <h3 class="text-lg font-extrabold text-white">Prediksi Omzet Harian</h3>
            <p class="text-xs text-slate-300 mt-1 mb-4 leading-relaxed">
                Dihitung dari rata-rata bergerak (Simple Moving Average) histori transaksi penjualan 7 hari terakhir.
            </p>
            <div class="p-4 bg-slate-800/80 rounded-xl border border-indigo-500/20 mb-4">
                <div class="text-xs text-slate-400">Estimasi Omzet Hari Berikutnya:</div>
                <div class="text-2xl font-black text-amber-300 mt-1">
                    Rp {{ number_format($movingAverageDailyForecast, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-400 mt-1">
                    Proyeksi kebutuhan stok stabil berdasarkan tren saat ini.
                </div>
            </div>
            <div class="text-xs text-indigo-200 flex items-center">
                <i class="fa-solid fa-circle-info mr-2"></i> Membantu pengambilan keputusan pembelian ke pemasok.
            </div>
        </div>

        <!-- Top Selling Products -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
            <h3 class="text-base font-bold text-slate-900 mb-1">Top Produk Terlaris</h3>
            <p class="text-xs text-slate-500 mb-4">Produk dengan perputaran penjualan tertinggi</p>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-2.5">Produk</th>
                            <th class="py-2.5">SKU</th>
                            <th class="py-2.5 text-center">Terjual</th>
                            <th class="py-2.5 text-right">Total Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($topProducts as $top)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 font-semibold text-slate-800">{{ $top->name }}</td>
                                <td class="py-3 text-xs font-mono text-slate-500">{{ $top->sku }}</td>
                                <td class="py-3 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700">
                                        {{ $top->total_sold }} pcs
                                    </span>
                                </td>
                                <td class="py-3 text-right font-bold text-slate-900">
                                    Rp {{ number_format($top->total_revenue, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-slate-400 text-xs">Belum ada data transaksi</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert List & Recent Transactions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Low Stock Warning Table -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    <h3 class="text-base font-bold text-slate-900">Peringatan Stok Menipis</h3>
                </div>
                <a href="{{ route('purchases.create') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                    + Restok Pemasok
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs font-bold text-slate-500 uppercase">
                            <th class="py-2">Produk</th>
                            <th class="py-2 text-center">Sisa Stok</th>
                            <th class="py-2 text-center">Batas Min</th>
                            <th class="py-2 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($lowStockProducts as $lp)
                            <tr>
                                <td class="py-2.5">
                                    <div class="font-semibold text-slate-800 text-xs">{{ $lp->name }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $lp->category->name ?? '-' }}</div>
                                </td>
                                <td class="py-2.5 text-center font-bold {{ $lp->stock <= 0 ? 'text-rose-600' : 'text-amber-600' }}">
                                    {{ $lp->stock }}
                                </td>
                                <td class="py-2.5 text-center text-slate-500 text-xs">{{ $lp->min_stock }}</td>
                                <td class="py-2.5 text-right">
                                    @if($lp->stock <= 0)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700">Habis</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">Rendah</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-emerald-600 text-xs font-medium">
                                    <i class="fa-solid fa-check-circle mr-1"></i> Semua stok produk dalam batas aman.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Sales Table -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900">Transaksi Terbaru</h3>
                <a href="{{ route('sales.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                    Lihat Semua <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs font-bold text-slate-500 uppercase">
                            <th class="py-2">Invoice</th>
                            <th class="py-2">Pelanggan</th>
                            <th class="py-2 text-right">Total</th>
                            <th class="py-2 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentSales as $sale)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-2.5">
                                    <a href="{{ route('sales.show', $sale->id) }}" class="font-bold text-indigo-600 hover:underline text-xs">
                                        {{ $sale->invoice_number }}
                                    </a>
                                    <div class="text-[10px] text-slate-400">{{ $sale->sale_date->format('d/m/Y H:i') }}</div>
                                </td>
                                <td class="py-2.5 text-xs text-slate-700">{{ $sale->customer_name }}</td>
                                <td class="py-2.5 text-right font-bold text-xs text-slate-900">
                                    Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="py-2.5 text-center">
                                    @if($sale->status === 'completed')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Selesai</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">Dibatalkan</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-slate-400 text-xs">Belum ada transaksi</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let trendChart = null;
    let categoryChart = null;

    function initCharts() {
        const trendCtx = document.getElementById('salesTrendChart').getContext('2d');
        trendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Penjualan (Omzet Rp)',
                        data: [],
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.08)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointRadius: 4,
                        pointBackgroundColor: '#4f46e5',
                    },
                    {
                        label: 'Margin Laba (Rp)',
                        data: [],
                        borderColor: '#10b981',
                        backgroundColor: 'transparent',
                        borderDash: [5, 5],
                        tension: 0.35,
                        borderWidth: 2,
                        pointRadius: 3,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { boxWidth: 12, font: { size: 11 } } },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(val) {
                                return 'Rp ' + (val >= 1000 ? (val/1000) + 'k' : val);
                            },
                            font: { size: 10 }
                        }
                    },
                    x: { ticks: { font: { size: 10 } } }
                }
            }
        });

        const catCtx = document.getElementById('categoryChart').getContext('2d');
        categoryChart = new Chart(catCtx, {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: [
                        '#4f46e5', '#10b981', '#f59e0b', '#ec4899', '#06b6d4', '#8b5cf6'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } }
                },
                cutout: '65%'
            }
        });
    }

    // Dynamic Polling from JSON Endpoint
    function fetchLiveChartData() {
        fetch("{{ route('dashboard.charts') }}")
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    // Update Trend Chart
                    trendChart.data.labels = data.sales_trend.labels;
                    trendChart.data.datasets[0].data = data.sales_trend.sales;
                    trendChart.data.datasets[1].data = data.sales_trend.profits;
                    trendChart.update();

                    // Update Category Chart
                    categoryChart.data.labels = data.category_distribution.labels;
                    categoryChart.data.datasets[0].data = data.category_distribution.data;
                    categoryChart.update();

                    // Update live metric text numbers
                    if (data.live_metrics) {
                        const omzetEl = document.getElementById('stat-omzet');
                        if (omzetEl) omzetEl.innerText = data.live_metrics.omzet_formatted;

                        const transEl = document.getElementById('stat-transactions');
                        if (transEl) transEl.innerHTML = data.live_metrics.transactions + ' <span class="text-sm font-semibold text-slate-500">Sukses</span>';

                        const lowEl = document.getElementById('stat-low-stock');
                        if (lowEl) lowEl.innerHTML = data.live_metrics.low_stock_count + ' <span class="text-sm font-semibold text-slate-500">Produk</span>';
                    }
                }
            })
            .catch(err => console.error('Error fetching live chart polling:', err));
    }

    document.addEventListener('DOMContentLoaded', function() {
        initCharts();
        fetchLiveChartData();

        // Polling interval: every 10 seconds
        setInterval(fetchLiveChartData, 10000);
    });
</script>
@endpush
