<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['required', 'string', 'max:1000'],
            'images.*' => ['image', 'max:2048'],
            'custom_specs' => ['array'],
        ];
    }
}
