<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($this->route('user'))],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:admin,peminjam'],
            'account_status' => ['required', 'in:aktif,nonaktif'],
            'phone' => ['required', 'string', 'min:10', 'max:20', 'regex:/^(\+62|62|0)[0-9]{9,18}$/'],
            'address' => ['nullable', 'string'],
        ];

        if (Schema::hasColumn('users', 'nisn')) {
            $rules['nisn'] = ['required', 'digits:10', Rule::unique('users', 'nisn')->ignore($this->route('user'))];
        }

        return $rules;
    }
}
