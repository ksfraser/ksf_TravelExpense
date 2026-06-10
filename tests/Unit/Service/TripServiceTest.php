<?php

declare(strict_types=1);

namespace Ksfraser\TravelExpense\Tests\Unit\Service;

use DateTime;
use Ksfraser\TravelExpense\Entity\Trip;
use Ksfraser\TravelExpense\Service\TripService;
use PHPUnit\Framework\TestCase;

class TripServiceTest extends TestCase
{
    private TripService $service;

    protected function setUp(): void
    {
        $this->service = new TripService();
    }

    public function testCreate(): void
    {
        $data = [
            'employee_id' => 'emp_123',
            'name' => 'Sales Conference',
            'destination' => 'New York',
            'start_date' => '2024-06-01',
            'end_date' => '2024-06-05',
        ];

        $trip = $this->service->create($data);

        $this->assertInstanceOf(Trip::class, $trip);
        $this->assertSame('emp_123', $trip->getEmployeeId());
        $this->assertSame('Sales Conference', $trip->getName());
        $this->assertSame('New York', $trip->getDestination());
        $this->assertNotNull($trip->getId());
    }

    public function testGet(): void
    {
        $created = $this->service->create([
            'employee_id' => 'emp_123',
            'name' => 'Test Trip',
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
        $trip = $this->service->create([
            'employee_id' => 'emp_123',
            'name' => 'Original',
        ]);

        $updated = $this->service->update($trip->getId(), ['name' => 'Updated']);

        $this->assertSame('Updated', $updated->getName());
    }

    public function testDelete(): void
    {
        $trip = $this->service->create([
            'employee_id' => 'emp_123',
            'name' => 'ToDelete',
        ]);
        $id = $trip->getId();

        $result = $this->service->delete($id);

        $this->assertTrue($result);
        $this->assertNull($this->service->get($id));
    }

    public function testFindByEmployee(): void
    {
        $this->service->create(['employee_id' => 'emp_1', 'name' => 'Trip1']);
        $this->service->create(['employee_id' => 'emp_2', 'name' => 'Trip2']);
        $this->service->create(['employee_id' => 'emp_1', 'name' => 'Trip3']);

        $emp1Trips = $this->service->findByEmployee('emp_1');

        $this->assertCount(2, $emp1Trips);
    }

    public function testFindByStatus(): void
    {
        $planned = $this->service->create(['employee_id' => 'emp_1', 'name' => 'Planned']);
        $approved = $this->service->create(['employee_id' => 'emp_2', 'name' => 'Approved']);
        $approved->approve('manager_1');

        $plannedTrips = $this->service->findByStatus(Trip::STATUS_PLANNED);
        $approvedTrips = $this->service->findByStatus(Trip::STATUS_APPROVED);

        $this->assertCount(1, $plannedTrips);
        $this->assertCount(1, $approvedTrips);
    }

    public function testFindByProject(): void
    {
        $this->service->create([
            'employee_id' => 'emp_1',
            'name' => 'Trip1',
            'project_id' => 'proj_123',
        ]);
        $this->service->create([
            'employee_id' => 'emp_2',
            'name' => 'Trip2',
        ]);

        $projectTrips = $this->service->findByProject('proj_123');

        $this->assertCount(1, $projectTrips);
    }

    public function testApprove(): void
    {
        $trip = $this->service->create([
            'employee_id' => 'emp_1',
            'name' => 'Test Trip',
        ]);

        $result = $this->service->approve($trip->getId(), 'manager_1');

        $this->assertSame(Trip::STATUS_APPROVED, $result->getStatus());
        $this->assertSame('manager_1', $result->getApproverId());
    }

    public function testReject(): void
    {
        $trip = $this->service->create([
            'employee_id' => 'emp_1',
            'name' => 'Test Trip',
        ]);

        $result = $this->service->reject($trip->getId(), 'Budget exceeded');

        $this->assertSame(Trip::STATUS_REJECTED, $result->getStatus());
        $this->assertSame('Budget exceeded', $result->getRejectionReason());
    }

    public function testStart(): void
    {
        $trip = $this->service->create([
            'employee_id' => 'emp_1',
            'name' => 'Test Trip',
        ]);
        $trip->approve('manager_1');

        $result = $this->service->start($trip->getId());

        $this->assertSame(Trip::STATUS_IN_PROGRESS, $result->getStatus());
    }

    public function testComplete(): void
    {
        $trip = $this->service->create([
            'employee_id' => 'emp_1',
            'name' => 'Test Trip',
        ]);
        $trip->approve('manager_1');
        $trip->start();

        $result = $this->service->complete($trip->getId());

        $this->assertSame(Trip::STATUS_COMPLETE, $result->getStatus());
    }

    public function testCancel(): void
    {
        $trip = $this->service->create([
            'employee_id' => 'emp_1',
            'name' => 'Test Trip',
        ]);

        $result = $this->service->cancel($trip->getId());

        $this->assertSame(Trip::STATUS_CANCELLED, $result->getStatus());
    }

    public function testAddExpense(): void
    {
        $trip = $this->service->create([
            'employee_id' => 'emp_1',
            'name' => 'Test Trip',
            'budget_approved' => 1000.00,
        ]);

        $result = $this->service->addExpense($trip->getId(), 250.00);

        $this->assertSame(250.00, $result->getExpensesTotal());
    }

    public function testFindOverBudget(): void
    {
        $overBudget = $this->service->create([
            'employee_id' => 'emp_1',
            'name' => 'Over Budget',
            'budget_approved' => 500.00,
            'expenses_total' => 600.00,
        ]);
        $this->service->create([
            'employee_id' => 'emp_2',
            'name' => 'Under Budget',
            'budget_approved' => 500.00,
            'expenses_total' => 400.00,
        ]);

        $overBudgetTrips = $this->service->findOverBudget();

        $this->assertCount(1, $overBudgetTrips);
    }

    public function testSearch(): void
    {
        $this->service->create(['employee_id' => 'emp_1', 'name' => 'New York Conference']);
        $this->service->create(['employee_id' => 'emp_2', 'name' => 'Chicago Training']);

        $results = $this->service->search('new york');

        $this->assertCount(1, $results);
    }
}
