<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_id'  => 'required|exists:vehicles,id',
            'start_date'  => 'required|date|after_or_equal:today',
            'start_time'  => 'required|date_format:H:i',
            'end_date'    => 'required|date|after:start_date',
            'notes'       => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'vehicle_id.required'       => 'Kendaraan harus dipilih.',
            'vehicle_id.exists'         => 'Kendaraan tidak ditemukan.',
            'start_date.required'       => 'Tanggal mulai harus diisi.',
            'start_date.after_or_equal' => 'Tanggal mulai tidak boleh sebelum hari ini.',
            'start_time.required'       => 'Jam pengambilan harus diisi.',
            'start_time.date_format'    => 'Format jam tidak valid.',
            'end_date.required'         => 'Tanggal selesai harus diisi.',
            'end_date.after'            => 'Tanggal selesai harus setelah tanggal mulai.',
        ];
    }
}
