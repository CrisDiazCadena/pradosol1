<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LicenseRequest extends FormRequest
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

            'description',
            'support' => 'required|string|max:100',
            'start_license' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $endLicense = $this->input('end_license');
                    if ($endLicense && $value > $endLicense) {
                        $fail('La fecha de inicio debe ser anterior a la fecha de fin.');
                    }
                },
            ],
            'end_license' => 'required|date',
            'type_license_id' => 'required|exists:licenses_types,id',
            'employee_id' => 'required|exists:employees,id',

        ];
    }
}
