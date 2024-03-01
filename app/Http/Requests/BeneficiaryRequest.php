<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BeneficiaryRequest extends FormRequest
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
            'lastname' => 'required|string|max:100',
            'type_beneficiary' => 'required|string|max:50',
            'identification_card' => 'required|string|max:50',
            'partner_id' => 'required|exists:partners,id',
            'type_identification_id' => 'required|exists:type_identification_users,id',
        ];
    }
}
