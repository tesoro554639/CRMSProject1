<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isManager() || auth()->user()->isSales();
    }

    public function rules(): array
    {
        return [
            'activity_type' => ['required', 'in:call,email,meeting,note'],
            'description' => ['required', 'string', 'max:2000'],
            'activity_date' => ['required', 'date'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'lead_id' => ['nullable', 'exists:leads,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $hasCustomer = $this->filled('customer_id');
            $hasLead = $this->filled('lead_id');

            if (! $hasCustomer && ! $hasLead) {
                $validator->errors()->add('link', 'Please select either a lead or customer.');
            }

            if ($hasCustomer && $hasLead) {
                $validator->errors()->add('link', 'Please select only one: lead or customer, not both.');
            }
        });
    }
}
