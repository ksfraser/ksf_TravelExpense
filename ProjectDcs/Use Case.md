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
6. Submit request

**Postconditions**:
- Trip created with status = Planned
- Manager receives notification

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
- Trip is in progress

**Steps**:
1. Open trip
2. Click "Add Expense"
3. Enter date
4. Select category
5. Enter amount and description
6. Select GL code
7. Upload receipt
8. Save

**Postconditions**:
- Expense line added to trip
- Running total updated

---

### UC-TE-004: Submit Expense Report

**Actor**: Employee

**Preconditions**:
- Trip is complete
- Expenses entered

**Steps**:
1. Open completed trip
2. Click "Create Expense Report"
3. Review all expense lines
4. Verify totals
5. Click "Submit for Approval"

**Postconditions**:
- ExpenseReport status = Submitted
- Manager notified

---

### UC-TE-005: Approve Expense Report

**Actor**: Manager

**Preconditions**:
- Manager has pending expense reports

**Steps**:
1. View pending expense reports
2. Review expense lines
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

*Document Version: 1.0.0*
*Last Updated: 2026-05-11*