<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:100',
            'type'          => 'required|string|max:50',
            'plate_number'  => 'required|string|max:20|unique:vehicles,plate_number',
            'price_per_day' => 'required|numeric|min:0',
            'photos'        => 'nullable|array|max:6',
            'photos.*'      => 'image|mimes:jpg,jpeg,png|max:2048',
            'status'        => 'required|in:available,rented,maintenance',
            'description'   => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'          => 'Nama kendaraan harus diisi.',
            'type.required'          => 'Tipe kendaraan harus diisi.',
            'plate_number.required'  => 'Nomor plat harus diisi.',
            'plate_number.unique'    => 'Nomor plat sudah digunakan.',
            'price_per_day.required' => 'Harga per hari harus diisi.',
            'price_per_day.numeric'  => 'Harga harus berupa angka.',
            'photos.max'             => 'Maksimal 6 foto.',
            'photos.*.image'         => 'Setiap file harus berupa gambar.',
            'photos.*.mimes'         => 'Format foto harus jpg, jpeg, atau png.',
            'photos.*.max'           => 'Ukuran setiap foto maksimal 2MB.',
            'status.required'        => 'Status kendaraan harus dipilih.',
        ];
    }
}
