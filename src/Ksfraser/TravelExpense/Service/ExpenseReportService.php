<?php

declare(strict_types=1);

namespace Ksfraser\TravelExpense\Service;

use Ksfraser\TravelExpense\Entity\ExpenseReport;
use Ksfraser\TravelExpense\Entity\ExpenseLine;

class ExpenseReportService
{
    private array $reports = [];
    private array $lines = [];

    public function create(array $data): ExpenseReport
    {
        $report = ExpenseReport::fromArray($data);
        $report->setId($data['id'] ?? uniqid('exp_rpt_'));
        $report->setCreatedAt(new \DateTime());
        $report->setUpdatedAt(new \DateTime());

        $this->reports[$report->getId()] = $report;

        return $report;
    }

    public function get(string $id): ?ExpenseReport
    {
        return $this->reports[$id] ?? null;
    }

    public function update(string $id, array $data): ExpenseReport
    {
        $report = $this->get($id);
        if (!$report) {
            throw new \RuntimeException("Expense report not found: {$id}");
        }

        foreach ($data as $key => $value) {
            $method = 'set' . str_replace('_', '', ucwords($key, '_'));
            if (method_exists($report, $method)) {
                $report->$method($value);
            }
        }
        $report->setUpdatedAt(new \DateTime());

        return $report;
    }

    public function delete(string $id): bool
    {
        if (!isset($this->reports[$id])) {
            return false;
        }
        unset($this->reports[$id]);
        return true;
    }

    public function findByEmployee(string $employeeId): array
    {
        return array_filter(
            $this->reports,
            fn(ExpenseReport $r) => $r->getEmployeeId() === $employeeId
        );
    }

    public function findByTrip(string $tripId): array
    {
        return array_filter(
            $this->reports,
            fn(ExpenseReport $r) => $r->getTripId() === $tripId
        );
    }

    public function findByStatus(string $status): array
    {
        return array_filter(
            $this->reports,
            fn(ExpenseReport $r) => $r->getStatus() === $status
        );
    }

    public function findPendingApproval(): array
    {
        return array_filter(
            $this->reports,
            fn(ExpenseReport $r) => $r->getStatus() === ExpenseReport::STATUS_SUBMITTED
        );
    }

    public function findPendingReimbursement(): array
    {
        return array_filter(
            $this->reports,
            fn(ExpenseReport $r) => $r->getStatus() === ExpenseReport::STATUS_FINANCE_VERIFIED
        );
    }

    public function addLine(string $reportId, array $data): ExpenseLine
    {
        $report = $this->get($reportId);
        if (!$report) {
            throw new \RuntimeException("Expense report not found: {$reportId}");
        }

        $line = ExpenseLine::fromArray($data);
        $line->setId($data['id'] ?? uniqid('exp_line_'));
        $line->setExpenseReportId($reportId);
        $line->setCreatedAt(new \DateTime());

        $this->lines[$line->getId()] = $line;
        $report->addExpenseLine($line);
        $report->setUpdatedAt(new \DateTime());

        return $line;
    }

    public function getLine(string $lineId): ?ExpenseLine
    {
        return $this->lines[$lineId] ?? null;
    }

    public function removeLine(string $reportId, string $lineId): bool
    {
        $report = $this->get($reportId);
        if (!$report) {
            throw new \RuntimeException("Expense report not found: {$reportId}");
        }

        if (!isset($this->lines[$lineId])) {
            return false;
        }

        unset($this->lines[$lineId]);
        $report->removeExpenseLine($lineId);
        $report->setUpdatedAt(new \DateTime());

        return true;
    }

    public function submit(string $reportId): ExpenseReport
    {
        $report = $this->get($reportId);
        if (!$report) {
            throw new \RuntimeException("Expense report not found: {$reportId}");
        }

        if ($report->getStatus() !== ExpenseReport::STATUS_DRAFT) {
            throw new \RuntimeException("Only draft reports can be submitted");
        }

        $report->submit();
        $report->setUpdatedAt(new \DateTime());

        return $report;
    }

    public function approve(string $reportId, string $approverId): ExpenseReport
    {
        $report = $this->get($reportId);
        if (!$report) {
            throw new \RuntimeException("Expense report not found: {$reportId}");
        }

        if ($report->getStatus() !== ExpenseReport::STATUS_SUBMITTED) {
            throw new \RuntimeException("Only submitted reports can be approved");
        }

        $report->approve($approverId);
        $report->setUpdatedAt(new \DateTime());

        return $report;
    }

    public function verify(string $reportId): ExpenseReport
    {
        $report = $this->get($reportId);
        if (!$report) {
            throw new \RuntimeException("Expense report not found: {$reportId}");
        }

        if ($report->getStatus() !== ExpenseReport::STATUS_MANAGER_APPROVED) {
            throw new \RuntimeException("Only manager-approved reports can be verified");
        }

        $report->verify();
        $report->setUpdatedAt(new \DateTime());

        return $report;
    }

    public function reject(string $reportId, string $reason): ExpenseReport
    {
        $report = $this->get($reportId);
        if (!$report) {
            throw new \RuntimeException("Expense report not found: {$reportId}");
        }

        $report->reject($reason);
        $report->setUpdatedAt(new \DateTime());

        return $report;
    }

    public function markReimbursed(string $reportId): ExpenseReport
    {
        $report = $this->get($reportId);
        if (!$report) {
            throw new \RuntimeException("Expense report not found: {$reportId}");
        }

        if ($report->getStatus() !== ExpenseReport::STATUS_FINANCE_VERIFIED) {
            throw new \RuntimeException("Only verified reports can be reimbursed");
        }

        $report->markReimbursed();
        $report->setUpdatedAt(new \DateTime());

        return $report;
    }

    public function getAll(): array
    {
        return $this->reports;
    }

    public function getTotalByEmployee(string $employeeId): float
    {
        $reports = $this->findByEmployee($employeeId);
        return array_reduce(
            $reports,
            fn(float $sum, ExpenseReport $r) => $sum + $r->getTotalAmount(),
            0.0
        );
    }

    public function getPendingByEmployee(string $employeeId): array
    {
        return array_filter(
            $this->findByEmployee($employeeId),
            fn(ExpenseReport $r) => !in_array($r->getStatus(), [
                ExpenseReport::STATUS_DRAFT,
                ExpenseReport::STATUS_REJECTED,
                ExpenseReport::STATUS_REIMBURSED
            ])
        );
    }
}
