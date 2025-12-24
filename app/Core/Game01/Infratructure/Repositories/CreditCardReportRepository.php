<?php

namespace App\Core\Game01\Infratructure\Repositories;

use App\Core\Game01\Domain\Contracts\CreditCardRepositoryInterface;
use App\Core\Game01\Domain\Models\CreditCard;
use App\Core\Game01\Domain\Models\Report;
use Illuminate\Support\Facades\DB;

class CreditCardReportRepository implements CreditCardRepositoryInterface
{
    public function findChunkByDate(
        int $year,
        int $month,
        int $limit,
        ?int $lastId
    ): array {
        $query = DB::table('subscription_reports as sr')
            ->join('subscriptions as s', 's.id', '=', 'sr.subscription_id')
            ->join('report_credit_cards as rcc', 'rcc.subscription_report_id', '=', 'sr.id')
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
                'rcc.id as report_id',
                'rcc.bank',
                'rcc.currency',
                'rcc.line',
                'rcc.used',
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
            $reports[] = new CreditCard(
                id: $row->id,
                fullName: $row->full_name,
                document: $row->document,
                email: $row->email,
                phone: $row->phone,
                period: $row->year . '-' . $row->month,
                reportId: $row->report_id,
                bank: $row->bank,
                currency: $row->currency,
                line: $row->line,
                used: $row->used,
            );
        }

        $lastRow = $rows->last();

        return [
            'items' => $reports,
            'last_id' => $lastRow->id
        ];
    }
}
