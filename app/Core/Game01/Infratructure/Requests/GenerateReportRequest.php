<?php

namespace App\Core\Game01\Infratructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'year'  => ['required', 'integer', 'min:2000', 'max:' . (date('Y') + 1)],
            'month' => ['required', 'integer', 'between:1,12'],
        ];
    }

    public function messages(): array
    {
        return [
            'year.required'  => 'El year es obligatorio.',
            'month.between' => 'El month debe estar entre 1 y 12.',
        ];
    }
}
