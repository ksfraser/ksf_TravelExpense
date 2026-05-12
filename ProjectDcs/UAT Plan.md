# UAT Plan - ksf_TravelExpense

## Document Information
- **Module**: ksf_TravelExpense
- **Version**: 1.0.0
- **Date**: 2026-05-11
- **Status**: Proposed
- **Author**: KSFII Development Team

---

## 1. UAT Scenarios

### UAT-TE-001: Complete Trip Workflow

**Actor**: Employee

**Steps**:
1. Create new trip request
2. Enter destination and dates
3. Submit request
4. Manager approves
5. Travel occurs
6. Add expense entries
7. Create and submit expense report
8. Manager approves report
9. Finance verifies
10. Mark reimbursed

**Expected**: Complete expense workflow executed

---

### UAT-TE-002: Per Diem Calculation

**Precondition**: Trip with 5 days in NYC

**Steps**:
1. Create trip to New York
2. Enter dates (Mon-Fri)
3. System calculates per diem

**Expected**: Per diem = 5 × NYC daily rate

---

### UAT-TE-003: Over Budget Warning

**Precondition**: Trip with $500 budget, adding $600 in expenses

**Steps**:
1. Add expenses totaling $600
2. View budget status

**Expected**: Warning displayed, remaining budget = -$100

---

*Document Version: 1.0.0*
*Last Updated: 2026-05-11*