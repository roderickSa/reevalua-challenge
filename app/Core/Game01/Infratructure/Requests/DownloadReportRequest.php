<?php

namespace App\Core\Game01\Infratructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DownloadReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => [
                'required',
                'date',
                'date_format:Y-m-d',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'date.required'    => 'La fecha del reporte es obligatoria.',
            'date.date_format' => 'La fecha debe tener el formato Año-Mes-Día (ejemplo: 2025-12-25).',
        ];
    }
}
