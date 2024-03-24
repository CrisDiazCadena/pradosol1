<?php

namespace App\Http\Requests;

use App\Models\Products;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStockRequest extends FormRequest
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
            'stock' => 'required|numeric|max:1000|min:0',
            'status' => 'required|string|in:' . Products::ACTIVE_STATUS . "," . Products::SOLD_STATUS . "," . Products::OFFER_STATUS
        ];
    }
}
