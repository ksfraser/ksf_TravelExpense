<?php

declare(strict_types=1);

namespace Ksfraser\TravelExpense\Tests\Unit\Service;

use Ksfraser\TravelExpense\Entity\ExpenseReport;
use Ksfraser\TravelExpense\Entity\ExpenseLine;
use Ksfraser\TravelExpense\Service\ExpenseReportService;
use PHPUnit\Framework\TestCase;

class ExpenseReportServiceTest extends TestCase
{
    private ExpenseReportService $service;

    protected function setUp(): void
    {
        $this->service = new ExpenseReportService();
    }

    public function testCreate(): void
    {
        $data = [
            'trip_id' => 'trip_123',
            'employee_id' => 'emp_456',
        ];

        $report = $this->service->create($data);

        $this->assertInstanceOf(ExpenseReport::class, $report);
        $this->assertSame('trip_123', $report->getTripId());
        $this->assertSame('emp_456', $report->getEmployeeId());
        $this->assertNotNull($report->getId());
    }

    public function testGet(): void
    {
        $created = $this->service->create([
            'trip_id' => 'trip_123',
            'employee_id' => 'emp_456',
        ]);

        $retrieved = $this->service->get($created->getId());

        $this->assertSame($created, $retrieved);
    }

    public function testGetReturnsNullForNonexistent(): void
    {
        $result = $this->service->get('nonexistent');
        $this->assertNull($result);
    }

    public function testUpdate(): void
    {
        $report = $this->service->create([
            'trip_id' => 'trip_123',
            'employee_id' => 'emp_456',
        ]);

        $updated = $this->service->update($report->getId(), [
            'trip_id' => 'trip_789',
        ]);

        $this->assertSame('trip_789', $updated->getTripId());
    }

    public function testDelete(): void
    {
        $report = $this->service->create([
            'trip_id' => 'trip_123',
            'employee_id' => 'emp_456',
        ]);
        $id = $report->getId();

        $result = $this->service->delete($id);

        $this->assertTrue($result);
        $this->assertNull($this->service->get($id));
    }

    public function testFindByEmployee(): void
    {
        $this->service->create(['trip_id' => 'trip_1', 'employee_id' => 'emp_1']);
        $this->service->create(['trip_id' => 'trip_2', 'employee_id' => 'emp_2']);
        $this->service->create(['trip_id' => 'trip_3', 'employee_id' => 'emp_1']);

        $emp1Reports = $this->service->findByEmployee('emp_1');

        $this->assertCount(2, $emp1Reports);
    }

    public function testFindByTrip(): void
    {
        $this->service->create(['trip_id' => 'trip_123', 'employee_id' => 'emp_1']);
        $this->service->create(['trip_id' => 'trip_123', 'employee_id' => 'emp_2']);
        $this->service->create(['trip_id' => 'trip_456', 'employee_id' => 'emp_3']);

        $tripReports = $this->service->findByTrip('trip_123');

        $this->assertCount(2, $tripReports);
    }

    public function testFindByStatus(): void
    {
        $draft = $this->service->create(['trip_id' => 'trip_1', 'employee_id' => 'emp_1']);
        $submitted = $this->service->create(['trip_id' => 'trip_2', 'employee_id' => 'emp_2']);
        $submitted->submit();

        $draftReports = $this->service->findByStatus(ExpenseReport::STATUS_DRAFT);
        $submittedReports = $this->service->findByStatus(ExpenseReport::STATUS_SUBMITTED);

        $this->assertCount(1, $draftReports);
        $this->assertCount(1, $submittedReports);
    }

    public function testAddLine(): void
    {
        $report = $this->service->create([
            'trip_id' => 'trip_123',
            'employee_id' => 'emp_456',
        ]);

        $line = $this->service->addLine($report->getId(), [
            'category' => ExpenseLine::CATEGORY_HOTEL,
            'amount' => 150.00,
            'description' => 'Hilton stay',
        ]);

        $this->assertInstanceOf(ExpenseLine::class, $line);
        $this->assertSame(150.00, $line->getAmount());
        $this->assertSame($report->getId(), $line->getExpenseReportId());
    }

