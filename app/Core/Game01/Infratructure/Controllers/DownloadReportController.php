<?php

namespace App\Core\Game01\Infratructure\Controllers;

use App\Core\Game01\Infratructure\Requests\DownloadReportRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadReportController extends Controller
{
    public function __invoke(DownloadReportRequest $request): BinaryFileResponse
    {
        $date = $request->input('date');

        $fileName = "report.zip";
        $relativePath = "exports/reports/{$date}/{$fileName}";

        if (!Storage::disk('public')->exists($relativePath)) {
            abort(404, "El reporte solicitado para la fecha {$date} no existe o aún se está procesando.");
        }

        $absolutePath = Storage::disk('public')->path($relativePath);

        return response()->download($absolutePath, $fileName);
    }
}
