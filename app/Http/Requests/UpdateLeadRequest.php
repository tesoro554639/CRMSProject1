<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Sales staff can only update their own assigned leads
        if (auth()->user()->isSales()) {
            $lead = $this->route('lead');

            return $lead && $lead->assigned_user_id === auth()->id();
        }

        // Only admin can update leads
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        $leadId = $this->route('lead')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'source' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:new,contacted,qualified,proposal_sent,negotiation,won,lost'],
            'priority' => ['required', 'in:low,medium,high,critical'],
            'expected_value' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
        ];
    }
}
