<?php

namespace App\Http\Requests;

use App\Enums\BillingInterval;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePlanRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'billing_interval' => ['required', Rule::enum(BillingInterval::class)],
            'course_ids' => ['nullable', 'array'],
            'course_ids.*' => ['integer', Rule::exists('courses', 'id')],
        ];
    }
}
