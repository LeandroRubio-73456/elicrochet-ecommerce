<?php

namespace App\Http\Requests\Front;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCheckoutRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_lastname' => ['required', 'string', 'max:255'],
            'customer_cedula' => ['required', 'string', 'max:13'],
            'customer_email' => ['required', 'email'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'shipping_address' => ['required', 'string'],
            'shipping_city' => ['required', 'string'],
            'shipping_province' => ['required', 'string'],
            'shipping_reference' => ['nullable', 'string'],
            'shipping_zip' => ['required', 'string'],
        ];
    }
}
