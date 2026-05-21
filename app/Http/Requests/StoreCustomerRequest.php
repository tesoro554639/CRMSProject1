<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isManager();
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:customers,email'],
            'phone' => ['required', 'string', 'max:20'],
            'company' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'status' => ['nullable', 'in:active,inactive'],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
        ];
    }
}