    public function testRemoveLine(): void
    {
        $report = $this->service->create([
            'trip_id' => 'trip_123',
            'employee_id' => 'emp_456',
        ]);
        $line = $this->service->addLine($report->getId(), [
            'category' => ExpenseLine::CATEGORY_TAXI,
            'amount' => 50.00,
        ]);

        $result = $this->service->removeLine($report->getId(), $line->getId());

        $this->assertTrue($result);
        $this->assertNull($this->service->getLine($line->getId()));
    }

    public function testSubmit(): void
    {
        $report = $this->service->create([
            'trip_id' => 'trip_123',
            'employee_id' => 'emp_456',
        ]);

        $result = $this->service->submit($report->getId());

        $this->assertSame(ExpenseReport::STATUS_SUBMITTED, $result->getStatus());
        $this->assertNotNull($result->getSubmittedAt());
    }

    public function testApprove(): void
    {
        $report = $this->service->create([
            'trip_id' => 'trip_123',
            'employee_id' => 'emp_456',
        ]);
        $report->submit();

        $result = $this->service->approve($report->getId(), 'manager_1');

        $this->assertSame(ExpenseReport::STATUS_MANAGER_APPROVED, $result->getStatus());
        $this->assertSame('manager_1', $result->getApproverId());
    }

    public function testVerify(): void
    {
        $report = $this->service->create([
            'trip_id' => 'trip_123',
            'employee_id' => 'emp_456',
        ]);
        $report->submit();
        $report->approve('manager_1');

        $result = $this->service->verify($report->getId());

        $this->assertSame(ExpenseReport::STATUS_FINANCE_VERIFIED, $result->getStatus());
    }

    public function testReject(): void
    {
        $report = $this->service->create([
            'trip_id' => 'trip_123',
            'employee_id' => 'emp_456',
        ]);
        $report->submit();

        $result = $this->service->reject($report->getId(), 'Missing receipts');

        $this->assertSame(ExpenseReport::STATUS_REJECTED, $result->getStatus());
        $this->assertSame('Missing receipts', $result->getRejectedReason());
    }

    public function testMarkReimbursed(): void
    {
        $report = $this->service->create([
            'trip_id' => 'trip_123',
            'employee_id' => 'emp_456',
        ]);
        $report->submit();
        $report->approve('manager_1');
        $report->verify();

        $result = $this->service->markReimbursed($report->getId());

        $this->assertSame(ExpenseReport::STATUS_REIMBURSED, $result->getStatus());
        $this->assertNotNull($result->getReimbursedAt());
    }

    public function testFindPendingApproval(): void
    {
        $draft = $this->service->create(['trip_id' => 'trip_1', 'employee_id' => 'emp_1']);
        $submitted = $this->service->create(['trip_id' => 'trip_2', 'employee_id' => 'emp_2']);
        $submitted->submit();
        $approved = $this->service->create(['trip_id' => 'trip_3', 'employee_id' => 'emp_3']);
        $approved->submit();
        $approved->approve('manager_1');

        $pending = $this->service->findPendingApproval();

        $this->assertCount(1, $pending);
    }

    public function testGetTotalByEmployee(): void
    {
        $r1 = $this->service->create(['trip_id' => 'trip_1', 'employee_id' => 'emp_1']);
        $r2 = $this->service->create(['trip_id' => 'trip_2', 'employee_id' => 'emp_1']);
        $this->service->addLine($r1->getId(), ['amount' => 100.00, 'category' => ExpenseLine::CATEGORY_OTHER]);
        $this->service->addLine($r2->getId(), ['amount' => 200.00, 'category' => ExpenseLine::CATEGORY_OTHER]);

        $total = $this->service->getTotalByEmployee('emp_1');

        $this->assertSame(300.00, $total);
    }

    public function testSubmitOnlyDraftThrows(): void
    {
        $report = $this->service->create([
            'trip_id' => 'trip_123',
            'employee_id' => 'emp_456',
        ]);
        $report->submit();

        $this->expectException(\RuntimeException::class);
        $this->service->submit($report->getId());
    }
}
