<?php

declare(strict_types=1);

namespace Ksfraser\TravelExpense\Tests\Unit\Entity;

use DateTime;
use Ksfraser\TravelExpense\Entity\ExpenseLine;
use PHPUnit\Framework\TestCase;

class ExpenseLineTest extends TestCase
{
    private ExpenseLine $line;

    protected function setUp(): void
    {
        $this->line = new ExpenseLine();
    }

    public function testSetAndGetDate(): void
    {
        $date = new DateTime('2024-01-15');
        $result = $this->line->setDate($date);
        $this->assertSame($this->line, $result);
        $this->assertSame($date, $this->line->getDate());
    }

    public function testSetAndGetCategory(): void
    {
        $result = $this->line->setCategory(ExpenseLine::CATEGORY_HOTEL);
        $this->assertSame($this->line, $result);
        $this->assertSame(ExpenseLine::CATEGORY_HOTEL, $this->line->getCategory());
        $this->assertSame('HOTEL', $this->line->getGlCode());
    }

    public function testSetAndGetDescription(): void
    {
        $result = $this->line->setDescription('Hotel stay');
        $this->assertSame($this->line, $result);
        $this->assertSame('Hotel stay', $this->line->getDescription());
    }

    public function testSetAndGetAmount(): void
    {
        $result = $this->line->setAmount(150.00);
        $this->assertSame($this->line, $result);
        $this->assertSame(150.00, $this->line->getAmount());
    }

    public function testNegativeAmountThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->line->setAmount(-50.00);
    }

    public function testSetAndGetProjectId(): void
    {
        $result = $this->line->setProjectId('proj_123');
        $this->assertSame($this->line, $result);
        $this->assertSame('proj_123', $this->line->getProjectId());
    }

    public function testSetAndGetGlCode(): void
    {
        $result = $this->line->setGlCode('CUSTOM_GL');
        $this->assertSame($this->line, $result);
        $this->assertSame('CUSTOM_GL', $this->line->getGlCode());
    }

    public function testIsBillable(): void
    {
        $this->assertFalse($this->line->isBillable());
        $this->line->setProjectId('proj_001');
        $this->assertTrue($this->line->isBillable());
    }

    public function testIsMeal(): void
    {
        $this->assertFalse($this->line->isMeal());
        $this->line->setCategory(ExpenseLine::CATEGORY_MEAL_BREAKFAST);
        $this->assertTrue($this->line->isMeal());
        $this->line->setCategory(ExpenseLine::CATEGORY_HOTEL);
        $this->assertFalse($this->line->isMeal());
    }

    public function testGetDefaultGlCode(): void
    {
        $this->assertSame('HOTEL', ExpenseLine::getDefaultGlCode(ExpenseLine::CATEGORY_HOTEL));
        $this->assertSame('TAXI', ExpenseLine::getDefaultGlCode(ExpenseLine::CATEGORY_TAXI));
        $this->assertSame('OTHER', ExpenseLine::getDefaultGlCode('unknown'));
    }

    public function testGetMealCategories(): void
    {
        $categories = ExpenseLine::getMealCategories();

        $this->assertContains(ExpenseLine::CATEGORY_MEAL_BREAKFAST, $categories);
        $this->assertContains(ExpenseLine::CATEGORY_MEAL_LUNCH, $categories);
        $this->assertContains(ExpenseLine::CATEGORY_MEAL_DINNER, $categories);
    }

    public function testToArrayReturnsAllFields(): void
    {
        $this->line->setId('line_123');
        $this->line->setExpenseReportId('rpt_456');
        $this->line->setCategory(ExpenseLine::CATEGORY_TRANSIT);
        $this->line->setAmount(45.00);

        $array = $this->line->toArray();

        $this->assertSame('line_123', $array['id']);
        $this->assertSame('rpt_456', $array['expense_report_id']);
        $this->assertSame(ExpenseLine::CATEGORY_TRANSIT, $array['category']);
        $this->assertSame(45.00, $array['amount']);
    }

    public function testFromArrayCreatesLine(): void
    {
        $data = [
            'id' => 'line_789',
            'expense_report_id' => 'rpt_001',
            'category' => ExpenseLine::CATEGORY_CAR_RENTAL,
            'amount' => 200.00,
            'description' => 'Enterprise rental',
        ];

        $line = ExpenseLine::fromArray($data);

        $this->assertSame('line_789', $line->getId());
        $this->assertSame('rpt_001', $line->getExpenseReportId());
        $this->assertSame(ExpenseLine::CATEGORY_CAR_RENTAL, $line->getCategory());
        $this->assertSame(200.00, $line->getAmount());
        $this->assertSame('Enterprise rental', $line->getDescription());
    }
}
