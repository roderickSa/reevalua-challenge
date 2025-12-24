<?php

namespace App\Providers;

use App\Core\Game01\Domain\Contracts\CreditCardRepositoryInterface;
use App\Core\Game01\Domain\Contracts\LoanRepositoryInterface;
use App\Core\Game01\Domain\Contracts\OtherDebtRepositoryInterface;
use App\Core\Game01\Domain\Contracts\ReportRepositoryInterface;
use App\Core\Game01\Domain\Contracts\XlsxExporterInterface;
use App\Core\Game01\Infratructure\Exporters\XlsxExporter;
use App\Core\Game01\Infratructure\Repositories\CreditCardReportRepository;
use App\Core\Game01\Infratructure\Repositories\LoanReportRepository;
use App\Core\Game01\Infratructure\Repositories\OtherDebtReportRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    $this->app->bind(
        LoanRepositoryInterface::class,
        LoanReportRepository::class
    );

    $this->app->bind(
        OtherDebtRepositoryInterface::class,
        OtherDebtReportRepository::class
    );

    $this->app->bind(
        CreditCardRepositoryInterface::class,
        CreditCardReportRepository::class
    );

    $this->app->bind(
        XlsxExporterInterface::class,
        XlsxExporter::class
    );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
