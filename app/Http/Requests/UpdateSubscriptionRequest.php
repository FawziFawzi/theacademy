<?php

namespace App\Http\Requests;

use App\Enums\SubscriptionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(SubscriptionStatus::class)],
            'current_period_start' => ['required', 'date'],
            'current_period_end' => ['required', 'date', 'after_or_equal:current_period_start'],
        ];
    }
}
