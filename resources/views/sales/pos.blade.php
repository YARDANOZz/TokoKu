@extends('layouts.app')

@section('title', 'Point of Sale (POS) Kasir')

@section('content')
<div class="h-[calc(100vh-8.5rem)] flex flex-col lg:flex-row gap-6 -m-4 sm:-m-6 lg:-m-8 p-4 sm:p-6 lg:p-8 overflow-hidden">

    <!-- Left Column: Product Selection Grid -->
    <div class="lg:w-7/12 flex flex-col bg-white rounded-2xl border border-slate-200 shadow-sm p-4 overflow-hidden">
        <!-- Search & Filter Header -->
        <div class="mb-4 space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></span>
                    <h2 class="text-base font-extrabold text-slate-900 tracking-tight">Katalog Kasir</h2>
                </div>
                <span class="text-xs text-slate-500" id="productCount">{{ $products->count() }} Produk Tersedia</span>
            </div>

            <div class="relative">
                <input type="text" id="posSearch" placeholder="Ketik nama produk atau scan barcode / SKU..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-barcode text-base"></i>
                </div>
            </div>
        </div>

        <!-- Scrollable Products Grid -->
        <div class="flex-1 overflow-y-auto pr-1">
            <div id="productGrid" class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach($products as $product)
                    <div class="product-item bg-slate-50 hover:bg-emerald-50/50 rounded-xl p-3 border border-slate-200 hover:border-emerald-300 transition cursor-pointer flex flex-col justify-between"
                         data-id="{{ $product->id }}"
                         data-name="{{ $product->name }}"
                         data-sku="{{ $product->sku }}"
                         data-price="{{ $product->selling_price }}"
                         data-stock="{{ $product->stock }}"
                         onclick="posAddToCart({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->sku }}', {{ $product->selling_price }}, {{ $product->stock }})">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] font-mono text-slate-500">{{ $product->sku }}</span>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $product->stock <= $product->min_stock ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                    Stok: {{ $product->stock }}
                                </span>
                            </div>
                            <h4 class="font-bold text-xs text-slate-900 line-clamp-2">{{ $product->name }}</h4>
                        </div>
                        <div class="mt-2 pt-2 border-t border-slate-200/60 flex items-center justify-between">
                            <span class="text-xs font-black text-emerald-700">
                                Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                            </span>
                            <span class="w-6 h-6 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-xs shadow-sm">
                                <i class="fa-solid fa-plus"></i>
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Right Column: Interactive POS Cart & Payment -->
    <div class="lg:w-5/12 flex flex-col bg-white rounded-2xl border border-slate-200 shadow-sm p-4 overflow-hidden">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-receipt text-indigo-600 text-lg"></i>
                <h3 class="text-base font-extrabold text-slate-900">Pesanan Aktif</h3>
            </div>
            <button type="button" onclick="clearPosCart()" class="text-xs font-bold text-rose-500 hover:text-rose-700">
                <i class="fa-solid fa-trash-can mr-1"></i> Bersihkan
            </button>
        </div>

        <!-- Cart Items List (Scrollable) -->
        <div id="posCartContainer" class="flex-1 overflow-y-auto py-3 space-y-2">
            <div id="emptyCartMessage" class="h-full flex flex-col items-center justify-center text-slate-400 py-12">
                <i class="fa-solid fa-cart-shopping text-4xl mb-2 text-slate-300"></i>
                <p class="text-xs font-semibold">Keranjang masih kosong</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Pilih produk di samping untuk ditambahkan</p>
            </div>
            <!-- Dynamic Cart Items injected here -->
            <div id="posCartItems" class="space-y-2"></div>
        </div>

        <!-- Payment & Checkout Form -->
        <form id="posCheckoutForm" method="POST" action="{{ route('sales.store') }}" class="pt-3 border-t border-slate-200 space-y-3">
            @csrf

            <!-- Hidden container for items array inputs -->
            <div id="hiddenItemsInputs"></div>

            <!-- Customer Name & Payment Method -->
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Nama Pembeli</label>
                    <input type="text" name="customer_name" id="customer_name" value="Umum / Walk-in"
                        class="w-full px-3 py-1.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Metode Bayar</label>
                    <select name="payment_method" id="payment_method" onchange="toggleCashInputs()"
                        class="w-full px-3 py-1.5 rounded-lg border border-slate-200 focus:outline-none focus:ring-1 focus:ring-indigo-500 font-semibold">
                        <option value="cash" selected>Tunai (Cash)</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="qris">QRIS</option>
                    </select>
                </div>
            </div>

            <!-- Calculation Box -->
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 space-y-1.5 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Total Belanja:</span>
                    <span id="posTotalDisplay" class="text-base font-black text-indigo-700">Rp 0</span>
                </div>

                <!-- Cash input & Change -->
                <div id="cashFields" class="space-y-1.5 pt-1 border-t border-slate-200/60">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-700">Uang Diterima:</span>
                        <input type="number" id="paid_amount" name="paid_amount" value="0" min="0" oninput="calculateChange()"
                            class="w-32 px-2.5 py-1 text-right font-bold rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="font-bold text-slate-700">Kembalian:</span>
                        <span id="posChangeDisplay" class="font-extrabold text-emerald-700 text-sm">Rp 0</span>
                    </div>

                    <!-- Quick Cash Buttons -->
                    <div class="flex gap-1 pt-1 overflow-x-auto">
                        <button type="button" onclick="setExactCash()" class="px-2 py-0.5 rounded bg-slate-200 hover:bg-slate-300 text-[10px] font-bold">Uang Pas</button>
                        <button type="button" onclick="setCash(50000)" class="px-2 py-0.5 rounded bg-slate-200 hover:bg-slate-300 text-[10px] font-bold">50k</button>
                        <button type="button" onclick="setCash(100000)" class="px-2 py-0.5 rounded bg-slate-200 hover:bg-slate-300 text-[10px] font-bold">100k</button>
                        <button type="button" onclick="setCash(200000)" class="px-2 py-0.5 rounded bg-slate-200 hover:bg-slate-300 text-[10px] font-bold">200k</button>
                    </div>
                </div>
            </div>

            <button type="submit" id="posSubmitBtn" disabled
                class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white font-black text-sm shadow-lg shadow-emerald-600/20 transition flex items-center justify-center">
                <i class="fa-solid fa-check-circle mr-2 text-base"></i> Selesaikan Transaksi (Cetak Struk)
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let posCart = {}; // { productId: { id, name, sku, price, stock, qty } }

    function formatRupiah(number) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
    }

    function posAddToCart(id, name, sku, price, stock) {
        if (stock <= 0) {
            alert('Stok produk ini sedang kosong!');
            return;
        }

        if (posCart[id]) {
            if (posCart[id].qty + 1 > stock) {
                alert('Jumlah melebihi stok yang tersedia (' + stock + ' unit)!');
                return;
            }
            posCart[id].qty += 1;
        } else {
            posCart[id] = { id, name, sku, price, stock, qty: 1 };
        }

        renderPosCart();
    }

    function changePosQty(id, delta) {
        if (!posCart[id]) return;
        const newQty = posCart[id].qty + delta;

        if (newQty <= 0) {
            delete posCart[id];
        } else if (newQty > posCart[id].stock) {
            alert('Jumlah melebihi stok yang tersedia (' + posCart[id].stock + ' unit)!');
            return;
        } else {
            posCart[id].qty = newQty;
        }

        renderPosCart();
    }

    function removePosItem(id) {
        delete posCart[id];
        renderPosCart();
    }

    function clearPosCart() {
        posCart = {};
        renderPosCart();
    }

    function renderPosCart() {
        const container = document.getElementById('posCartItems');
        const emptyMsg = document.getElementById('emptyCartMessage');
        const hiddenInputs = document.getElementById('hiddenItemsInputs');
        const submitBtn = document.getElementById('posSubmitBtn');

        container.innerHTML = '';
        hiddenInputs.innerHTML = '';

        const keys = Object.keys(posCart);
        if (keys.length === 0) {
            emptyMsg.classList.remove('hidden');
            submitBtn.disabled = true;
            document.getElementById('posTotalDisplay').innerText = 'Rp 0';
            document.getElementById('paid_amount').value = 0;
            document.getElementById('posChangeDisplay').innerText = 'Rp 0';
            return;
        }

        emptyMsg.classList.add('hidden');
        submitBtn.disabled = false;

        let total = 0;
        let index = 0;

        keys.forEach(id => {
            const item = posCart[id];
            const subtotal = item.price * item.qty;
            total += subtotal;

            // Render Item Card
            const itemDiv = document.createElement('div');
            itemDiv.className = 'flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 text-xs';
            itemDiv.innerHTML = `
                <div class="flex-1 min-w-0 pr-2">
                    <div class="font-bold text-slate-800 truncate">${item.name}</div>
                    <div class="text-[10px] text-slate-500">@ ${formatRupiah(item.price)}</div>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="flex items-center border border-slate-200 rounded-lg overflow-hidden bg-white">
                        <button type="button" onclick="changePosQty(${item.id}, -1)" class="w-6 h-6 flex items-center justify-center font-bold text-slate-600 hover:bg-slate-100">-</button>
                        <span class="w-8 text-center font-bold text-slate-800">${item.qty}</span>
                        <button type="button" onclick="changePosQty(${item.id}, 1)" class="w-6 h-6 flex items-center justify-center font-bold text-slate-600 hover:bg-slate-100">+</button>
                    </div>
                    <div class="w-20 text-right font-black text-slate-900">${formatRupiah(subtotal)}</div>
                    <button type="button" onclick="removePosItem(${item.id})" class="text-slate-400 hover:text-rose-600 ml-1">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            `;
            container.appendChild(itemDiv);

            // Hidden Form Inputs
            hiddenInputs.innerHTML += `
                <input type="hidden" name="items[${index}][product_id]" value="${item.id}">
                <input type="hidden" name="items[${index}][quantity]" value="${item.qty}">
            `;
            index++;
        });

        document.getElementById('posTotalDisplay').innerText = formatRupiah(total);
        calculateChange(total);
    }

    function calculateChange(currentTotal = null) {
        let total = currentTotal;
        if (total === null) {
            total = Object.values(posCart).reduce((sum, item) => sum + (item.price * item.qty), 0);
        }

        const paid = parseFloat(document.getElementById('paid_amount').value) || 0;
        const change = paid - total;

        const changeEl = document.getElementById('posChangeDisplay');
        if (change >= 0) {
            changeEl.innerText = formatRupiah(change);
            changeEl.className = 'font-extrabold text-emerald-700 text-sm';
        } else {
            changeEl.innerText = 'Kurang ' + formatRupiah(Math.abs(change));
            changeEl.className = 'font-extrabold text-rose-600 text-sm';
        }
    }

    function setExactCash() {
        const total = Object.values(posCart).reduce((sum, item) => sum + (item.price * item.qty), 0);
        document.getElementById('paid_amount').value = total;
        calculateChange(total);
    }

    function setCash(amount) {
        document.getElementById('paid_amount').value = amount;
        calculateChange();
    }

    function toggleCashInputs() {
        const method = document.getElementById('payment_method').value;
        const cashDiv = document.getElementById('cashFields');
        if (method === 'cash') {
            cashDiv.classList.remove('hidden');
        } else {
            // For transfer/qris, paid amount is exactly the total
            setExactCash();
            cashDiv.classList.add('hidden');
        }
    }

    // Live search filter in catalog grid
    document.getElementById('posSearch').addEventListener('input', function(e) {
        const val = e.target.value.toLowerCase();
        const items = document.querySelectorAll('.product-item');
        let visibleCount = 0;

        items.forEach(item => {
            const name = item.dataset.name.toLowerCase();
            const sku = item.dataset.sku.toLowerCase();
            if (name.includes(val) || sku.includes(val)) {
                item.style.display = 'flex';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        document.getElementById('productCount').innerText = visibleCount + ' Produk Ditemukan';
    });
</script>
@endpush
