<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isOwner();
    }

    public function rules(): array
    {
        return [
            'cancel_reason' => ['required', 'string', 'min:5', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'cancel_reason.required' => 'Alasan pembatalan transaksi wajib diisi.',
            'cancel_reason.min' => 'Alasan pembatalan minimal 5 karakter.',
        ];
    }
}
