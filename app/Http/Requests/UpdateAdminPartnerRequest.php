<?php

namespace App\Http\Requests;

use App\Models\Partner;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminPartnerRequest extends FormRequest
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
            'name' => 'string|max:50',
            'lastname' => 'string|max:50',
            'identification_card' => 'string|max:30',
            'address' => 'string|max:50',
            'email' => 'email',
            'phone' => 'nullable|string|max:30',
            'type_identification_id' => 'numeric|exists:type_identification_users,id',
            'status' => 'string|in:' .  User::ACTIVE_STATUS . ',' . User::INACTIVE_STATUS,
            'verified' => 'string|in:' . User::NOT_VERIFIED_USER . ',' . User::VERIFIED_USER,
            'validation_status_id' => 'numeric|exists:validation_status_users,id',
            'children' => 'numeric|max:30',
            'pass' => 'numeric|max:30',
            'marital_status' => 'in:' . Partner::MARRIED_STATUS . ',' . Partner::LIVING_STATUS .  ',' . Partner::WIDOW_STATUS . ',' . Partner::SINGLE_STATUS . ',' . Partner::SEPARATED_STATUS,
            'bonding' => 'in:' . Partner::BONDING_PENSION . ',' . Partner::BONDING_WORK .  ',' . Partner::BONDING_GENERAL . ',' . Partner::NO_BONDING,
        ];
    }
}
