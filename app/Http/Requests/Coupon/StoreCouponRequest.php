<?php

namespace App\Http\Requests\Coupon;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCouponRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:50', 'unique:coupons,code'],
            'amount' => ['required', 'numeric', 'min:0'],
            'limit_count' => ['required', 'integer', 'min:1'],
            'effective_date' => ['required', 'date'],
            'expired_date' => ['required', 'date', 'after:effective_date'],
        ];
    }
}
