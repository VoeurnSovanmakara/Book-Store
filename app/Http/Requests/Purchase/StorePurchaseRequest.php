<?php

namespace App\Http\Requests\Purchase;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_address_id' => ['required', 'exists:customer_addresses,id'],
            'coupon_code' => ['nullable', 'string', 'exists:coupons,code'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.book_id' => ['required', 'exists:books,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ];
    }
}
