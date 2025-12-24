<?php

namespace App\Core\Game01\Infratructure\Jobs;

use App\Core\Game01\Application\Usecases\GenerateReportDateUseCase;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateReportJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private int $year,
        private int $month
    ) {}

    /**
     * Execute the job.
     */
    public function handle(
        GenerateReportDateUseCase $useCase
    ): void {
        $useCase->execute(
            year: $this->year,
            month: $this->month
        );
    }
}
