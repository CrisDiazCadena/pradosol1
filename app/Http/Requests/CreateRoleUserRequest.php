<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Contracts\Service\Attribute\Required;

class CreateRoleUserRequest extends FormRequest
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
            'rol' => 'required',
            'position' => 'nullable',
            'vinculation' => 'required',
            'pass' => 'required',
            'marital_status' => 'required',
            'childrens' => 'required',
            'user_id' => 'required',
        ];
    }
}
