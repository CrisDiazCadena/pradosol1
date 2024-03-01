<?php

namespace App\Http\Requests;

use App\Models\Partner;
use Illuminate\Foundation\Http\FormRequest;

class updatePartnerRequest extends FormRequest
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
            'children' => 'numeric|max:30',
            'marital_status' => 'in:' . Partner::MARRIED_STATUS . ',' . Partner::LIVING_STATUS .  ',' . Partner::WIDOW_STATUS . ',' . Partner::SINGLE_STATUS . ',' . Partner::SEPARATED_STATUS,
            'user_id' => 'required'
        ];
    }
}
