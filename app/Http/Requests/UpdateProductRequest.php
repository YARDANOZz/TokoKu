<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isOwner();
    }

    public function rules(): array
    {
        $productId = $this->route('product') ? $this->route('product')->id : null;

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'sku' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'sku')->ignore($productId),
            ],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Pilih kategori produk.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'sku.required' => 'SKU produk wajib diisi.',
            'sku.unique' => 'SKU ini sudah digunakan untuk produk lain.',
            'name.required' => 'Nama produk wajib diisi.',
            'purchase_price.required' => 'Harga beli wajib diisi.',
            'selling_price.required' => 'Harga jual wajib diisi.',
            'min_stock.required' => 'Batas minimum stok wajib diisi.',
            'image.image' => 'File harus berupa gambar.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}
