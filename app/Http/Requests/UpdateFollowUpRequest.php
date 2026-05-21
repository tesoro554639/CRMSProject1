<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFollowUpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isManager() || auth()->user()->isSales();
    }

    public function rules(): array
    {
        $followUpId = $this->route('follow_up')?->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['required', 'date'],
            'status' => ['required', 'in:pending,completed'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'lead_id' => ['nullable', 'exists:leads,id'],
            'user_id' => ['required', 'exists:users,id'],
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
