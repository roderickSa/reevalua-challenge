<?php

namespace App\Core\Game01\Infratructure\Exporters;

use App\Core\Game01\Domain\Contracts\XlsxExporterInterface;
use Rap2hpoutre\FastExcel\FastExcel;

class XlsxExporter implements XlsxExporterInterface
{
    public function export(
        array $rows,
        string $path,
        array $headers = []
    ): void {
        $fastExcel = new FastExcel($rows);

        $fastExcel->export($path);
    }
}
