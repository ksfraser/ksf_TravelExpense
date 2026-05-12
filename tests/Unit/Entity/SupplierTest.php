<?php

declare(strict_types=1);

namespace Ksfraser\TravelExpense\Tests\Unit\Entity;

use DateTime;
use Ksfraser\TravelExpense\Entity\Supplier;
use PHPUnit\Framework\TestCase;

class SupplierTest extends TestCase
{
    private Supplier $supplier;

    protected function setUp(): void
    {
        $this->supplier = new Supplier();
    }

    public function testSetAndGetName(): void
    {
        $result = $this->supplier->setName('Hilton Hotels');
        $this->assertSame($this->supplier, $result);
        $this->assertSame('Hilton Hotels', $this->supplier->getName());
    }

    public function testSetAndGetType(): void
    {
        $result = $this->supplier->setType(Supplier::TYPE_HOTEL);
        $this->assertSame($this->supplier, $result);
        $this->assertSame(Supplier::TYPE_HOTEL, $this->supplier->getType());
    }

    public function testSetAndGetContact(): void
    {
        $result = $this->supplier->setContact('555-1234');
        $this->assertSame($this->supplier, $result);
        $this->assertSame('555-1234', $this->supplier->getContact());
    }

    public function testSetAndGetWebsite(): void
    {
        $result = $this->supplier->setWebsite('https://hilton.com');
        $this->assertSame($this->supplier, $result);
        $this->assertSame('https://hilton.com', $this->supplier->getWebsite());
    }

    public function testSetAndGetRateCode(): void
    {
        $result = $this->supplier->setRateCode('CORP123');
        $this->assertSame($this->supplier, $result);
        $this->assertSame('CORP123', $this->supplier->getRateCode());
    }

    public function testIsPreferred(): void
    {
        $this->supplier->setPreferenceOrder(2);
        $this->assertTrue($this->supplier->isPreferred());

        $this->supplier->setPreferenceOrder(5);
        $this->assertFalse($this->supplier->isPreferred());
    }

    public function testCorporateRateAvailable(): void
    {
        $this->assertFalse($this->supplier->hasCorporateRate());
        $this->supplier->setCorporateRateAvailable(true);
        $this->assertTrue($this->supplier->hasCorporateRate());
    }

    public function testActivateDeactivate(): void
    {
        $this->assertTrue($this->supplier->isActive());
        $this->supplier->deactivate();
        $this->assertFalse($this->supplier->isActive());
        $this->supplier->activate();
        $this->assertTrue($this->supplier->isActive());
    }

    public function testToArrayReturnsAllFields(): void
    {
        $this->supplier->setId('sup_123');
        $this->supplier->setName('Marriott');
        $this->supplier->setType(Supplier::TYPE_HOTEL);
        $this->supplier->setCorporateRateAvailable(true);

        $array = $this->supplier->toArray();

        $this->assertSame('sup_123', $array['id']);
        $this->assertSame('Marriott', $array['name']);
        $this->assertSame(Supplier::TYPE_HOTEL, $array['type']);
        $this->assertTrue($array['corporate_rate_available']);
    }

    public function testFromArrayCreatesSupplier(): void
    {
        $data = [
            'id' => 'sup_456',
            'name' => 'Budget Inn',
            'type' => Supplier::TYPE_HOTEL,
            'status' => Supplier::STATUS_ACTIVE,
        ];

        $supplier = Supplier::fromArray($data);

        $this->assertSame('sup_456', $supplier->getId());
        $this->assertSame('Budget Inn', $supplier->getName());
        $this->assertSame(Supplier::TYPE_HOTEL, $supplier->getType());
    }
}
