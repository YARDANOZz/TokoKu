<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check(); // owner, staff, or customer
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['nullable', 'string', 'max:100'],
            'payment_method' => ['required', 'string', 'in:cash,transfer,qris'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => 'Pilih metode pembayaran.',
            'payment_method.in' => 'Metode pembayaran tidak valid.',
            'items.required' => 'Keranjang penjualan tidak boleh kosong.',
            'items.min' => 'Pilih minimal satu produk untuk transaksi.',
            'items.*.product_id.required' => 'Produk tidak valid.',
            'items.*.quantity.min' => 'Jumlah produk minimal 1.',
        ];
    }
}
