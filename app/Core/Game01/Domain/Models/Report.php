<?php

namespace App\Core\Game01\Domain\Models;

class Report
{
    public function __construct(
        private string $id,
        private string $fullName,
        private string $dni,
        private string $email,
        private string $phone,
        private string $company,
        private string $debtType,
        private string $condition,
        private int $delay,
        private string $entity,
        private float $totalAmount,
        private float $totalLine,
        private float $usedLine,
        private string $period,
        private String $reportDate,
        private string $status
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getDni(): string
    {
        return $this->dni;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function getDebtType(): string
    {
        return $this->debtType;
    }

    public function getCondition(): string
    {
        return $this->condition;
    }

    public function getDelay(): int
    {
        return $this->delay;
    }

    public function getEntity(): string
    {
        return $this->entity;
    }

    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    public function getTotalLine(): float
    {
        return $this->totalLine;
    }

    public function getUsedLine(): float
    {
        return $this->usedLine;
    }

    public function getPeriod(): string
    {
        return $this->period;
    }

    public function getReportDate(): string
    {
        return $this->reportDate;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function toArray(): array
    {
        return [
            'id'           => $this->id,
            'full_name'    => $this->fullName,
            'dni'          => $this->dni,
            'email'        => $this->email,
            'phone'        => $this->phone,
            'company'      => $this->company,
            'debt_type'    => $this->debtType,
            'condition'    => $this->condition,
            'delay'        => $this->delay,
            'entity'       => $this->entity,
            'total_amount' => $this->totalAmount,
            'total_line'   => $this->totalLine,
            'used_line'    => $this->usedLine,
            'period'       => $this->period,
            'report_date'  => $this->reportDate,
            'status'       => $this->status,
        ];
    }
}
