<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class StoreExtensionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'additional_days' => 'required|integer|min:1|max:30',
            'payment_proof'   => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'additional_days.required' => 'Jumlah hari perpanjangan harus diisi.',
            'additional_days.integer'  => 'Jumlah hari harus berupa angka.',
            'additional_days.min'      => 'Minimal 1 hari perpanjangan.',
            'additional_days.max'      => 'Maksimal 30 hari perpanjangan.',
            'payment_proof.required'   => 'Bukti pembayaran harus diupload.',
            'payment_proof.image'      => 'File harus berupa gambar.',
            'payment_proof.mimes'      => 'Format gambar harus jpg, jpeg, atau png.',
            'payment_proof.max'        => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}
