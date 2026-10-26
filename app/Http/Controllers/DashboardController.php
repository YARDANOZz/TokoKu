<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isOwner()) {
            return $this->ownerDashboard();
        } elseif ($user->isStaff()) {
            return $this->staffDashboard();
        } else {
            return $this->userDashboard();
        }
    }

    protected function ownerDashboard()
    {
        // 1. Metric Aggregates (Dynamic database queries)
        $totalOmzet = Sale::where('status', 'completed')->sum('total_amount');

        // Gross profit = sum((selling_price - purchase_price) * quantity) on completed sales
        $grossProfit = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->sum(DB::raw('(sale_items.selling_price - sale_items.purchase_price) * sale_items.quantity'));

        $totalCompletedSales = Sale::where('status', 'completed')->count();
        $totalCancelledSales = Sale::where('status', 'cancelled')->count();
        $totalPurchasesAmount = Purchase::sum('total_amount');

        // Low stock products
        $lowStockProducts = Product::whereRaw('stock <= min_stock')->with('category')->get();
        $totalProducts = Product::count();

        // Recent sales
        $recentSales = Sale::with(['cashier', 'user', 'items.product'])
            ->latest('sale_date')
            ->take(6)
            ->get();

        // Top 5 selling products
        $topProducts = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.status', 'completed')
            ->select('products.name', 'products.sku', DB::raw('SUM(sale_items.quantity) as total_sold'), DB::raw('SUM(sale_items.subtotal) as total_revenue'))
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // Forecasting: Simple Moving Average (SMA 7-hari) untuk prediksi penjualan
        $salesLast14Days = Sale::where('status', 'completed')
            ->where('sale_date', '>=', Carbon::now()->subDays(14)->startOfDay())
            ->select(DB::raw('DATE(sale_date) as date'), DB::raw('SUM(total_amount) as daily_total'), DB::raw('COUNT(id) as daily_count'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $dailySalesMap = $salesLast14Days->pluck('daily_total', 'date')->toArray();
        $recent7DaysSum = 0;
        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::now()->subDays($i)->format('Y-m-d');
            $recent7DaysSum += ($dailySalesMap[$d] ?? 0);
        }
        $movingAverageDailyForecast = $recent7DaysSum > 0 ? round($recent7DaysSum / 7) : 0;

        return view('dashboard.owner', compact(
            'totalOmzet',
            'grossProfit',
            'totalCompletedSales',
            'totalCancelledSales',
            'totalPurchasesAmount',
            'lowStockProducts',
            'totalProducts',
            'recentSales',
            'topProducts',
            'movingAverageDailyForecast'
        ));
    }

    protected function staffDashboard()
    {
        $today = Carbon::today();

        $todaySalesAmount = Sale::where('status', 'completed')
            ->whereDate('sale_date', $today)
            ->sum('total_amount');

        $todayTransactionsCount = Sale::where('status', 'completed')
            ->whereDate('sale_date', $today)
            ->count();

        $todayPurchasesCount = Purchase::whereDate('purchase_date', $today)->count();

        $lowStockProducts = Product::whereRaw('stock <= min_stock')->with('category')->take(10)->get();

        $recentSales = Sale::with(['cashier', 'items.product'])
            ->latest('sale_date')
            ->take(8)
            ->get();

        return view('dashboard.staff', compact(
            'todaySalesAmount',
            'todayTransactionsCount',
            'todayPurchasesCount',
            'lowStockProducts',
            'recentSales'
        ));
    }

    protected function userDashboard()
    {
        $user = auth()->user();

        $orders = Sale::where('user_id', $user->id)
            ->with(['items.product'])
            ->latest('sale_date')
            ->paginate(10);

        $totalSpent = Sale::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('total_amount');

        $completedOrdersCount = Sale::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        return view('dashboard.user', compact(
            'orders',
            'totalSpent',
            'completedOrdersCount'
        ));
    }

    /**
     * JSON API Endpoint for Dynamic Chart.js Polling
     * Allows charts to refresh automatically every few seconds without page reload.
     */
    public function chartData(): JsonResponse
    {
        // 1. Daily sales trend (past 7 days)
        $labels = [];
        $salesData = [];
        $profitData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateString = $date->format('Y-m-d');
            $label = $date->translatedFormat('d M');
            $labels[] = $label;

            $daySale = Sale::where('status', 'completed')
                ->whereDate('sale_date', $dateString)
                ->sum('total_amount');

            $dayProfit = DB::table('sale_items')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->where('sales.status', 'completed')
                ->whereDate('sales.sale_date', $dateString)
                ->sum(DB::raw('(sale_items.selling_price - sale_items.purchase_price) * sale_items.quantity'));

            $salesData[] = (float) $daySale;
            $profitData[] = (float) $dayProfit;
        }

        // 2. Category distribution
        $categoryDistribution = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('sales.status', 'completed')
            ->select('categories.name', DB::raw('SUM(sale_items.quantity) as total_qty'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_qty')
            ->take(6)
            ->get();

        // 3. Live aggregates
        $liveOmzet = Sale::where('status', 'completed')->sum('total_amount');
        $liveTransactions = Sale::where('status', 'completed')->count();
        $liveLowStockCount = Product::whereRaw('stock <= min_stock')->count();

        return response()->json([
            'status' => 'success',
            'timestamp' => Carbon::now()->toIso8601String(),
            'sales_trend' => [
                'labels' => $labels,
                'sales' => $salesData,
                'profits' => $profitData,
            ],
            'category_distribution' => [
                'labels' => $categoryDistribution->pluck('name'),
                'data' => $categoryDistribution->pluck('total_qty'),
            ],
            'live_metrics' => [
                'omzet' => $liveOmzet,
                'omzet_formatted' => 'Rp ' . number_format($liveOmzet, 0, ',', '.'),
                'transactions' => $liveTransactions,
                'low_stock_count' => $liveLowStockCount,
            ]
        ]);
    }
}
