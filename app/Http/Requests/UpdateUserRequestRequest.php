<?php

namespace App\Http\Requests;

use App\Models\UserRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequestRequest extends FormRequest
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
            'status' => 'required|in:' . UserRequest::STATUS_ACCEPTED . ',' . UserRequest::STATUS_REJECTED,
            'comments' => 'required|string|max:255',
            'admin_id' => 'required|exists:administrators,id'
        ];
    }
}
