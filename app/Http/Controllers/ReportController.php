<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Sales Report (Laporan Penjualan & Keuntungan)
     */
    public function sales(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $query = Sale::with(['cashier', 'items.product'])
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate);

        $sales = (clone $query)->latest('sale_date')->paginate(15)->withQueryString();

        // Summaries
        $completedSalesQuery = (clone $query)->where('status', 'completed');
        $totalOmzet = (clone $completedSalesQuery)->sum('total_amount');
        $totalTransactions = (clone $completedSalesQuery)->count();
        $cancelledCount = (clone $query)->where('status', 'cancelled')->count();

        // Profit calculation (Owner only)
        $totalGrossProfit = 0;
        if (auth()->user()->isOwner()) {
            $totalGrossProfit = DB::table('sale_items')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->where('sales.status', 'completed')
                ->whereDate('sales.sale_date', '>=', $startDate)
                ->whereDate('sales.sale_date', '<=', $endDate)
                ->sum(DB::raw('(sale_items.selling_price - sale_items.purchase_price) * sale_items.quantity'));
        }

        return view('reports.sales', compact(
            'sales',
            'startDate',
            'endDate',
            'totalOmzet',
            'totalTransactions',
            'cancelledCount',
            'totalGrossProfit'
        ));
    }

    /**
     * Purchases Report (Laporan Pembelian / Stok Masuk)
     */
    public function purchases(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $query = Purchase::with(['supplier', 'user', 'items.product'])
            ->whereDate('purchase_date', '>=', $startDate)
            ->whereDate('purchase_date', '<=', $endDate);

        $purchases = (clone $query)->latest('purchase_date')->paginate(15)->withQueryString();
        $totalSpent = (clone $query)->sum('total_amount');
        $totalPurchases = (clone $query)->count();

        return view('reports.purchases', compact(
            'purchases',
            'startDate',
            'endDate',
            'totalSpent',
            'totalPurchases'
        ));
    }

    /**
     * Inventory Valuation Report (Laporan Nilai Inventori & Stok)
     */
    public function stock(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = (clone $query)->latest()->paginate(15)->withQueryString();
        $categories = Category::all();

        $totalStockQty = Product::sum('stock');
        $totalAssetValue = Product::sum(DB::raw('stock * purchase_price'));
        $totalRetailValue = Product::sum(DB::raw('stock * selling_price'));

        return view('reports.stock', compact(
            'products',
            'categories',
            'totalStockQty',
            'totalAssetValue',
            'totalRetailValue'
        ));
    }
}
