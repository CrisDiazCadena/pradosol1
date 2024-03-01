<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Contracts\Service\Attribute\Required;

class UpdateBeneficiaryRequest extends FormRequest
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
            //
            'name' => 'string|max:100',
            'lastname' => 'string|max:100',
            'type_beneficiary' => 'string|max:50',
            'identification_card' => 'string|max:50',
            'type_identification_id' => 'exists:type_identification_users,id',
            'partner_id' => 'required'
        ];
    }
}
