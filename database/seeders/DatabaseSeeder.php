<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Services\PurchaseService;
use App\Services\SaleService;
use App\Services\StockService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $stockService = new StockService();
        $purchaseService = new PurchaseService($stockService);
        $saleService = new SaleService($stockService);

        // 1. Seed Users for each Role
        $this->call(UserSeeder::class);
        $owner = User::firstOrCreate(
            ['email' => 'owner@tokoku.com'],
            [
                'name' => 'Budi Santoso (Owner)',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'phone' => '081234567891',
                'address' => 'Jl. Sudirman No. 10, Jakarta Pusat',
            ]
        );

        $staff = User::firstOrCreate(
            ['email' => 'staff@tokoku.com'],
            [
                'name' => 'Siti Rahma (Staff/Kasir)',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'phone' => '081234567892',
                'address' => 'Jl. Kebon Jeruk No. 45, Jakarta Barat',
            ]
        );

        $customer = User::firstOrCreate(
            ['email' => 'user@tokoku.com'],
            [
                'name' => 'Andi Wijaya (Pelanggan)',
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone' => '081234567893',
                'address' => 'Jl. Melati No. 8, Tangerang',
            ]
        );

        // 2. Seed Categories
        $categoriesData = [
            ['name' => 'Elektronik & Gadget', 'description' => 'Aksesoris gadget, kabel, charger, dan perangkat elektronik ritel.'],
            ['name' => 'Makanan & Minuman', 'description' => 'Snack, minuman kemasan, dan kebutuhan konsumsi harian.'],
            ['name' => 'Alat Tulis Kantor', 'description' => 'Buku, pulpen, map, kalkulator, dan perlengkapan kantor.'],
            ['name' => 'Fashion & Pakaian', 'description' => 'Kaos polos, kemeja, celana, topi, dan pakaian casual.'],
            ['name' => 'Kebutuhan Rumah Tangga', 'description' => 'Sabun cuci, pewangi, pembersih lantai, dan perlengkapan rumah.'],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['name']] = Category::firstOrCreate(
                ['name' => $cat['name']],
                ['slug' => Str::slug($cat['name']), 'description' => $cat['description']]
            );
        }

        // 3. Seed Suppliers
        $suppliersData = [
            ['name' => 'PT Sumber Rejeki Abadi', 'email' => 'kontak@sumberrejeki.com', 'phone' => '08111222333', 'address' => 'Kawasan Industri Pulogadung, Jakarta Timur'],
            ['name' => 'CV Mega Niaga Distribusi', 'email' => 'sales@meganiaga.co.id', 'phone' => '08122333444', 'address' => 'Jl. Soekarno Hatta No. 88, Bandung'],
            ['name' => 'PT Pangan Sejahtera Prima', 'email' => 'order@panganprima.com', 'phone' => '08133444555', 'address' => 'Komplek Pergudangan Margomulyo, Surabaya'],
            ['name' => 'CV Stationery Nusantara', 'email' => 'nusantara.atk@gmail.com', 'phone' => '08144555666', 'address' => 'Jl. Pemuda No. 120, Semarang'],
        ];

        $suppliers = [];
        foreach ($suppliersData as $sup) {
            $suppliers[] = Supplier::firstOrCreate(['name' => $sup['name']], $sup);
        }

        // 4. Seed Products with SKU, buy/sell prices, and initial stock = 0
        $productsData = [
            // Elektronik
            [
                'category' => 'Elektronik & Gadget',
                'sku' => 'ELK-001',
                'name' => 'Kabel Data Fast Charging Type-C 1M',
                'description' => 'Kabel fast charging 65W nylon braided tahan tekukan.',
                'purchase_price' => 15000,
                'selling_price' => 28000,
                'min_stock' => 10,
            ],
            [
                'category' => 'Elektronik & Gadget',
                'sku' => 'ELK-002',
                'name' => 'Powerbank 10.000 mAh Dual Output',
                'description' => 'Powerbank portabel slim dengan indikator LED baterai.',
                'purchase_price' => 75000,
                'selling_price' => 125000,
                'min_stock' => 5,
            ],
            [
                'category' => 'Elektronik & Gadget',
                'sku' => 'ELK-003',
                'name' => 'TWS Bluetooth Earbuds Wireless 5.3',
                'description' => 'Earbuds bass booster dengan daya tahan baterai 24 jam.',
                'purchase_price' => 85000,
                'selling_price' => 149000,
                'min_stock' => 5,
            ],
            // Makanan & Minuman
            [
                'category' => 'Makanan & Minuman',
                'sku' => 'MKN-001',
                'name' => 'Kopi Arabika Gayo Bubuk 200g',
                'description' => 'Kopi single origin aceh gayo aroma harum medium roast.',
                'purchase_price' => 32000,
                'selling_price' => 48000,
                'min_stock' => 8,
            ],
            [
                'category' => 'Makanan & Minuman',
                'sku' => 'MKN-002',
                'name' => 'Keripik Kentang Keju Panggang 100g',
                'description' => 'Snack renyah gurih rasa keju panggang lezat.',
                'purchase_price' => 9500,
                'selling_price' => 15000,
                'min_stock' => 15,
            ],
            [
                'category' => 'Makanan & Minuman',
                'sku' => 'MKN-003',
                'name' => 'Teh Melati Organik Premium Box 25 Kantong',
                'description' => 'Teh wangi melati asli menyegarkan tanpa pengawet.',
                'purchase_price' => 12000,
                'selling_price' => 19500,
                'min_stock' => 10,
            ],
            // Alat Tulis Kantor
            [
                'category' => 'Alat Tulis Kantor',
                'sku' => 'ATK-001',
                'name' => 'Buku Catatan Hardcover A5 Grid 100 Lembar',
                'description' => 'Notebook sampul tebal kertas 80gsm anti tembus.',
                'purchase_price' => 18000,
                'selling_price' => 32000,
                'min_stock' => 10,
            ],
            [
                'category' => 'Alat Tulis Kantor',
                'sku' => 'ATK-002',
                'name' => 'Pulpen Gel Gel-Pen 0.5mm Hitam Box Isi 12',
                'description' => 'Pulpen tinta gel lancar tidak macet warna pekat.',
                'purchase_price' => 22000,
                'selling_price' => 36000,
                'min_stock' => 8,
            ],
            // Fashion
            [
                'category' => 'Fashion & Pakaian',
                'sku' => 'FSH-001',
                'name' => 'Kaos Polos Cotton Combed 30s Hitam (L)',
                'description' => 'Bahan 100% cotton combed adem dan nyaman dipakai harian.',
                'purchase_price' => 35000,
                'selling_price' => 60000,
                'min_stock' => 10,
            ],
            [
                'category' => 'Fashion & Pakaian',
                'sku' => 'FSH-002',
                'name' => 'Topi Baseball Casual Polos Navy',
                'description' => 'Topi bahan drill dengan strap pengatur ukuran logam.',
                'purchase_price' => 20000,
                'selling_price' => 38000,
                'min_stock' => 5,
            ],
            // Rumah Tangga
            [
                'category' => 'Kebutuhan Rumah Tangga',
                'sku' => 'RMT-001',
                'name' => 'Cairan Pembersih Lantai Lavender 800ml',
                'description' => 'Pembersih lantai antibakteri aroma lavender segar tahan lama.',
                'purchase_price' => 11000,
                'selling_price' => 17500,
                'min_stock' => 12,
            ],
            [
                'category' => 'Kebutuhan Rumah Tangga',
                'sku' => 'RMT-002',
                'name' => 'Sabun Cuci Piring Jeruk Nipis 750ml Refill',
                'description' => 'Formula ekstra jeruk nipis ampuh angkat lemak membandel.',
                'purchase_price' => 10500,
                'selling_price' => 16000,
                'min_stock' => 12,
            ],
        ];

        $createdProducts = [];
        foreach ($productsData as $p) {
            $cat = $categories[$p['category']];
            $createdProducts[$p['sku']] = Product::firstOrCreate(
                ['sku' => $p['sku']],
                [
                    'category_id' => $cat->id,
                    'name' => $p['name'],
                    'description' => $p['description'],
                    'purchase_price' => $p['purchase_price'],
                    'selling_price' => $p['selling_price'],
                    'stock' => 0, // initially 0, will increase through PurchaseService!
                    'min_stock' => $p['min_stock'],
                ]
            );
        }

        // 5. Seed Initial Stock via PurchaseService (Stok Masuk)
        // This ensures the stock is recorded properly in purchases, purchase_items, AND stock_movements!
        $purchaseBatch1 = [
            'supplier_id' => $suppliers[0]->id,
            'purchase_date' => Carbon::now()->subDays(10)->format('Y-m-d'),
            'notes' => 'Stok masuk awal pengadaan elektronik dari PT Sumber Rejeki Abadi',
        ];
        $itemsBatch1 = [
            ['product_id' => $createdProducts['ELK-001']->id, 'quantity' => 50, 'purchase_price' => 15000],
            ['product_id' => $createdProducts['ELK-002']->id, 'quantity' => 25, 'purchase_price' => 75000],
            ['product_id' => $createdProducts['ELK-003']->id, 'quantity' => 20, 'purchase_price' => 85000],
        ];
        $purchaseService->createPurchase($purchaseBatch1, $itemsBatch1, $owner);

        $purchaseBatch2 = [
            'supplier_id' => $suppliers[2]->id,
            'purchase_date' => Carbon::now()->subDays(9)->format('Y-m-d'),
            'notes' => 'Pasokan makanan dan minuman harian',
        ];
        $itemsBatch2 = [
            ['product_id' => $createdProducts['MKN-001']->id, 'quantity' => 40, 'purchase_price' => 32000],
            ['product_id' => $createdProducts['MKN-002']->id, 'quantity' => 60, 'purchase_price' => 9500],
            ['product_id' => $createdProducts['MKN-003']->id, 'quantity' => 35, 'purchase_price' => 12000],
        ];
        $purchaseService->createPurchase($purchaseBatch2, $itemsBatch2, $staff);

        $purchaseBatch3 = [
            'supplier_id' => $suppliers[3]->id,
            'purchase_date' => Carbon::now()->subDays(8)->format('Y-m-d'),
            'notes' => 'Pasokan ATK dan Fashion untuk etalase depan',
        ];
        $itemsBatch3 = [
            ['product_id' => $createdProducts['ATK-001']->id, 'quantity' => 30, 'purchase_price' => 18000],
            ['product_id' => $createdProducts['ATK-002']->id, 'quantity' => 40, 'purchase_price' => 22000],
            ['product_id' => $createdProducts['FSH-001']->id, 'quantity' => 25, 'purchase_price' => 35000],
            ['product_id' => $createdProducts['FSH-002']->id, 'quantity' => 15, 'purchase_price' => 20000],
            ['product_id' => $createdProducts['RMT-001']->id, 'quantity' => 30, 'purchase_price' => 11000],
            ['product_id' => $createdProducts['RMT-002']->id, 'quantity' => 30, 'purchase_price' => 10500],
        ];
        $purchaseService->createPurchase($purchaseBatch3, $itemsBatch3, $owner);

        // 6. Seed Sample Multi-Item Sales spanning across the past 7 days
        // This populates our Chart.js graphs, dashboard analytics, and stock_movements!
        $salesRounds = [
            [
                'days_ago' => 6,
                'customer' => 'Dewi Lestari',
                'payment' => 'cash',
                'items' => [
                    ['product_id' => $createdProducts['MKN-001']->id, 'quantity' => 2],
                    ['product_id' => $createdProducts['MKN-002']->id, 'quantity' => 3],
                ]
            ],
            [
                'days_ago' => 5,
                'customer' => 'Rian Hidayat',
                'payment' => 'qris',
                'items' => [
                    ['product_id' => $createdProducts['ELK-001']->id, 'quantity' => 3],
                    ['product_id' => $createdProducts['ELK-002']->id, 'quantity' => 1],
                ]
            ],
            [
                'days_ago' => 4,
                'customer' => 'Bambang Pratama',
                'payment' => 'transfer',
                'items' => [
                    ['product_id' => $createdProducts['FSH-001']->id, 'quantity' => 2],
                    ['product_id' => $createdProducts['FSH-002']->id, 'quantity' => 1],
                    ['product_id' => $createdProducts['ATK-001']->id, 'quantity' => 1],
                ]
            ],
            [
                'days_ago' => 3,
                'customer' => 'Siska Amelia',
                'payment' => 'cash',
                'items' => [
                    ['product_id' => $createdProducts['RMT-001']->id, 'quantity' => 2],
                    ['product_id' => $createdProducts['RMT-002']->id, 'quantity' => 2],
                    ['product_id' => $createdProducts['MKN-003']->id, 'quantity' => 1],
                ]
            ],
            [
                'days_ago' => 2,
                'customer' => 'Hendra Setiawan',
                'payment' => 'qris',
                'items' => [
                    ['product_id' => $createdProducts['ELK-003']->id, 'quantity' => 1],
                    ['product_id' => $createdProducts['ELK-001']->id, 'quantity' => 2],
                ]
            ],
            [
                'days_ago' => 1,
                'customer' => 'Andi Wijaya (Pelanggan)',
                'payment' => 'transfer',
                'user' => $customer,
                'items' => [
                    ['product_id' => $createdProducts['MKN-001']->id, 'quantity' => 1],
                    ['product_id' => $createdProducts['ATK-002']->id, 'quantity' => 2],
                ]
            ],
            [
                'days_ago' => 0,
                'customer' => 'Kasir Walk-in',
                'payment' => 'cash',
                'items' => [
                    ['product_id' => $createdProducts['ELK-001']->id, 'quantity' => 1],
                    ['product_id' => $createdProducts['MKN-002']->id, 'quantity' => 2],
                ]
            ],
        ];

        foreach ($salesRounds as $round) {
            $userObj = $round['user'] ?? null;
            $cashierObj = $userObj ? null : $staff;

            // Calculate exact total to pass paid_amount for cash/transfer
            $sale = $saleService->createSale(
                [
                    'customer_name' => $round['customer'],
                    'payment_method' => $round['payment'],
                ],
                $round['items'],
                $userObj,
                $cashierObj
            );

            // Backdate created_at and sale_date to generate historical trend
            $backdate = Carbon::now()->subDays($round['days_ago'])->setHour(rand(9, 20))->setMinute(rand(5, 55));
            $sale->update(['sale_date' => $backdate, 'created_at' => $backdate]);
            $sale->items()->update(['created_at' => $backdate]);
        }

        // 7. Seed One Cancelled Sale (To demonstrate cancellation, restored stock, and cancelled_by metadata)
        $saleToCancel = $saleService->createSale(
            [
                'customer_name' => 'Joko Susilo (Batal)',
                'payment_method' => 'cash',
            ],
            [
                ['product_id' => $createdProducts['ELK-002']->id, 'quantity' => 1],
                ['product_id' => $createdProducts['ATK-001']->id, 'quantity' => 1],
            ],
            null,
            $staff
        );
        // Owner cancels it:
        $saleService->cancelSale(
            $saleToCancel,
            $owner,
            'Pelanggan membatalkan pesanan karena salah pilih varian barang.'
        );
    }
}
