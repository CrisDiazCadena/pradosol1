<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
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
            'position' => 'string|max:50',
            'start_work' => 'date_format:Y-m-d',
            'time_out' => 'date_format:H:i',
            'time_in' => 'date_format:H:i|before:time_out'
        ];
    }

    protected function validateTimeComparison($validator)
    {
        $timeOut = $this->input('time_out');
        $timeIn = $this->input('time_in');

        if ($timeOut <= $timeIn) {
            $validator->errors()->add('time_in', 'La hora de entrada debe ser anterior a la hora de salida.');
        }
    }

    public function messages()
    {
        return [
            // Otros mensajes de error aquí...
            'time_in.date_format' => 'El campo de hora de ingreso debe ser una hora válida en formato HH:mm.',
            'time_out.date_format' => 'El campo de hora de salida debe ser una hora válida en formato HH:mm.',
        ];
    }
}
