<?php

namespace App\Core\Game01\Infratructure\Controllers;

use App\Core\Game01\Infratructure\Jobs\GenerateReportJob;
use App\Core\Game01\Infratructure\Requests\GenerateReportRequest;
use App\Http\Controllers\Controller;

class GenerateReportController extends Controller
{

    public function __invoke(GenerateReportRequest $request)
    {
        $year  = $request->integer('year');
        $month = $request->integer('month');

        GenerateReportJob::dispatch(
            year: $year,
            month: $month
        );

        return response()->json([
            'status'      => 'processing',
        ], 202);
    }
}
