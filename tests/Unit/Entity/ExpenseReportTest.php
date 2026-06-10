<?php

declare(strict_types=1);

namespace Ksfraser\TravelExpense\Tests\Unit\Entity;

use DateTime;
use Ksfraser\TravelExpense\Entity\ExpenseReport;
use Ksfraser\TravelExpense\Entity\ExpenseLine;
use PHPUnit\Framework\TestCase;

class ExpenseReportTest extends TestCase
{
    private ExpenseReport $report;

    protected function setUp(): void
    {
        $this->report = new ExpenseReport();
    }

    public function testSetAndGetTripId(): void
    {
        $result = $this->report->setTripId('trip_123');
        $this->assertSame($this->report, $result);
        $this->assertSame('trip_123', $this->report->getTripId());
    }

    public function testSetAndGetEmployeeId(): void
    {
        $result = $this->report->setEmployeeId('emp_456');
        $this->assertSame($this->report, $result);
        $this->assertSame('emp_456', $this->report->getEmployeeId());
    }

    public function testSetAndGetTotalAmount(): void
    {
        $result = $this->report->setTotalAmount(500.00);
        $this->assertSame($this->report, $result);
        $this->assertSame(500.00, $this->report->getTotalAmount());
    }

    public function testAddExpenseLine(): void
    {
        $line = new ExpenseLine();
        $line->setAmount(100.00);

        $this->report->addExpenseLine($line);

        $this->assertCount(1, $this->report->getExpenseLines());
        $this->assertSame(100.00, $this->report->getTotalAmount());
    }

    public function testRemoveExpenseLine(): void
    {
        $line = new ExpenseLine();
        $line->setId('line_1');
        $line->setAmount(100.00);

        $this->report->addExpenseLine($line);
        $this->report->removeExpenseLine('line_1');

        $this->assertCount(0, $this->report->getExpenseLines());
        $this->assertSame(0.00, $this->report->getTotalAmount());
    }

    public function testSubmit(): void
    {
        $this->report->submit();

        $this->assertSame(ExpenseReport::STATUS_SUBMITTED, $this->report->getStatus());
        $this->assertNotNull($this->report->getSubmittedAt());
    }

    public function testApprove(): void
    {
        $this->report->approve('manager_1');

        $this->assertSame(ExpenseReport::STATUS_MANAGER_APPROVED, $this->report->getStatus());
        $this->assertSame('manager_1', $this->report->getApproverId());
        $this->assertNotNull($this->report->getApprovedAt());
    }

    public function testVerify(): void
    {
        $this->report->verify();

        $this->assertSame(ExpenseReport::STATUS_FINANCE_VERIFIED, $this->report->getStatus());
    }

    public function testReject(): void
    {
        $this->report->reject('Missing receipts');

        $this->assertSame(ExpenseReport::STATUS_REJECTED, $this->report->getStatus());
        $this->assertSame('Missing receipts', $this->report->getRejectedReason());
    }

    public function testMarkReimbursed(): void
    {
        $this->report->markReimbursed();

        $this->assertSame(ExpenseReport::STATUS_REIMBURSED, $this->report->getStatus());
        $this->assertNotNull($this->report->getReimbursedAt());
    }

    public function testToArrayReturnsAllFields(): void
    {
        $this->report->setId('rpt_123');
        $this->report->setTripId('trip_456');
        $this->report->setEmployeeId('emp_789');

        $array = $this->report->toArray();

        $this->assertSame('rpt_123', $array['id']);
        $this->assertSame('trip_456', $array['trip_id']);
        $this->assertSame('emp_789', $array['employee_id']);
    }

    public function testFromArrayCreatesReport(): void
    {
        $data = [
            'id' => 'rpt_456',
            'trip_id' => 'trip_001',
            'employee_id' => 'emp_002',
            'status' => ExpenseReport::STATUS_DRAFT,
        ];

        $report = ExpenseReport::fromArray($data);

        $this->assertSame('rpt_456', $report->getId());
        $this->assertSame('trip_001', $report->getTripId());
        $this->assertSame('emp_002', $report->getEmployeeId());
        $this->assertSame(ExpenseReport::STATUS_DRAFT, $report->getStatus());
    }
}
