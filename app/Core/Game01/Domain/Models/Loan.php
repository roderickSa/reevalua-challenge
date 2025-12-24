<?php

namespace App\Core\Game01\Domain\Models;

class Loan
{
    public function __construct(
        private string $id,
        private string $fullName,
        private string $document,
        private string $email,
        private string $phone,
        private string $period,
        private string $reportId,
        private string $bank,
        private string $status,
        private string $currency,
        private string $amount,
        private string $expirationDays,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getDocument(): string
    {
        return $this->document;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getPeriod(): string
    {
        return $this->period;
    }

    public function getReportId(): string
    {
        return $this->reportId;
    }

    public function getBank(): string
    {
        return $this->bank;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getExpirationDays(): string
    {
        return $this->expirationDays;
    }
}
