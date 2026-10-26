<?php

namespace Tests\Feature;

use App\Exceptions\InsufficientStockException;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use App\Services\SaleService;
use App\Services\StockService;
use Tests\TestCase;

class TokoKuSystemTest extends TestCase
{
    protected User $owner;
    protected User $staff;
    protected User $user;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::where('role', 'owner')->first();
        $this->staff = User::where('role', 'staff')->first();
        $this->user = User::where('role', 'user')->first();
        $this->product = Product::where('stock', '>', 5)->first();
    }

    /** Test 1: Login validation & wrong credentials */
    public function test_login_with_invalid_credentials_returns_error_message(): void
    {
        $response = $this->post('/login', [
            'email' => 'wrong@email.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['email' => 'Email atau password salah.']);
    }

    /** Test 2: Role Authorization - Staff blocked from cancelling sales */
    public function test_staff_cannot_cancel_sale(): void
    {
        $sale = Sale::where('status', 'completed')->first();

        $response = $this->actingAs($this->staff)->post("/sales/{$sale->id}/cancel", [
            'cancel_reason' => 'Staf mencoba membatalkan',
        ]);

        // Should return 403 Forbidden due to Policy / Gate
        $response->assertStatus(403);
    }

    /** Test 3: Role Authorization - Staff blocked from creating category */
    public function test_staff_cannot_create_category(): void
    {
        $response = $this->actingAs($this->staff)->post('/categories', [
            'name' => 'Kategori Baru Ilegal',
        ]);

        $response->assertStatus(403);
    }

    /** Test 4: Catalog only shows products with stock > 0 */
    public function test_catalog_only_displays_products_with_stock_greater_than_zero(): void
    {
        // Create an out of stock product
        $category = Category::first();
        $outOfStock = Product::create([
            'category_id' => $category->id,
            'sku' => 'TEST-OUT-001',
            'name' => 'Produk Habis Stok Hidden',
            'purchase_price' => 10000,
            'selling_price' => 20000,
            'stock' => 0,
            'min_stock' => 5,
        ]);

        $response = $this->get('/catalog');
        $response->assertStatus(200);
        $response->assertDontSee('TEST-OUT-001');

        $outOfStock->forceDelete();
    }

    /** Test 5: Dynamic Chart JSON endpoint returns valid polling structure */
    public function test_chart_polling_endpoint_returns_valid_data(): void
    {
        $response = $this->actingAs($this->owner)->get('/api/dashboard-charts');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'timestamp',
            'sales_trend' => ['labels', 'sales', 'profits'],
            'category_distribution' => ['labels', 'data'],
            'live_metrics' => ['omzet', 'transactions', 'low_stock_count'],
        ]);
    }

    /** Test 6: Sale Decreases Stock and Owner Cancellation Restores Stock */
    public function test_sale_decreases_stock_and_owner_cancel_restores_it(): void
    {
        $stockService = app(StockService::class);
        $saleService = app(SaleService::class);

        $initialStock = $this->product->fresh()->stock;
        $qtyToBuy = 2;

        // 1. Create Sale
        $sale = $saleService->createSale(
            ['customer_name' => 'Test Pembeli', 'payment_method' => 'cash'],
            [['product_id' => $this->product->id, 'quantity' => $qtyToBuy]],
            null,
            $this->staff
        );

        $this->assertEquals($initialStock - $qtyToBuy, $this->product->fresh()->stock);
        $this->assertEquals('completed', $sale->status);

        // 2. Owner Cancels Sale
        $saleService->cancelSale($sale, $this->owner, 'Testing pembatalan transaksi oleh owner');

        $this->assertEquals($initialStock, $this->product->fresh()->stock);
        $this->assertEquals('cancelled', $sale->fresh()->status);
        $this->assertEquals($this->owner->id, $sale->fresh()->cancelled_by);
    }

    /** Test 7: Insufficient stock triggers InsufficientStockException and rolls back */
    public function test_insufficient_stock_throws_exception_and_rolls_back(): void
    {
        $saleService = app(SaleService::class);
        $currentStock = $this->product->fresh()->stock;
        $excessiveQty = $currentStock + 500;

        $this->expectException(InsufficientStockException::class);

        $saleService->createSale(
            ['customer_name' => 'Beli Melebihi Stok', 'payment_method' => 'cash'],
            [['product_id' => $this->product->id, 'quantity' => $excessiveQty]],
            null,
            $this->staff
        );

        // Stock must remain unchanged
        $this->assertEquals($currentStock, $this->product->fresh()->stock);
    }
}
