<?php

namespace App\Http\Requests;

use App\Models\UserRequest;
use Illuminate\Foundation\Http\FormRequest;

class UserRequestRequest extends FormRequest
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
            'type' => 'required|in:' . UserRequest::TYPE_USER . ',' . UserRequest::TYPE_EVENT . ',' . UserRequest::TYPE_OTHER,
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
        ];
    }
}
