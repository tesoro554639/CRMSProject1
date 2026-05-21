<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeadLostFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Sales staff can only mark their own assigned leads as lost
        if (auth()->user()->isSales()) {
            $lead = $this->route('lead');

            return $lead && $lead->assigned_user_id === auth()->id();
        }

        // Admin and manager can mark any lead as lost
        return auth()->user()->isAdmin() || auth()->user()->isManager();
    }

    public function rules(): array
    {
        return [
            'lost_category' => ['required', 'string', 'max:255'],
            'lost_reason' => ['required', 'string', 'max:2000'],
        ];
    }
}
