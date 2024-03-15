<?php

namespace App\Http\Requests;

use App\Models\Beneficiary;
use Illuminate\Foundation\Http\FormRequest;

class StatusBeneficiaryRequest extends FormRequest
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
            'verified' => 'required|in:' . Beneficiary::VERIFIED . "," . Beneficiary::NO_VERIFIED,
            'user_id' => 'required|exists:users,id'
        ];
    }
}
