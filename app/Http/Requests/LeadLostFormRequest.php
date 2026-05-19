<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeadLostFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lost_category' => ['required', 'string', 'max:255'],
            'lost_reason' => ['required', 'string', 'max:2000'],
        ];
    }
}
