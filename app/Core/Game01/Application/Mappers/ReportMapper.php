<?php

namespace App\Core\Game01\Application\Mappers;

use App\Core\Game01\Domain\Models\Loan;
use App\Core\Game01\Domain\Models\CreditCard;
use App\Core\Game01\Domain\Models\OtherDebt;
use App\Core\Game01\Domain\Models\Report;

class ReportMapper
{
    public function fromLoan(Loan $loan, string $today): Report
    {
        return new Report(
            id: $loan->getReportId(),
            fullName: $loan->getFullName(),
            dni: $loan->getDocument(),
            email: $loan->getEmail(),
            phone: $loan->getPhone(),
            company: $loan->getBank(),
            debtType: 'Préstamo',
            condition: $loan->getStatus(),
            delay: (int) $loan->getExpirationDays(),
            entity: $loan->getBank(),
            totalAmount: (float) $loan->getAmount(),
            totalLine: 0.0,
            usedLine: 0.0,
            period: $loan->getPeriod(),
            reportDate: $today,
            status: $loan->getStatus()
        );
    }

    public function fromOtherDebt(OtherDebt $debt, string $today): Report
    {
        return new Report(
            id: $debt->getReportId(),
            fullName: $debt->getFullName(),
            dni: $debt->getDocument(),
            email: $debt->getEmail(),
            phone: $debt->getPhone(),
            company: $debt->getEntity(),
            debtType: 'Otra deuda',
            condition: 'N/A',
            delay: $debt->getExpirationDays(),
            entity: $debt->getEntity(),
            totalAmount: $debt->getAmount(),
            totalLine: 0.0,
            usedLine: 0.0,
            period: $debt->getPeriod(),
            reportDate: $today,
            status: 'N/A'
        );
    }

    public function fromCreditCard(CreditCard $card, string $today): Report
    {
        return new Report(
            id: $card->getReportId(),
            fullName: $card->getFullName(),
            dni: $card->getDocument(),
            email: $card->getEmail(),
            phone: $card->getPhone(),
            company: $card->getBank(),
            debtType: 'Tarjeta de Crédito',
            condition: 'N/A',
            delay: 0,
            entity: $card->getBank(),
            totalAmount: 0.0,
            totalLine: $card->getLine(),
            usedLine: $card->getUsed(),
            period: $card->getPeriod(),
            reportDate: $today,
            status: 'N/A'
        );
    }
}
