<?php

use App\Core\Game01\Infratructure\Controllers\DownloadReportController;
use App\Core\Game01\Infratructure\Controllers\GenerateReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/reports/export', GenerateReportController::class);
Route::get('/reports/export', DownloadReportController::class);
