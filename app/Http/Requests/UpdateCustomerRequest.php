<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Sales staff can only update their own assigned customers
        if (auth()->user()->isSales()) {
            $customer = $this->route('customer');

            return $customer && $customer->assigned_user_id === auth()->id();
        }

        // Admin and manager can update any customer
        return auth()->user()->isAdmin() || auth()->user()->isManager();
    }

    public function rules(): array
    {
        $customerId = $this->route('customer')?->id;

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:customers,email,'.$customerId],
            'phone' => ['required', 'string', 'max:20'],
            'company' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'status' => ['nullable', 'in:active,inactive'],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
        ];
    }
}
