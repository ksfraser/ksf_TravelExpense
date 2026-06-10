# Use Case - ksf_TravelExpense

## Document Information
- **Module**: ksf_TravelExpense
- **Version**: 1.0.0
- **Date**: 2026-05-11
- **Status**: Proposed
- **Author**: KSFII Development Team

---

## 1. Use Cases

### UC-TE-001: Create Trip Request

**Actor**: Employee

**Preconditions**:
- Employee logged in

**Steps**:
1. Navigate to My Trips
2. Click "New Trip Request"
3. Enter trip name and dates
4. Enter destination
5. Optionally link to project
6. Optionally assign to different user
7. Submit request

**Postconditions**:
- Trip created with status = Planned
- Manager receives notification

**Note**:
- Allows manager or admin assistant to create the trip request.
- This should be a Hook so other modules (e.g. HRM, ProjectManagement, Contracts) can create the trip
---

### UC-TE-002: Manager Approves Trip

**Actor**: Manager

**Preconditions**:
- Manager has pending trip approvals

**Steps**:
1. View pending trip requests
2. Review trip details
3. Check calendar for conflicts
4. Set budget approval
5. Click "Approve"

**Postconditions**:
- Trip status = Approved
- Employee notified

---

### UC-TE-003: Add Expense

**Actor**: Employee

**Preconditions**:
- Trip is Approved or In Progress

**Steps**:
1. Open trip
2. Click "Add Expense"
3. Enter date
4. Select category
5. Enter amount and description
6. Select GL code
7. Upload receipt
8. Save
9. Duplicate check runs and advises if appears to be duplicate.

**Postconditions**:
- Expense line added to trip
- Running total updated

---

### UC-TE-004: Submit Expense Report

**Actor**: Employee

**Preconditions**:
- Trip is Approved, In Progress or Complete
- Expenses entered

**Steps**:
1. Open trip
2. Click "Create Expense Report"
3. Review all expense lines
4. Select expenses to submit in this report.
5. Add comments
6. Verify totals
7. Click "Submit for Approval"

**Postconditions**:
- ExpenseReport status = Submitted
- Manager notified

**NOTE**
- This is similar to batch invoicing of deliveries in Front Accounting's Sales workflow.  Not all expenses need to be on 1 report.

---

### UC-TE-005: Approve Expense Report

**Actor**: Manager

**Preconditions**:
- Manager has pending expense reports

**Steps**:
1. View pending expense reports
2. Review expense lines
2.a. System advises of line items that are flagged as maybe duplicates.
3. Check against budget
4. Click "Approve"

**Postconditions**:
- Report status = ManagerApproved
- Finance notified

---

### UC-TE-006: Finance Verification

**Actor**: Finance

**Preconditions**:
- Report is manager approved

**Steps**:
1. View reports awaiting verification
2. Verify GL codes
3. Check receipts
4. Click "Verify"

**Postconditions**:
- Report status = FinanceVerified
- Ready for reimbursement

---

### UC-TE-007: Reject Expense Report

**Actor**: Manager

**Preconditions**:
- Manager has pending expense reports

**Steps**:
1. View pending expense reports
2. Review expense lines
2.a. System advises of line items that are flagged as maybe duplicates.
3. Check against budget
4. Manager adds comments
5. Click "Reject"

**Postconditions**:
- Report status = ManagerRejected
- Employee notified

---


### UC-TE-008: Edit Expense Report

**Actor**: Employee

**Preconditions**:
- Trip is Approved, In Progress or Complete
- Report is Open or ManagerRejected
- Expenses entered

**Steps**:
1. Click "Edit Expense Report"
2. Review Manager Comments
3. Review all expense lines.
4. Select or Deselect expenses to submit in this report.
5. Add comments
6. Verify totals
7. Click "Submit for Approval"

**Postconditions**:
- ExpenseReport status = Submitted
- Manager notified

**NOTE**
- This is similar to batch invoicing of deliveries in Front Accounting's Sales workflow.  Not all expenses need to be on 1 report.

---


*Document Version: 1.1.0*
*Last Updated: 2026-06-10*