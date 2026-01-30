<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            // produit
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'nullable|numeric|min:0',
            'stock'       => 'nullable|integer|min:0',
            'image'       => 'nullable|string',
            'category_id' => 'required|exists:categories,id',

            // tailles pivot
            'sizes' => 'nullable|array|min:1',

            'sizes.*.size_id' => 'nullable|exists:sizes,id',
            'sizes.*.stock'   => 'nullable|integer|min:0',
            'sizes.*.price'   => 'nullable|numeric|min:0',
        ];
    }
}
