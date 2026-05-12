# Functional Requirements - ksf_TravelExpense

## Document Information
- **Module**: ksf_TravelExpense
- **Version**: 1.0.0
- **Date**: 2026-05-11
- **Status**: Proposed
- **Author**: KSFII Development Team

---

## 1. Overview

### 1.1 Purpose
ksf_TravelExpense provides travel and expense management including supplier management, trip scheduling, and expense reporting with GL integration.

### 1.2 Scope
- Preferred supplier management
- Trip creation and approval
- Expense entry and reporting
- Per diem calculations
- GL code mapping
- Workflow integration

---

## 2. Core Entities

### 2.1 Supplier

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| id | string | Yes | UUID |
| name | string | Yes | Supplier name |
| type | enum | Yes | SupplierType |
| contact | string | No | Contact info |
| website | string | No | URL |
| rate_code | string | No | Corporate rate code |
| preference_order | int | Yes | 1 = preferred |
| corporate_rate_available | bool | Yes | Has corporate rate |
| status | enum | Yes | active/inactive |

### 2.2 Trip

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| id | string | Yes | UUID |
| employee_id | string | Yes | FK to Employee |
| name | string | Yes | Trip name |
| start_date | Date | Yes | Start date |
| end_date | Date | Yes | End date |
| destination | string | Yes | Travel destination |
| project_id | string | No | FK to Project |
| status | enum | Yes | TripStatus |
| budget_approved | float | No | Approved budget |
| expenses_total | float | Yes | Total expenses |
| approver_id | string | No | Manager ID |
| approved_at | DateTime | No | Approval timestamp |

### 2.3 ExpenseReport

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| id | string | Yes | UUID |
| trip_id | string | Yes | FK to Trip |
| employee_id | string | Yes | FK to Employee |
| submitted_at | DateTime | No | Submission timestamp |
| status | enum | Yes | ExpenseReportStatus |
| total_amount | float | Yes | Total expenses |
| approver_id | string | No | Manager ID |
| approved_at | DateTime | No | Approval timestamp |
| rejected_reason | string | No | Rejection reason |

### 2.4 ExpenseLine

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| id | string | Yes | UUID |
| expense_report_id | string | Yes | FK to ExpenseReport |
| date | Date | Yes | Expense date |
| category | enum | Yes | ExpenseCategory |
| description | string | Yes | Description |
| amount | float | Yes | Expense amount |
| project_id | string | No | FK to Project (billable) |
| task_id | string | No | FK to Task (billable) |
| gl_code | string | Yes | GL expense code |
| receipt_path | string | No | Receipt file path |
| status | enum | Yes | active/inactive |

---

## 3. Functional Requirements

### FR-TE-001: Supplier Management
**Requirement**: System shall manage preferred suppliers.

**Features**:
- Add/edit/deactivate suppliers
- Categorize by type
- Set preference order
- Track corporate rate availability
- Search by name/type

### FR-TE-002: Trip Management
**Requirement**: System shall manage employee trips.

**Features**:
- Create trip request
- Manager approval workflow
- Link to project for billing
- Track trip status
- Calculate per diem

### FR-TE-003: Expense Entry
**Requirement**: System shall record expense line items.

**Features**:
- Add expense with category
- Set date and amount
- Link to project/task
- Assign GL code
- Upload receipt
- Validate against budget

### FR-TE-004: Expense Report
**Requirement**: System shall aggregate expenses into reports.

**Features**:
- Create report for trip
- Add/remove expense lines
- Calculate totals
- Submit for approval
- Manager approval
- Finance verification
- Mark as reimbursed

### FR-TE-005: Per Diem Calculation
**Requirement**: System shall calculate per diem allowances.

**Features**:
- Get rate by city
- Calculate full-day rates
- Calculate first/last day (75%)
- Track excess to return
- Apply company rules

### FR-TE-006: GL Integration
**Requirement**: System shall map expenses to GL codes.

**Features**:
- Default GL by category
- Validate GL codes
- Export for accounting
- Track GL totals

---

## 4. Default GL Codes

| Category | GL Code |
|----------|---------|
| Meal - Breakfast | MEAL-BREAKFAST |
| Meal - Lunch | MEAL-LUNCH |
| Meal - Dinner | MEAL-DINNER |
| Hotel | HOTEL |
| Car Rental | CAR_RENTAL |
| Taxi | TAXI |
| Transit | TRANSIT |
| Per Diem | PER_DIEM |
| Other | OTHER |

---

## 5. Events

| Event | Trigger |
|-------|---------|
| trip.created | New trip created |
| trip.approved | Trip approved |
| trip.rejected | Trip rejected |
| expense_report.submitted | Report submitted |
| expense_report.approved | Report approved |
| expense_report.rejected | Report rejected |
| expense_report.reimbursed | Payment made |

---

*Document Version: 1.0.0*
*Last Updated: 2026-05-11*