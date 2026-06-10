<?php

declare(strict_types=1);

namespace Ksfraser\TravelExpense\Entity;

class ExpenseReport
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_MANAGER_APPROVED = 'manager_approved';
    public const STATUS_FINANCE_VERIFIED = 'finance_verified';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_REIMBURSED = 'reimbursed';

    private ?string $id = null;
    private string $tripId = '';
    private string $employeeId = '';
    private ?\DateTime $submittedAt = null;
    private string $status = self::STATUS_DRAFT;
    private float $totalAmount = 0.0;
    private ?string $approverId = null;
    private ?\DateTime $approvedAt = null;
    private ?string $rejectedReason = null;
    private ?\DateTime $reimbursedAt = null;
    private ?\DateTime $createdAt = null;
    private ?\DateTime $updatedAt = null;

    private array $expenseLines = [];

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getTripId(): string
    {
        return $this->tripId;
    }

    public function setTripId(string $tripId): self
    {
        $this->tripId = $tripId;
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

    public function getSubmittedAt(): ?\DateTime
    {
        return $this->submittedAt;
    }

    public function setSubmittedAt(?\DateTime $submittedAt): self
    {
        $this->submittedAt = $submittedAt;
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

    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(float $totalAmount): self
    {
        $this->totalAmount = $totalAmount;
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

    public function getRejectedReason(): ?string
    {
        return $this->rejectedReason;
    }

    public function setRejectedReason(?string $reason): self
    {
        $this->rejectedReason = $reason;
        return $this;
    }

    public function getReimbursedAt(): ?\DateTime
    {
        return $this->reimbursedAt;
    }

    public function setReimbursedAt(?\DateTime $reimbursedAt): self
    {
        $this->reimbursedAt = $reimbursedAt;
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

    public function getExpenseLines(): array
    {
        return $this->expenseLines;
    }

    public function addExpenseLine(ExpenseLine $line): self
    {
        $line->setExpenseReportId($this->id ?? '');
        $this->expenseLines[] = $line;
        $this->recalculateTotal();
        return $this;
    }

    public function removeExpenseLine(string $lineId): self
    {
        $this->expenseLines = array_filter(
            $this->expenseLines,
            fn(ExpenseLine $l) => $l->getId() !== $lineId
        );
        $this->recalculateTotal();
        return $this;
    }

    private function recalculateTotal(): void
    {
        $this->totalAmount = array_reduce(
            $this->expenseLines,
            fn(float $sum, ExpenseLine $l) => $sum + $l->getAmount(),
            0.0
        );
    }

    public function submit(): self
    {
        $this->status = self::STATUS_SUBMITTED;
        $this->submittedAt = new \DateTime();
        return $this;
    }

    public function approve(string $approverId): self
    {
        $this->status = self::STATUS_MANAGER_APPROVED;
        $this->approverId = $approverId;
        $this->approvedAt = new \DateTime();
        return $this;
    }

    public function verify(): self
    {
        $this->status = self::STATUS_FINANCE_VERIFIED;
        return $this;
    }

    public function reject(string $reason): self
    {
        $this->status = self::STATUS_REJECTED;
        $this->rejectedReason = $reason;
        return $this;
    }

    public function markReimbursed(): self
    {
        $this->status = self::STATUS_REIMBURSED;
        $this->reimbursedAt = new \DateTime();
        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'trip_id' => $this->tripId,
            'employee_id' => $this->employeeId,
            'submitted_at' => $this->submittedAt?->format('Y-m-d H:i:s'),
            'status' => $this->status,
            'total_amount' => $this->totalAmount,
            'approver_id' => $this->approverId,
            'approved_at' => $this->approvedAt?->format('Y-m-d H:i:s'),
            'rejected_reason' => $this->rejectedReason,
            'reimbursed_at' => $this->reimbursedAt?->format('Y-m-d H:i:s'),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'expense_lines' => array_map(fn(ExpenseLine $l) => $l->toArray(), $this->expenseLines),
        ];
    }

    public static function fromArray(array $data): self
    {
        $report = new self();
        
        if (isset($data['id'])) $report->setId($data['id']);
        if (isset($data['trip_id'])) $report->setTripId($data['trip_id']);
        if (isset($data['employee_id'])) $report->setEmployeeId($data['employee_id']);
        if (isset($data['submitted_at'])) $report->setSubmittedAt(new \DateTime($data['submitted_at']));
        if (isset($data['status'])) $report->setStatus($data['status']);
        if (isset($data['total_amount'])) $report->setTotalAmount((float)$data['total_amount']);
        if (isset($data['approver_id'])) $report->setApproverId($data['approver_id']);
        if (isset($data['rejected_reason'])) $report->setRejectedReason($data['rejected_reason']);
        
        if (isset($data['expense_lines']) && is_array($data['expense_lines'])) {
            foreach ($data['expense_lines'] as $lineData) {
                $report->addExpenseLine(ExpenseLine::fromArray($lineData));
            }
        }
        
        return $report;
    }
}