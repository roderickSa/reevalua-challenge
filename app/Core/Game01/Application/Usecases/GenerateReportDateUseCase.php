<?php

namespace App\Core\Game01\Application\Usecases;

use App\Core\Game01\Application\Mappers\ReportMapper;
use App\Core\Game01\Domain\Contracts\CreditCardRepositoryInterface;
use App\Core\Game01\Domain\Contracts\LoanRepositoryInterface;
use App\Core\Game01\Domain\Contracts\OtherDebtRepositoryInterface;
use App\Core\Game01\Domain\Contracts\XlsxExporterInterface;
use App\Core\Game01\Domain\Models\CreditCard;
use App\Core\Game01\Domain\Models\Loan;
use App\Core\Game01\Domain\Models\OtherDebt;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class GenerateReportDateUseCase
{
    private int $chunkSize;

    public function __construct(
        private XlsxExporterInterface $xlsxExporter,
        private ReportMapper $reportMapper,
        private LoanRepositoryInterface $loanRepository,
        private CreditCardRepositoryInterface $creditCardRepository,
        private OtherDebtRepositoryInterface $otherDebtRepository,
    ) {
        $this->chunkSize = (int) env('REPORT_CHUNK_SIZE', 100);
    }

    public function execute(int $year, int $month): void
    {
        $today = now()->format('Y-m-d');
        $folderName = $today;
        $basePath = "exports/reports/{$folderName}";
        Storage::disk('public')->makeDirectory($basePath);
        $absolutePath = Storage::disk('public')->path($basePath);

        $repositories = [
            $this->loanRepository,
            $this->otherDebtRepository,
            $this->creditCardRepository,
        ];

        $buffer = [];
        $fileIndex = 1;

        foreach ($repositories as $repository) {
            $lastId = null;

            while (true) {
                $result = $repository->findChunkByDate(
                    year: $year,
                    month: $month,
                    limit: $this->chunkSize,
                    lastId: $lastId
                );

                if (empty($result['items'])) break;

                foreach ($result['items'] as $item) {
                    $buffer[] = $this->mapToReport($item, $today);

                    if (count($buffer) >= $this->chunkSize) {
                        $this->exportBuffer($buffer, $absolutePath, $fileIndex++);
                        $buffer = [];
                    }
                }

                if ($result['last_id'] === null) {
                    break;
                }

                $lastId = $result['last_id'];
            }
        }

        if (!empty($buffer)) {
            $this->exportBuffer($buffer, $absolutePath, $fileIndex);
        }

        $this->createZipArchive($absolutePath);
    }

    private function mapToReport($item, string $today)
    {
        return match (true) {
            $item instanceof Loan => $this->reportMapper->fromLoan($item, $today),
            $item instanceof OtherDebt => $this->reportMapper->fromOtherDebt($item, $today),
            $item instanceof CreditCard => $this->reportMapper->fromCreditCard($item, $today),
            default => throw new \InvalidArgumentException("Tipo desconocido"),
        };
    }

    private function exportBuffer(array $reports, string $path, int $index): void
    {
        $filePath = "{$path}/report_part_{$index}.xlsx";
        $this->xlsxExporter->export($reports, $filePath, $this->headers());
    }

    private function headers(): array
    {
        return [
            'ID',
            'Nombre completo',
            'DNI',
            'Email',
            'Teléfono',
            'Empresa',
            'Tipo deuda',
            'Condición',
            'Días atraso',
            'Entidad',
            'Monto total',
            'Línea total',
            'Línea usada',
            'Fecha reporte',
            'Estado'
        ];
    }

    private function createZipArchive(string $folderPath): void
    {
        $zipFile = "{$folderPath}/report.zip";
        $zip = new ZipArchive();

        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $files = glob("{$folderPath}/*.xlsx");
            foreach ($files as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();

            foreach ($files as $file) {
                unlink($file);
            }
        }
    }
}
