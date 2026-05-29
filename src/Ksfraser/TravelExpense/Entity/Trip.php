<?php

declare(strict_types=1);

namespace Ksfraser\TravelExpense\Entity;

class Trip
{
    public const STATUS_PLANNED = 'planned';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETE = 'complete';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';

    private ?string $id = null;
    private string $employeeId = '';
    private string $name = '';
    private \DateTime $startDate;
    private \DateTime $endDate;
    private string $destination = '';
    private ?string $projectId = null;
    private string $status = self::STATUS_PLANNED;
    private float $budgetApproved = 0.0;
    private float $expensesTotal = 0.0;
    private ?string $approverId = null;
    private ?\DateTime $approvedAt = null;
    private ?string $rejectionReason = null;
    private ?\DateTime $createdAt = null;
    private ?\DateTime $updatedAt = null;

    public function __construct()
    {
        $this->startDate = new \DateTime();
        $this->endDate = new \DateTime();
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

    public function getEmployeeId(): string
    {
        return $this->employeeId;
    }

    public function setEmployeeId(string $employeeId): self
    {
        $this->employeeId = $employeeId;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTime $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): \DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTime $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function getDestination(): string
    {
        return $this->destination;
    }

    public function setDestination(string $destination): self
    {
        $this->destination = $destination;
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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getBudgetApproved(): float
    {
        return $this->budgetApproved;
    }

    public function setBudgetApproved(float $budgetApproved): self
    {
        $this->budgetApproved = $budgetApproved;
        return $this;
    }

    public function getExpensesTotal(): float
    {
        return $this->expensesTotal;
    }

    public function setExpensesTotal(float $expensesTotal): self
    {
        $this->expensesTotal = $expensesTotal;
        return $this;
    }

    public function addExpense(float $amount): self
    {
        $this->expensesTotal += $amount;
        return $this;
    }

    public function getApproverId(): ?string
    {
        return $this->approverId;
    }

    public function setApproverId(?string $approverId): self
    {
        $this->approverId = $approverId;
        return $this;
    }

    public function getApprovedAt(): ?\DateTime
    {
        return $this->approvedAt;
    }

    public function setApprovedAt(?\DateTime $approvedAt): self
    {
        $this->approvedAt = $approvedAt;
        return $this;
    }

    public function getRejectionReason(): ?string
    {
        return $this->rejectionReason;
    }

    public function setRejectionReason(?string $reason): self
    {
        $this->rejectionReason = $reason;
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

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function calculateDuration(): int
    {
        return $this->endDate->diff($this->startDate)->days + 1;
    }

    public function isOverBudget(): bool
    {
        return $this->expensesTotal > $this->budgetApproved && $this->budgetApproved > 0;
    }

    public function getRemainingBudget(): float
    {
        return $this->budgetApproved - $this->expensesTotal;
    }

    public function approve(string $approverId): self
    {
        $this->status = self::STATUS_APPROVED;
        $this->approverId = $approverId;
        $this->approvedAt = new \DateTime();
        return $this;
    }

    public function reject(string $reason): self
    {
        $this->status = self::STATUS_REJECTED;
        $this->rejectionReason = $reason;
        return $this;
    }

    public function cancel(): self
    {
        $this->status = self::STATUS_CANCELLED;
        return $this;
    }

    public function start(): self
    {
        $this->status = self::STATUS_IN_PROGRESS;
        return $this;
    }

    public function complete(): self
    {
        $this->status = self::STATUS_COMPLETE;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employeeId,
            'name' => $this->name,
            'start_date' => $this->startDate->format('Y-m-d'),
            'end_date' => $this->endDate->format('Y-m-d'),
            'destination' => $this->destination,
            'project_id' => $this->projectId,
            'status' => $this->status,
            'budget_approved' => $this->budgetApproved,
            'expenses_total' => $this->expensesTotal,
            'approver_id' => $this->approverId,
            'approved_at' => $this->approvedAt ? $this->approvedAt->format('Y-m-d H:i:s') : null,
            'rejection_reason' => $this->rejectionReason,
            'created_at' => $this->createdAt ? $this->createdAt->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updatedAt ? $this->updatedAt->format('Y-m-d H:i:s') : null,
        ];
    }

    public static function fromArray(array $data): self
    {
        $trip = new self();
        
        if (isset($data['id'])) $trip->setId($data['id']);
        if (isset($data['employee_id'])) $trip->setEmployeeId($data['employee_id']);
        if (isset($data['name'])) $trip->setName($data['name']);
        if (isset($data['start_date'])) $trip->setStartDate(new \DateTime($data['start_date']));
        if (isset($data['end_date'])) $trip->setEndDate(new \DateTime($data['end_date']));
        if (isset($data['destination'])) $trip->setDestination($data['destination']);
        if (isset($data['project_id'])) $trip->setProjectId($data['project_id']);
        if (isset($data['status'])) $trip->setStatus($data['status']);
        if (isset($data['budget_approved'])) $trip->setBudgetApproved((float)$data['budget_approved']);
        if (isset($data['expenses_total'])) $trip->setExpensesTotal((float)$data['expenses_total']);
        if (isset($data['approver_id'])) $trip->setApproverId($data['approver_id']);
        if (isset($data['rejection_reason'])) $trip->setRejectionReason($data['rejection_reason']);
        
        return $trip;
    }
}