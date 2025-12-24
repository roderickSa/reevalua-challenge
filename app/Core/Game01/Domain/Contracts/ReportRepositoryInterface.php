<?php

namespace App\Core\Game01\Domain\Contracts;

interface ReportRepositoryInterface {
    /**
     * @param int $year
     * @param int $month
     * @return array{items: Report[], last_id: int|null}
     */
    public function findChunkByDate(int $year, int $month, int $limit, ?int $lastId): array;
}
