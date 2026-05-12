<?php

declare(strict_types=1);

namespace Ksfraser\TravelExpense\Tests\Unit\Service;

use Ksfraser\TravelExpense\Entity\Supplier;
use Ksfraser\TravelExpense\Service\SupplierService;
use PHPUnit\Framework\TestCase;

class SupplierServiceTest extends TestCase
{
    private SupplierService $service;

    protected function setUp(): void
    {
        $this->service = new SupplierService();
    }

    public function testCreate(): void
    {
        $data = [
            'name' => 'Hilton Hotels',
            'type' => Supplier::TYPE_HOTEL,
        ];

        $supplier = $this->service->create($data);

        $this->assertInstanceOf(Supplier::class, $supplier);
        $this->assertSame('Hilton Hotels', $supplier->getName());
        $this->assertSame(Supplier::TYPE_HOTEL, $supplier->getType());
        $this->assertNotNull($supplier->getId());
    }

    public function testGet(): void
    {
        $created = $this->service->create(['name' => 'Test', 'type' => Supplier::TYPE_HOTEL]);

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
        $supplier = $this->service->create(['name' => 'Original', 'type' => Supplier::TYPE_HOTEL]);

        $updated = $this->service->update($supplier->getId(), ['name' => 'Updated']);

        $this->assertSame('Updated', $updated->getName());
    }

    public function testDelete(): void
    {
        $supplier = $this->service->create(['name' => 'ToDelete', 'type' => Supplier::TYPE_HOTEL]);
        $id = $supplier->getId();

        $result = $this->service->delete($id);

        $this->assertTrue($result);
        $this->assertNull($this->service->get($id));
    }

    public function testFindByType(): void
    {
        $this->service->create(['name' => 'Hotel1', 'type' => Supplier::TYPE_HOTEL]);
        $this->service->create(['name' => 'Hotel2', 'type' => Supplier::TYPE_HOTEL]);
        $this->service->create(['name' => 'Taxi1', 'type' => Supplier::TYPE_TAXI]);

        $hotels = $this->service->findByType(Supplier::TYPE_HOTEL);

        $this->assertCount(2, $hotels);
    }

    public function testFindActiveByType(): void
    {
        $active = $this->service->create(['name' => 'Active Hotel', 'type' => Supplier::TYPE_HOTEL]);
        $inactive = $this->service->create(['name' => 'Inactive Hotel', 'type' => Supplier::TYPE_HOTEL]);
        $inactive->deactivate();

        $activeHotels = $this->service->findActiveByType(Supplier::TYPE_HOTEL);

        $this->assertCount(1, $activeHotels);
    }

    public function testFindPreferred(): void
    {
        $preferred = $this->service->create([
            'name' => 'Preferred Hotel',
            'type' => Supplier::TYPE_HOTEL,
            'preference_order' => 1,
        ]);
        $this->service->create([
            'name' => 'Other Hotel',
            'type' => Supplier::TYPE_HOTEL,
            'preference_order' => 5,
        ]);

        $preferredList = $this->service->findPreferred(Supplier::TYPE_HOTEL);

        $this->assertCount(1, $preferredList);
        $this->assertSame($preferred, array_values($preferredList)[0]);
    }

    public function testActivate(): void
    {
        $supplier = $this->service->create(['name' => 'Test', 'type' => Supplier::TYPE_HOTEL]);
        $supplier->deactivate();

        $result = $this->service->activate($supplier->getId());

        $this->assertTrue($result->isActive());
    }

    public function testDeactivate(): void
    {
        $supplier = $this->service->create(['name' => 'Test', 'type' => Supplier::TYPE_HOTEL]);

        $result = $this->service->deactivate($supplier->getId());

        $this->assertFalse($result->isActive());
    }

    public function testGetAll(): void
    {
        $this->service->create(['name' => 'Hotel1', 'type' => Supplier::TYPE_HOTEL]);
        $this->service->create(['name' => 'Hotel2', 'type' => Supplier::TYPE_HOTEL]);

        $all = $this->service->getAll();

        $this->assertCount(2, $all);
    }

    public function testSearch(): void
    {
        $this->service->create(['name' => 'Hilton Downtown', 'type' => Supplier::TYPE_HOTEL]);
        $this->service->create(['name' => 'Marriott', 'type' => Supplier::TYPE_HOTEL]);

        $results = $this->service->search('hilton');

        $this->assertCount(1, $results);
    }
}
