<?php

namespace App\Core\Game01\Infratructure\Repositories;

use App\Core\Game01\Domain\Contracts\OtherDebtRepositoryInterface;
use App\Core\Game01\Domain\Models\OtherDebt;
use Illuminate\Support\Facades\DB;

class OtherDebtReportRepository implements OtherDebtRepositoryInterface
{
    public function findChunkByDate(
        int $year,
        int $month,
        int $limit,
        ?int $lastId
    ): array {
        $query = DB::table('subscription_reports as sr')
            ->join('subscriptions as s', 's.id', '=', 'sr.subscription_id')
            ->join('report_other_debts as rod', 'rod.subscription_report_id', '=', 'sr.id')
            ->where('sr.year', $year)
            ->where('sr.month', $month)
            ->orderBy('sr.id')
            ->select([
                'sr.id',
                's.full_name',
                's.document',
                's.email',
                's.phone',
                'sr.year',
                'sr.month',
                'rod.id as report_id',
                'rod.entity',
                'rod.currency',
                'rod.amount',
                'rod.expiration_days',
            ])
            ->limit($limit);

        if ($lastId !== null) {
            $query->where('sr.id', '>', $lastId);
        }

        $rows = $query->get();

        if ($rows->isEmpty()) {
            return [
                'items' => [],
                'last_id' => null
            ];
        }

        $reports = [];
        foreach ($rows as $row) {
            $reports[] = new OtherDebt(
                id: $row->id,
                fullName: $row->full_name,
                document: $row->document,
                email: $row->email,
                phone: $row->phone,
                period: $row->year . '-' . $row->month,
                reportId: $row->report_id,
                entity: $row->entity,
                currency: $row->currency,
                amount: $row->amount,
                expirationDays: $row->expiration_days,
            );
        }

        $lastRow = $rows->last();

        return [
            'items' => $reports,
            'last_id' => $lastRow->id
        ];
    }
}
