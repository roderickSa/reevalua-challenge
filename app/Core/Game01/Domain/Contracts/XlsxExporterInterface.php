<?php

namespace App\Core\Game01\Domain\Contracts;

interface XlsxExporterInterface
{
    /**
     * @param Report[] $rows
     * @param string $path
     * @param array $headers
     * @return void
     */
    public function export(
        array $rows,
        string $path,
        array $headers = []
    ): void;
}
