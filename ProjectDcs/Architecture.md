# Architecture - ksf_TravelExpense

## Document Information
- **Module**: ksf_TravelExpense
- **Version**: 1.0.0
- **Date**: 2026-05-11
- **Status**: Proposed
- **Author**: KSFII Development Team

---

## 1. Module Overview

ksf_TravelExpense provides travel and expense tracking including supplier management, trip scheduling, and expense reporting with GL integration.

### 1.1 Namespace
```php
Ksfraser\TravelExpense\
```

### 1.2 Layer Pattern
```
ksf_TravelExpense/         → Business Logic
    ├── Entity/            → Domain entities
    ├── Service/           → Business services
    ├── Repository/        → Data access interfaces
    ├── Contract/          → Interfaces for adapters
    └── Exception/         → Domain exceptions
```

---

## 2. Core Entities

### 2.1 Supplier

```php
class Supplier {
    private string $id;
    private string $name;
    private SupplierType $type;      // hotel, car_rental, taxi, transit, meal, other
    private string $contact;
    private string $website;
    private ?string $rateCode;
    private int $preferenceOrder;   // 1 = preferred
    private bool $corporateRateAvailable;
    private SupplierStatus $status;
    
    // Methods
    public function isPreferred(): bool;
    public function getRate(): ?float;
}
```

### 2.2 Trip

```php
class Trip {
    private string $id;
    private string $employeeId;
    private string $name;
    private \DateTime $startDate;
    private \DateTime $endDate;
    private string $destination;
    private ?string $projectId;
    private TripStatus $status;
    private float $budgetApproved;
    private float $expensesTotal;
    private ?string $approverId;
    private ?\DateTime $approvedAt;
    
    // Methods
    public function calculateDuration(): int;
    public function calculatePerDiemTotal(): float;
    public function isOverBudget(): bool;
    public function getRemainingBudget(): float;
}
```

### 2.3 ExpenseReport

```php
class ExpenseReport {
    private string $id;
    private string $tripId;
    private string $employeeId;
    private \DateTime $submittedAt;
    private ExpenseReportStatus $status;
    private float $totalAmount;
    private ?string $approverId;
    private ?\DateTime $approvedAt;
    private ?string $rejectedReason;
    
    // Collection
    private Collection $expenseLines;
    
    // Methods
    public function addExpenseLine(ExpenseLine $line): self;
    public function calculateTotal(): float;
    public function submit(): self;
    public function approve(string $approverId): self;
    public function reject(string $reason): self;
}
```

### 2.4 ExpenseLine

```php
class ExpenseLine {
    private string $id;
    private string $expenseReportId;
    private \DateTime $date;
    private ExpenseCategory $category;
    private string $description;
    private float $amount;
    private ?string $projectId;
    private ?string $taskId;
    private string $glCode;
    private ?string $receiptPath;
    private ExpenseLineStatus $status;
    
    // Methods
    public function isBillable(): bool;
    public function getReimbursableAmount(): float;
}
```

---

## 3. Enumerations

### 3.1 SupplierType
```php
enum SupplierType: string {
    case Hotel = 'hotel';
    case CarRental = 'car_rental';
    case Taxi = 'taxi';
    case Transit = 'transit';
    case Meal = 'meal';
    case Other = 'other';
}
```

### 3.2 ExpenseCategory
```php
enum ExpenseCategory: string {
    case MealBreakfast = 'meal_breakfast';
    case MealLunch = 'meal_lunch';
    case MealDinner = 'meal_dinner';
    case Hotel = 'hotel';
    case CarRental = 'car_rental';
    case Taxi = 'taxi';
    case Transit = 'transit';
    case PerDiem = 'per_diem';
    case Other = 'other';
    
    public function getGlCode(): string;
}
```

### 3.3 TripStatus
```php
enum TripStatus: string {
    case Planned = 'planned';
    case Approved = 'approved';
    case InProgress = 'in_progress';
    case Complete = 'complete';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';
}
```

### 3.4 ExpenseReportStatus
```php
enum ExpenseReportStatus: string {
    case Draft = 'draft';
    case Submitted = 'submitted';
    case ManagerApproved = 'manager_approved';
    case FinanceVerified = 'finance_verified';
    case Rejected = 'rejected';
    case Reimbursed = 'reimbursed';
}
```

---

## 4. Service Layer

### 4.1 SupplierService

| Method | Description |
|--------|-------------|
| `createSupplier(array $data): Supplier` | Add new supplier |
| `getSupplier(string $id): ?Supplier` | Get supplier |
| `getSuppliersByType(SupplierType $type): array` | Filter by type |
| `getPreferredSuppliers(SupplierType $type): array` | Get preferred list |
| `updateSupplier(string $id, array $data): Supplier` | Update supplier |
| `deactivateSupplier(string $id): bool` | Deactivate supplier |

### 4.2 TripService

| Method | Description |
|--------|-------------|
| `createTrip(string $employeeId, array $data): Trip` | Create trip |
| `getTrip(string $id): ?Trip` | Get trip |
| `approveTrip(string $tripId, string $approverId): Trip` | Approve |
| `rejectTrip(string $tripId, string $reason): Trip` | Reject |
| `getEmployeeTrips(string $employeeId): array` | List trips |
| `calculatePerDiem(string $tripId): float` | Calculate per diem |
| `linkToProject(string $tripId, string $projectId): Trip` | Link to project |

### 4.3 ExpenseReportService

