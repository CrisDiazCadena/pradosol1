<?php

namespace App\Http\Requests;

use App\Models\Products;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'type' => 'required|string|in:' . Products::TYPE_PRODUCT . "," . Products::TYPE_SERVICE,
            'image' => 'required|string',
            'price' => 'required|numeric|max:1000000000|min:0',
            'stock' => 'required|numeric|max:10000|min:0',
            'status' => 'required|string|in:' . Products::ACTIVE_STATUS . "," . Products::SOLD_STATUS . "," . Products::OFFER_STATUS,
            'description' => 'required|string|max:255',
            'discount_price' => 'nullable|numeric|max:1000000000|min:0'
        ];
    }

    protected function validateDiscount($validator)
    {
        $price = $this->input('price');
        $discount = $this->input('discount_price');

        if($discount && $price <= $discount){
            $validator->errors()->add('El precio debe ser mayor al descuento');
        }
    }
}
