<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Contracts\Service\Attribute\Required;

class RegisterRequest extends FormRequest
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
            'type_identification' => 'required',
            'identification_card' => 'required|unique:users,type_identification',
            'password' => 'required',
            'name' => 'required',
            'lastname' => 'required',
            'address' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required',
        ];
    }
}
