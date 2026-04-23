<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFineSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'late_fee_per_day' => ['required', 'integer', 'min:1'],
            'max_fine_amount' => ['nullable', 'integer', 'min:1'],
            'grace_period_days' => ['required', 'integer', 'min:0', 'max:30'],
            'default_loan_duration_days' => ['required', 'integer', 'min:1', 'max:60'],
        ];
    }

    public function messages(): array
    {
        return [
            'late_fee_per_day.required' => 'Tarif denda per hari wajib diisi.',
            'late_fee_per_day.integer' => 'Tarif denda per hari harus berupa angka valid.',
            'late_fee_per_day.min' => 'Tarif denda per hari harus lebih dari nol.',
            'max_fine_amount.integer' => 'Maksimal denda harus berupa angka valid.',
            'max_fine_amount.min' => 'Maksimal denda harus lebih dari nol.',
            'grace_period_days.required' => 'Grace period wajib diisi.',
            'grace_period_days.integer' => 'Grace period harus berupa angka bulat.',
            'default_loan_duration_days.required' => 'Durasi pinjam default wajib diisi.',
            'default_loan_duration_days.integer' => 'Durasi pinjam default harus berupa angka bulat.',
            'default_loan_duration_days.min' => 'Durasi pinjam default minimal 1 hari.',
        ];
    }
}
