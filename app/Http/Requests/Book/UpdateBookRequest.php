<?php

namespace App\Http\Requests\Book;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
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
            'author_id' => ['sometimes', 'required', 'exists:authors,id'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'year' => ['sometimes', 'required', 'integer', 'min:1450', 'max:' . (date('Y') + 1)],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'cover' => ['sometimes', 'nullable', 'image', 'max:2048'],
            'stock' => ['sometimes', 'required', 'integer', 'min:0'],
        ];
    }
}
