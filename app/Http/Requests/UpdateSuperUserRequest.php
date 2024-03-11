<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSuperUserRequest extends FormRequest
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
            'phone' => 'string|max:30',
            'type_identification_id' => 'numeric|exists:type_identification_users,id',
            'status' => 'string|in:' .  User::ACTIVE_STATUS . ',' . User::INACTIVE_STATUS,
            'verified' => 'string|in:' . User::NOT_VERIFIED_USER . ',' . User::VERIFIED_USER,
            'validation_status_id' => 'numeric|exists:validation_status_users,id'


        ];
    }
}
