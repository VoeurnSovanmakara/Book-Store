<?php

namespace App\Http\Requests\Coupon;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCouponRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['sometimes', 'required', 'string', 'max:50', 'unique:coupons,code,' . $this->route('coupon')->id],
            'amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'limit_count' => ['sometimes', 'required', 'integer', 'min:1'],
            'effective_date' => ['sometimes', 'required', 'date'],
            'expired_date' => ['sometimes', 'required', 'date', 'after:effective_date'],
        ];
    }
}
