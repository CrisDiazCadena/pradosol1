<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'image' => 'required',
            'name' => 'required',
            'lastname' => 'required',
            'type' => 'required',
            'email' => [

                "required",
                Rule::unique('users', 'email')->ignore($this->user)
            ],
            'type_identification' => 'required',
            'identification_card' => [

                "required",
                Rule::unique('users', 'identification_card')->ignore($this->user)
            ],
            'address' => 'required',
            'status' => 'required',
            'phone' => 'nullable',
            'pass' => 'nullable',
            'current_user' => 'required',
        ];
    }
}
