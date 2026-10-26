@extends('layouts.app')

@section('title', 'Input Stok Masuk (Pembelian)')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Penerimaan Stok Masuk</h1>
            <p class="text-xs text-slate-500 mt-1">Catat pasokan barang dari pemasok. Stok produk akan bertambah otomatis.</p>
        </div>
        <a href="{{ route('purchases.index') }}" class="text-xs font-bold text-slate-600 hover:text-indigo-600">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <form method="POST" action="{{ route('purchases.store') }}" class="space-y-6">
        @csrf

        <!-- Header Card: Supplier & Date -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="supplier_id" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    Pemasok / Distributor <span class="text-rose-500">*</span>
                </label>
                <select name="supplier_id" id="supplier_id" required
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Pilih Pemasok --</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>
                            {{ $sup->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="purchase_date" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    Tanggal Terima <span class="text-rose-500">*</span>
                </label>
                <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="notes" class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan / No. Surat Jalan</label>
                <input type="text" name="notes" id="notes" value="{{ old('notes') }}" placeholder="Opsional..."
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <!-- Items Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-boxes-packing text-indigo-600"></i>
                    <h3 class="text-base font-bold text-slate-900">Daftar Produk Masuk</h3>
                </div>
                <button type="button" onclick="addPurchaseRow()" class="px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold transition">
                    <i class="fa-solid fa-plus mr-1"></i> Tambah Baris
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs" id="purchaseItemsTable">
                    <thead>
                        <tr class="border-b border-slate-200 font-bold text-slate-500 uppercase">
                            <th class="py-2 w-5/12">Produk</th>
                            <th class="py-2 w-2/12 text-center">Jumlah (Qty)</th>
                            <th class="py-2 w-3/12 text-right">Harga Beli Satuan (Rp)</th>
                            <th class="py-2 w-2/12 text-right">Subtotal</th>
                            <th class="py-2 w-1/12 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="purchaseItemsBody" class="divide-y divide-slate-100">
                        <!-- Dynamic Rows Injected Here -->
                    </tbody>
                </table>
            </div>

            <!-- Grand Total Display -->
            <div class="pt-4 border-t border-slate-100 flex justify-between items-center text-sm font-bold">
                <span class="text-slate-600 uppercase">Total Biaya Pengadaan:</span>
                <span id="grandTotalDisplay" class="text-xl font-black text-indigo-600 font-mono">Rp 0</span>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('purchases.index') }}" class="px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 transition flex items-center">
                <i class="fa-solid fa-cloud-arrow-up mr-2 text-sm"></i> Simpan Stok Masuk & Update Stok
            </button>
        </div>
    </form>
</div>

<!-- Product options template -->
<template id="productSelectOptions">
    <option value="">-- Pilih Produk --</option>
    @foreach($products as $prod)
        <option value="{{ $prod->id }}" data-price="{{ $prod->purchase_price }}" data-stock="{{ $prod->stock }}">
            {{ $prod->name }} (SKU: {{ $prod->sku }} | Stok Saat Ini: {{ $prod->stock }})
        </option>
    @endforeach
</template>

@push('scripts')
<script>
    let rowIndex = 0;

    function formatRupiah(number) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
    }

    function addPurchaseRow() {
        const tbody = document.getElementById('purchaseItemsBody');
        const optionsHtml = document.getElementById('productSelectOptions').innerHTML;

        const tr = document.createElement('tr');
        tr.id = `row-${rowIndex}`;
        tr.innerHTML = `
            <td class="py-2.5 pr-2">
                <select name="items[${rowIndex}][product_id]" required onchange="onProductSelect(this, ${rowIndex})"
                    class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs focus:ring-1 focus:ring-indigo-500">
                    ${optionsHtml}
                </select>
            </td>
            <td class="py-2.5 px-2 text-center">
                <input type="number" name="items[${rowIndex}][quantity]" id="qty-${rowIndex}" value="1" min="1" required
                    oninput="calcRow(${rowIndex})"
                    class="w-20 px-2 py-1.5 text-center font-bold rounded-lg border border-slate-200 text-xs focus:ring-1 focus:ring-indigo-500">
            </td>
            <td class="py-2.5 px-2 text-right">
                <input type="number" name="items[${rowIndex}][purchase_price]" id="price-${rowIndex}" value="0" min="0" required
                    oninput="calcRow(${rowIndex})"
                    class="w-32 px-2 py-1.5 text-right font-mono font-bold rounded-lg border border-slate-200 text-xs focus:ring-1 focus:ring-indigo-500">
            </td>
            <td class="py-2.5 px-2 text-right font-bold text-slate-800 font-mono text-xs" id="subtotal-${rowIndex}">
                Rp 0
            </td>
            <td class="py-2.5 pl-2 text-center">
                <button type="button" onclick="removePurchaseRow(${rowIndex})" class="text-slate-400 hover:text-rose-600 transition" title="Hapus baris">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        rowIndex++;
        calculateGrandTotal();
    }

    function removePurchaseRow(index) {
        const row = document.getElementById(`row-${index}`);
        if (row) {
            row.remove();
            calculateGrandTotal();
        }
    }

    function onProductSelect(selectEl, idx) {
        const selectedOption = selectEl.options[selectEl.selectedIndex];
        const defaultPrice = selectedOption.dataset.price || 0;
        document.getElementById(`price-${idx}`).value = defaultPrice;
        calcRow(idx);
    }

    function calcRow(idx) {
        const qty = parseFloat(document.getElementById(`qty-${idx}`).value) || 0;
        const price = parseFloat(document.getElementById(`price-${idx}`).value) || 0;
        const subtotal = qty * price;
        const subtotalEl = document.getElementById(`subtotal-${idx}`);
        if (subtotalEl) {
            subtotalEl.innerText = formatRupiah(subtotal);
        }
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let total = 0;
        const tbody = document.getElementById('purchaseItemsBody');
        const rows = tbody.querySelectorAll('tr');

        rows.forEach(tr => {
            const idx = tr.id.replace('row-', '');
            const qtyInput = document.getElementById(`qty-${idx}`);
            const priceInput = document.getElementById(`price-${idx}`);
            if (qtyInput && priceInput) {
                const qty = parseFloat(qtyInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                total += (qty * price);
            }
        });

        document.getElementById('grandTotalDisplay').innerText = formatRupiah(total);
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Start with 1 row by default
        addPurchaseRow();
    });
</script>
@endpush
@endsection
