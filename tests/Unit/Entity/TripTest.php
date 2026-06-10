<?php

declare(strict_types=1);

namespace Ksfraser\TravelExpense\Tests\Unit\Entity;

use DateTime;
use Ksfraser\TravelExpense\Entity\Trip;
use PHPUnit\Framework\TestCase;

class TripTest extends TestCase
{
    private Trip $trip;

    protected function setUp(): void
    {
        $this->trip = new Trip();
    }

    public function testSetAndGetEmployeeId(): void
    {
        $result = $this->trip->setEmployeeId('emp_123');
        $this->assertSame($this->trip, $result);
        $this->assertSame('emp_123', $this->trip->getEmployeeId());
    }

    public function testSetAndGetName(): void
    {
        $result = $this->trip->setName('Sales Conference');
        $this->assertSame($this->trip, $result);
        $this->assertSame('Sales Conference', $this->trip->getName());
    }

    public function testSetAndGetDestination(): void
    {
        $result = $this->trip->setDestination('New York');
        $this->assertSame($this->trip, $result);
        $this->assertSame('New York', $this->trip->getDestination());
    }

    public function testCalculateDuration(): void
    {
        $this->trip->setStartDate(new DateTime('2024-01-01'));
        $this->trip->setEndDate(new DateTime('2024-01-05'));

        $this->assertSame(5, $this->trip->calculateDuration());
    }

    public function testIsOverBudget(): void
    {
        $this->trip->setBudgetApproved(1000.00);
        $this->trip->setExpensesTotal(500.00);
        $this->assertFalse($this->trip->isOverBudget());

        $this->trip->setExpensesTotal(1500.00);
        $this->assertTrue($this->trip->isOverBudget());
    }

    public function testGetRemainingBudget(): void
    {
        $this->trip->setBudgetApproved(1000.00);
        $this->trip->setExpensesTotal(300.00);

        $this->assertSame(700.00, $this->trip->getRemainingBudget());
    }

    public function testApprove(): void
    {
        $this->trip->approve('manager_1');

        $this->assertSame(Trip::STATUS_APPROVED, $this->trip->getStatus());
        $this->assertSame('manager_1', $this->trip->getApproverId());
        $this->assertNotNull($this->trip->getApprovedAt());
    }

    public function testReject(): void
    {
        $this->trip->reject('Budget exceeded');

        $this->assertSame(Trip::STATUS_REJECTED, $this->trip->getStatus());
        $this->assertSame('Budget exceeded', $this->trip->getRejectionReason());
    }

    public function testStart(): void
    {
        $this->trip->start();

        $this->assertSame(Trip::STATUS_IN_PROGRESS, $this->trip->getStatus());
    }

    public function testComplete(): void
    {
        $this->trip->complete();

        $this->assertSame(Trip::STATUS_COMPLETE, $this->trip->getStatus());
    }

    public function testCancel(): void
    {
        $this->trip->cancel();

        $this->assertSame(Trip::STATUS_CANCELLED, $this->trip->getStatus());
    }

    public function testToArrayReturnsAllFields(): void
    {
        $this->trip->setId('trip_123');
        $this->trip->setEmployeeId('emp_456');
        $this->trip->setName('Annual Summit');
        $this->trip->setDestination('Chicago');
        $this->trip->setBudgetApproved(2000.00);

        $array = $this->trip->toArray();

        $this->assertSame('trip_123', $array['id']);
        $this->assertSame('emp_456', $array['employee_id']);
        $this->assertSame('Annual Summit', $array['name']);
        $this->assertSame('Chicago', $array['destination']);
        $this->assertSame(2000.00, $array['budget_approved']);
    }

    public function testFromArrayCreatesTrip(): void
    {
        $data = [
            'id' => 'trip_789',
            'employee_id' => 'emp_001',
            'name' => 'Training',
            'destination' => 'Boston',
            'start_date' => '2024-03-01',
            'end_date' => '2024-03-05',
        ];

        $trip = Trip::fromArray($data);

        $this->assertSame('trip_789', $trip->getId());
        $this->assertSame('emp_001', $trip->getEmployeeId());
        $this->assertSame('Training', $trip->getName());
        $this->assertSame('Boston', $trip->getDestination());
    }
}
