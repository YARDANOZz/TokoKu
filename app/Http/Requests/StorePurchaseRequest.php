<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->isOwner() || auth()->user()->isStaff());
    }

    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.purchase_price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required' => 'Pilih pemasok / supplier.',
            'supplier_id.exists' => 'Pemasok tidak ditemukan.',
            'purchase_date.required' => 'Tanggal pembelian wajib diisi.',
            'items.required' => 'Minimal harus menambahkan satu produk.',
            'items.min' => 'Minimal harus menambahkan satu produk.',
            'items.*.product_id.required' => 'Produk wajib dipilih.',
            'items.*.quantity.min' => 'Jumlah barang minimal 1.',
            'items.*.purchase_price.min' => 'Harga beli tidak boleh negatif.',
        ];
    }
}
