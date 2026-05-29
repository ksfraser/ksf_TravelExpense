<?php

declare(strict_types=1);

namespace Ksfraser\TravelExpense\Entity;

class ExpenseLine
{
    public const CATEGORY_MEAL_BREAKFAST = 'meal_breakfast';
    public const CATEGORY_MEAL_LUNCH = 'meal_lunch';
    public const CATEGORY_MEAL_DINNER = 'meal_dinner';
    public const CATEGORY_HOTEL = 'hotel';
    public const CATEGORY_CAR_RENTAL = 'car_rental';
    public const CATEGORY_TAXI = 'taxi';
    public const CATEGORY_TRANSIT = 'transit';
    public const CATEGORY_PER_DIEM = 'per_diem';
    public const CATEGORY_OTHER = 'other';

    private ?string $id = null;
    private string $expenseReportId = '';
    private \DateTime $date;
    private string $category = self::CATEGORY_OTHER;
    private string $description = '';
    private float $amount = 0.0;
    private ?string $projectId = null;
    private ?string $taskId = null;
    private string $glCode = '';
    private ?string $receiptPath = null;
    private string $status = 'active';
    private ?\DateTime $createdAt = null;

    private static array $defaultGlCodes = [
        self::CATEGORY_MEAL_BREAKFAST => 'MEAL-BREAKFAST',
        self::CATEGORY_MEAL_LUNCH => 'MEAL-LUNCH',
        self::CATEGORY_MEAL_DINNER => 'MEAL-DINNER',
        self::CATEGORY_HOTEL => 'HOTEL',
        self::CATEGORY_CAR_RENTAL => 'CAR_RENTAL',
        self::CATEGORY_TAXI => 'TAXI',
        self::CATEGORY_TRANSIT => 'TRANSIT',
        self::CATEGORY_PER_DIEM => 'PER_DIEM',
        self::CATEGORY_OTHER => 'OTHER',
    ];

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getExpenseReportId(): string
    {
        return $this->expenseReportId;
    }

    public function setExpenseReportId(string $expenseReportId): self
    {
        $this->expenseReportId = $expenseReportId;
        return $this;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;
        if (empty($this->glCode)) {
            $this->glCode = self::$defaultGlCodes[$category] ?? 'OTHER';
        }
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative');
        }
        $this->amount = $amount;
        return $this;
    }

    public function getProjectId(): ?string
    {
        return $this->projectId;
    }

    public function setProjectId(?string $projectId): self
    {
        $this->projectId = $projectId;
        return $this;
    }

    public function getTaskId(): ?string
    {
        return $this->taskId;
    }

    public function setTaskId(?string $taskId): self
    {
        $this->taskId = $taskId;
        return $this;
    }

    public function getGlCode(): string
    {
        return $this->glCode;
    }

    public function setGlCode(string $glCode): self
    {
        $this->glCode = $glCode;
        return $this;
    }

    public function getReceiptPath(): ?string
    {
        return $this->receiptPath;
    }

    public function setReceiptPath(?string $receiptPath): self
    {
        $this->receiptPath = $receiptPath;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function isBillable(): bool
    {
        return $this->projectId !== null;
    }

    public function isMeal(): bool
    {
        return in_array($this->category, [
            self::CATEGORY_MEAL_BREAKFAST,
            self::CATEGORY_MEAL_LUNCH,
            self::CATEGORY_MEAL_DINNER,
        ]);
    }

    public function getReimbursableAmount(): float
    {
        return $this->amount;
    }

    public static function getDefaultGlCode(string $category): string
    {
        return self::$defaultGlCodes[$category] ?? 'OTHER';
    }

    public static function getMealCategories(): array
    {
        return [
            self::CATEGORY_MEAL_BREAKFAST,
            self::CATEGORY_MEAL_LUNCH,
            self::CATEGORY_MEAL_DINNER,
        ];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'expense_report_id' => $this->expenseReportId,
            'date' => $this->date->format('Y-m-d'),
            'category' => $this->category,
            'description' => $this->description,
            'amount' => $this->amount,
            'project_id' => $this->projectId,
            'task_id' => $this->taskId,
            'gl_code' => $this->glCode,
            'receipt_path' => $this->receiptPath,
            'status' => $this->status,
            'created_at' => $this->createdAt ? $this->createdAt->format('Y-m-d H:i:s') : null,
        ];
    }

    public static function fromArray(array $data): self
    {
        $line = new self();
        
        if (isset($data['id'])) $line->setId($data['id']);
        if (isset($data['expense_report_id'])) $line->setExpenseReportId($data['expense_report_id']);
        if (isset($data['date'])) $line->setDate(new \DateTime($data['date']));
        if (isset($data['category'])) $line->setCategory($data['category']);
        if (isset($data['description'])) $line->setDescription($data['description']);
        if (isset($data['amount'])) $line->setAmount((float)$data['amount']);
        if (isset($data['project_id'])) $line->setProjectId($data['project_id']);
        if (isset($data['task_id'])) $line->setTaskId($data['task_id']);
        if (isset($data['gl_code'])) $line->setGlCode($data['gl_code']);
        if (isset($data['receipt_path'])) $line->setReceiptPath($data['receipt_path']);
        if (isset($data['status'])) $line->setStatus($data['status']);
        
        return $line;
    }
}