| Method | Description |
|--------|-------------|
| `createExpenseReport(string $tripId): ExpenseReport` | Create report |
| `addExpenseLine(string $reportId, array $data): ExpenseLine` | Add expense |
| `submitExpenseReport(string $reportId): ExpenseReport` | Submit for approval |
| `approveExpenseReport(string $reportId, string $approverId): ExpenseReport` | Approve |
| `rejectExpenseReport(string $reportId, string $reason): ExpenseReport` | Reject |
| `markReimbursed(string $reportId): ExpenseReport` | Mark reimbursed |
| `getExpenseReportTotal(string $reportId): float` | Get total |

### 4.4 PerDiemService

| Method | Description |
|--------|-------------|
| `getPerDiemRate(string $city, \DateTime $date): float` | Get rate for location |
| `calculatePerDiemTotal(string $tripId): float` | Calculate total |
| `calculateFirstLastDayRate(string $city, \DateTime $date, bool $isFirst): float` | First/last day % |
| `getExcessPerDiem(string $tripId): float` | Calculate excess to return |

---

## 5. Workflow

### 5.1 Trip Workflow

```
Planned ──> Manager Approval ──> Approved ──> In Progress ──> Complete
    │                              │
    └──────> Rejected              └──────> Cancelled
```

### 5.2 Expense Report Workflow

```
Draft ──> Submitted ──> Manager Approved ──> Finance Verified ──> Reimbursed
               │                                │
               └────> Rejected (return to draft) ────────> Rejected
```

---

## 6. Integration Architecture

### 6.1 Provided Services

| Consumer | Interface | Data |
|----------|-----------|------|
| ksf_FA_TravelExpense | TravelExpenseServiceInterface | Expense data |
| ksf_ProjectManagement | TripServiceInterface | Trip as project |
| ksf_Timesheets | TripServiceInterface | Trip time entries |

### 6.2 Consumed Services

| Provider | Interface | Data |
|---------|-----------|------|
| ksf_HRM | EmployeeServiceInterface | Employee records |
| ksf_ProjectManagement | ProjectServiceInterface | Trip project link |
| ksf_FA | GLServiceInterface | GL code validation |

---

## 7. Error Handling

### 7.1 Exception Hierarchy

```
\Exception
└── KsfTravelExpenseException (base)
    ├── SupplierNotFoundException
    ├── TripNotFoundException
    ├── ExpenseReportNotFoundException
    ├── OverBudgetException
    ├── InvalidGlCodeException
    └── WorkflowStateException
```

---

## 8. File Structure

```
ksf_TravelExpense/
├── composer.json
├── AGENTS.md
├── ProjectDcs/
│   ├── Business Requirements.md
│   ├── Architecture.md           ← THIS FILE
│   ├── Functional Requirements.md
│   ├── Use Case.md
│   ├── Test Plan.md
│   ├── UAT Plan.md
│   └── RTM.md
└── src/Ksfraser/TravelExpense/
    ├── Entity/
    │   ├── Supplier.php
    │   ├── Trip.php
    │   ├── ExpenseReport.php
    │   └── ExpenseLine.php
    ├── Service/
    │   ├── SupplierService.php
    │   ├── TripService.php
    │   ├── ExpenseReportService.php
    │   └── PerDiemService.php
    ├── Repository/
    │   └── SupplierRepositoryInterface.php
    ├── Contract/
    │   └── TravelExpenseServiceInterface.php
    └── Exception/
        └── TravelExpenseException.php
```

---

## 9. RBAC Integration (ksfraser/rbac)

### 9.1 Module Registration

ksf_TravelExpense registers with ksfraser/rbac:
- record_types: 'supplier', 'trip', 'expense_report', 'expense_line'
- projections: 'public' (basic info, status), 'full' (all fields including budget, rates, approval data)
- allow_invite: false
- children: expense_line (child of expense_report), expense_report (child of trip)

### 9.2 Entity Projections

| Entity | PUBLIC Fields | FULL Fields |
|--------|---------------|-------------|
| Supplier | name, type, contact, website, status | + rate_code, corporate_rate, preference_order |
| Trip | name, destination, dates, status | + budget_approved, expenses_total, approver_id, approved_at |
| ExpenseReport | name, total_amount, status, date_submitted | + all line items, receipts, approval_chain, GL coding |
| ExpenseLine | description, amount, category, date | + receipt_path, billable_flag, project_id |

### 9.3 Access Model

- **Employee**: Create/edit own trips and expense reports (via {userId}_individual team). View own records at PROJECTION_FULL.
- **Manager**: View team members' trips (via org_direct team), approve/reject expense reports
- **Finance/AP**: FULL access to approved expense reports for payment processing
- **Travel Admin**: FULL access to supplier management, corporate rates
- **Project Manager**: PUBLIC view of trip expenses linked to their projects

### 9.4 SQL Enforcement

Standard RBAC JOIN pattern against 0_rbac_record_access for all entity queries.

### 9.5 Approval Workflow

Expense report approval is a two-gate process:
1. RBAC team membership (manager must have can_edit on the expense_report record)
2. Business rule (expense_report.status must be 'submitted')
Approval action writes to audit log via AuditLoggerInterface.

### 9.6 Soft Delete

- Trips and expense reports use soft delete
- Expense lines cascade delete with parent report
- Suppliers may be soft-deleted (preferred) or hard-deleted (if no linked trips)

### 9.7 Persons Registry

Trip approver_id and employee_id link to the FA person registry (0_crm_persons/0_crm_contacts) for cross-module identity resolution.

---

*Document Version: 1.0.0*
*Last Updated: 2026-05-24*