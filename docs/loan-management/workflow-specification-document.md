# Loan Management Feature - Workflow Specification Document

## Table of Contents
1. [Workflow Overview](#1-workflow-overview)
2. [Use Cases](#2-use-cases)
3. [Sequence Diagrams](#3-sequence-diagrams)
4. [State Machines](#4-state-machines)
5. [User Stories](#5-user-stories)
6. [Business Rules](#6-business-rules)
7. [Data Flow Diagrams](#7-data-flow-diagrams)
8. [Approval Workflows](#8-approval-workflows)
9. [Integration Points](#9-integration-points)
10. [Exception Handling](#10-exception-handling)

---

## 1. Workflow Overview

### 1.1 System Actors

| Actor | Role | Permissions |
|-------|------|-------------|
| **Employee** | Applies for and repays loans | View own loans, apply for loans/topups |
| **HR Officer** | Manages loan applications | Review, approve/reject applications |
| **HR Admin** | Full loan management | Create loans, disburse, configure types |
| **Payroll Officer** | Processes payroll deductions | View deductions, enter manual deductions |
| **Finance** | Disburses funds | Process disbursement payments |
| **System** | Automated processes | Calculate deductions, generate schedules |

### 1.2 High-Level Workflow Map

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   APPLY/CREATE  │────▶│    APPROVAL     │────▶│   DISBURSEMENT  │
│                 │     │                 │     │                 │
│ • Employee Apply│     │ • Manager Review│     │ • Bank Transfer │
│ • HR Direct     │     │ • Auto-Approve  │     │ • Cash/Cheque   │
│ • Topup Apply   │     │ • Rejection     │     │ • Payroll       │
└─────────────────┘     └─────────────────┘     └─────────────────┘
                                                         │
                                                         ▼
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   LOAN CLOSURE  │◀────│    REPAYMENT    │◀────│     ACTIVE      │
│                 │     │                 │     │                 │
│ • Fully Paid    │     │ • Auto Payroll  │     │ • Deductions    │
│ • Written Off   │     │ • Manual Entry  │     │ • Amortization  │
│ • Cancelled     │     │ • Lump Sum      │     │ • Topup Option  │
└─────────────────┘     └─────────────────┘     └─────────────────┘
```

---

## 2. Use Cases

### UC-001: Employee Applies for New Loan

**Primary Actor:** Employee  
**Precondition:** Employee has active payroll profile

**Main Flow:**
1. Employee navigates to ESS → Loans → Apply for Loan
2. System displays loan types available to employee
3. Employee selects loan type and enters:
   - Requested amount
   - Purpose/description
   - Preferred repayment start date
   - Proposed monthly installment
4. System validates eligibility:
   - Maximum amount limit
   - No existing active loan (if type doesn't allow multiple)
   - Minimum employment period (if configured)
5. System displays calculated:
   - Repayment schedule preview
   - Total interest (if applicable)
   - Number of installments
6. Employee confirms application
7. System creates loan application with status "SUBMITTED"
8. System notifies approvers (HR/Manager)
9. Use case ends

**Alternative Flows:**
- 4a. Employee not eligible: Display reason, suggest alternative
- 5a. Employee modifies proposal: Recalculate on-the-fly
- 6a. Employee cancels: Save as "DRAFT" or discard

**Postcondition:** Loan application created, pending approval

---

### UC-002: HR Creates Loan for Employee

**Primary Actor:** HR Admin  
**Precondition:** Employee has active payroll profile

**Main Flow:**
1. HR navigates to Payroll → Loans → Create Loan
2. HR selects employee from dropdown
3. System loads employee's:
   - Active payroll details
   - Existing loans (if any)
   - Eligibility limits
4. HR selects loan type and enters details:
   - Amount
   - Repayment terms
   - Disbursement method
5. System auto-approves (no workflow for HR-created loans)
6. HR confirms and submits
7. System creates:
   - Approved application
   - Active loan record
   - Repayment schedule
8. Loan status set to "PENDING_DISBURSEMENT"
9. Use case ends

**Postcondition:** Loan created and ready for disbursement

---

### UC-003: Review and Approve Loan Application

**Primary Actor:** HR Officer / Approver  
**Precondition:** Application status is "SUBMITTED" or "UNDER_REVIEW"

**Main Flow:**
1. Approver receives notification/email
2. Approver navigates to Loans → Pending Applications
3. System displays application details:
   - Employee info
   - Requested amount vs. eligibility
   - Purpose
   - Repayment proposal
   - Credit history (existing loans)
4. Approver reviews and can:
   - Approve as-is
   - Approve with modifications (amount, terms)
   - Request more information
   - Reject with reason
5. If approved: System updates status to "APPROVED"
6. System creates loan record (status: PENDING_DISBURSEMENT)
7. System notifies:
   - Employee (approval notification)
   - Finance (disbursement pending)
8. Use case ends

**Alternative Flows:**
- 4a. Request more info: Status → "UNDER_REVIEW", notify employee
- 4b. Reject: Status → "REJECTED", notify employee with reason

**Postcondition:** Application approved/rejected, loan created if approved

---

### UC-004: Disburse Loan Funds

**Primary Actor:** Finance / HR Admin  
**Precondition:** Loan status is "PENDING_DISBURSEMENT"

**Main Flow:**
1. Finance navigates to Loans → Pending Disbursement
2. System displays loans awaiting disbursement
3. Finance selects loan and reviews details
4. Finance enters disbursement info:
   - Date
   - Method (bank transfer/cash/cheque)
   - Reference number
   - Bank account (if bank transfer)
5. Finance confirms disbursement
6. System updates:
   - Loan status → "ACTIVE"
   - Disbursement date recorded
   - Repayment schedule activated
7. System notifies employee of disbursement
8. System schedules automatic deductions
9. Use case ends

**Postcondition:** Loan active, repayments begin from start date

---

### UC-005: Automatic Payroll Deduction

**Primary Actor:** System  
**Precondition:** Payroll period is being processed

**Main Flow:**
1. Payroll officer initiates payroll processing
2. System calculates payroll for each employee
3. For employees with active automatic loans:
   - System identifies current month installment
   - Calculates deduction amount
   - Adds to total deductions
   - Creates payroll record detail entry
4. Payroll calculation includes loan deduction in net pay
5. Upon payroll approval:
   - System records loan payment
   - Updates repayment schedule (status → "PAID")
   - Updates loan balance
   - Decrements remaining months
6. If loan fully paid: Status → "PAID_OFF"
7. Use case ends

**Alternative Flows:**
- 3a. Manual deduction mode: Skip automatic calculation
- 5a. Partial payment (if net pay insufficient): Record partial, mark "PARTIALLY_PAID"

**Postcondition:** Loan payment recorded, balance updated

---

### UC-006: Manual Deduction Entry

**Primary Actor:** Payroll Officer  
**Precondition:** Loan is in "MANUAL" deduction mode

**Main Flow:**
1. Payroll officer navigates to Loans → Manual Deductions
2. Selects payroll period
3. System displays loans set to manual deduction
4. Officer enters deduction amount for each loan
5. System validates amount doesn't exceed:
   - Current installment
   - Remaining balance
6. System saves manual deduction entry
7. During payroll processing:
   - System includes manual deduction amounts
   - Marks entries as "PROCESSED"
8. Upon payroll approval:
   - System records loan payments
   - Updates loan balances
9. Use case ends

**Postcondition:** Manual deduction processed, loan updated

---

### UC-007: Employee Applies for Topup

**Primary Actor:** Employee  
**Precondition:** Employee has active loan with topup eligibility

**Main Flow:**
1. Employee navigates to ESS → My Loans
2. System displays active loans with "Apply Topup" option
3. Employee selects loan for topup
4. System displays:
   - Current loan details
   - Outstanding balance
   - Remaining months
   - Current installment
5. Employee enters:
   - Additional amount requested
   - New total repayment months
6. System calculates:
   - Consolidated principal (outstanding + new)
   - New monthly installment
   - New total interest
   - New repayment schedule
7. Employee reviews and confirms
8. System creates topup application with type "TOPUP"
9. System links to parent loan
10. Follows approval workflow (UC-003)
11. Upon approval:
    - Parent loan marked as paid off (consolidated)
    - New loan created with consolidated terms
12. Use case ends

**Postcondition:** New consolidated loan active, old loan closed

---

### UC-008: View Loan Dashboard

**Primary Actor:** HR Admin / Manager  
**Precondition:** User has dashboard permissions

**Main Flow:**
1. User navigates to Payroll → Loans → Dashboard
2. System displays:
   - Summary cards:
     * Total active loans (count & value)
     * Total outstanding balance
     * Pending disbursements
     * Pending approvals
   - Charts:
     * Monthly collections (6-month trend)
     * Loan status distribution
     * Top loan types
   - Tables:
     * Recent applications
     * Overdue installments
     * Top borrowers
3. User can filter by:
   - Date range
   - Department
   - Loan type
   - Status
4. User can drill down to detailed reports
5. Use case ends

---

### UC-009: Loan Write-off

**Primary Actor:** HR Admin  
**Precondition:** Loan has outstanding balance

**Main Flow:**
1. Admin navigates to loan details
2. Admin selects "Write Off" action
3. System prompts for:
   - Write-off reason
   - Approval password/confirmation
   - Effective date
4. Admin confirms
5. System:
   - Updates loan status → "WRITTEN_OFF"
   - Records write-off in audit log
   - Cancels remaining schedule entries
   - Creates adjustment entry
6. System notifies relevant parties
7. Use case ends

**Postcondition:** Loan written off, no further deductions

---

### UC-010: Loan Suspension

**Primary Actor:** HR Admin  
**Precondition:** Loan is active

**Main Flow:**
1. Admin navigates to loan details
2. Admin selects "Suspend" action
3. System prompts for:
   - Suspension reason
   - Expected resumption date (optional)
4. Admin confirms
5. System:
   - Updates loan status → "SUSPENDED"
   - Pauses automatic deductions
   - Extends repayment end date (if needed)
6. Admin can resume later:
   - Status → "ACTIVE"
   - Deductions resume
7. Use case ends

**Postcondition:** Loan suspended, deductions paused

---

## 3. Sequence Diagrams

### 3.1 New Loan Application Flow

```
Employee    System    Validation    Approval    Notification
   │           │           │            │            │
   │──Apply──▶│           │            │            │
   │          │──Validate─▶│            │            │
   │          │◀─Result───│            │            │
   │◀─Preview│            │            │            │
   │          │           │            │            │
   │──Confirm▶│           │            │            │
   │          │────────Create App─────▶│            │
   │          │◀─────────App ID────────│            │
   │          │           │            │            │
   │          │─────────────────────────▶Notify─────▶
   │◀────────Approved───────────────────────────────│
   │           (later, after approval)               │
```

### 3.2 Payroll Deduction Flow

```
Payroll Officer    Payroll System    Loan Service    Loan Record
        │                │               │               │
        │──Process──────▶│               │               │
        │                │──Calc Loans──▶│               │
        │                │               │──Get Active──▶│
        │                │               │◀──Schedule───│
        │                │◀──Amounts────│               │
        │                │               │               │
        │◀──Preview─────│               │               │
        │                │               │               │
        │──Approve──────▶│               │               │
        │                │──Record─────▶│               │
        │                │               │──Payment─────▶│
        │                │               │◀──Updated────│
        │◀───────────────│               │               │
```

### 3.3 Topup Consolidation Flow

```
Employee    System    Calculator    Parent Loan    New Loan
   │           │           │              │            │
   │──Request─▶│          │              │            │
   │          │──Calc────▶│              │            │
   │          │◀─Result───│              │            │
   │◀─Preview│           │              │            │
   │          │           │              │            │
   │──Confirm▶│          │              │            │
   │          │────────Create App────────────────────▶│
   │          │           │              │            │
   │ (after approval)     │              │            │
   │          │────────Mark Old─────────▶│            │
   │          │◀────────Done──────────────│            │
   │          │────────Create New────────────────────▶│
   │          │◀───────────────────────────New ID───────│
   │◀────────Success───────────────────────────────────│
```

---

## 4. State Machines

### 4.1 Loan Application State Machine

```
                    ┌─────────────┐
         ┌─────────│   DRAFT   │◀────────┐
         │         └──────┬──────┘         │
         │                │ Save          │
         │ Submit         ▼               │
         │         ┌─────────────┐         │
         └────────▶│  SUBMITTED  │─────────┘
                   └──────┬──────┘
                          │
           ┌──────────────┼──────────────┐
           │              │               │
    Request Info      Approve         Reject
           │              │               │
           ▼              ▼               ▼
    ┌─────────────┐  ┌──────────┐    ┌──────────┐
    │UNDER_REVIEW│  │ APPROVED │    │ REJECTED │
    └──────┬──────┘  └────┬─────┘    └────┬─────┘
           │              │               │
           └──────────────┘               │
                  │                   Cancel
                  ▼                        │
           ┌──────────┐                    ▼
           │ PROCESSED│◀────────────┐  ┌─────────┐
           └────┬─────┘             └──│ CANCELLED│
                │                      └─────────┘
                ▼
         (Loan Created)
```

**States:**
- **DRAFT:** Application being prepared
- **SUBMITTED:** Ready for review
- **UNDER_REVIEW:** Additional information requested
- **APPROVED:** Approved, ready for loan creation
- **REJECTED:** Denied, not proceeding
- **CANCELLED:** Withdrawn by applicant
- **PROCESSED:** Converted to loan

### 4.2 Loan State Machine

```
                    ┌─────────────────┐
         ┌─────────│PENDING_DISBURSE │◀────────┐
         │         └────────┬────────┘         │
         │                  │ Create            │
         │                  ▼                   │
         │         ┌─────────────────┐          │
         │         │     ACTIVE      │◀─────┐    │
         │         └────────┬────────┘      │    │
         │    Disburse      │              │    │
         │                  │              │    │
    ┌────┴────┐        ┌────┴────┐    ┌────┴────┴───┐
    │ SUSPEND │        │ TOPUP   │    │ FULLY_PAID  │
    │         │        │         │    │             │
    └────┬────┘        └────┬────┘    └─────────────┘
         │                  │
         │ Resume           │ Consolidate
         │                  │
         ▼                  ▼
    ┌─────────┐        ┌─────────┐
    │ WRITE-  │        │ CANCEL  │
    │  OFF    │        │         │
    └─────────┘        └─────────┘
```

**States:**
- **PENDING_DISBURSEMENT:** Approved but not yet paid out
- **ACTIVE:** Disbursed, repayments ongoing
- **SUSPENDED:** Temporarily paused
- **PAID_OFF:** Fully repaid
- **WRITTEN_OFF:** Cancelled with balance forgiven
- **CANCELLED:** Cancelled before disbursement

---

## 5. User Stories

### 5.1 Employee User Stories

| ID | As an... | I want to... | So that I... | Priority |
|----|----------|--------------|--------------|----------|
| US-001 | Employee | Apply for a loan online | Can access funds without paperwork | High |
| US-002 | Employee | See my loan balance | Know how much I still owe | High |
| US-003 | Employee | View my repayment schedule | Can plan my finances | High |
| US-004 | Employee | Apply for a topup | Can get additional funds | Medium |
| US-005 | Employee | See when my next deduction is | Can verify my payslip | Medium |
| US-006 | Employee | Cancel my pending application | Can withdraw if I change my mind | Medium |
| US-007 | Employee | Download my loan statement | Have records for my files | Low |

### 5.2 HR User Stories

| ID | As an... | I want to... | So that I... | Priority |
|----|----------|--------------|--------------|----------|
| US-008 | HR Officer | Review pending applications | Can make approval decisions | High |
| US-009 | HR Admin | Create loans directly for employees | Can process urgent requests quickly | High |
| US-010 | HR Admin | Configure loan types | Can offer different loan products | High |
| US-011 | HR Manager | See loan statistics | Can monitor portfolio health | Medium |
| US-012 | HR Officer | Disburse approved loans | Can complete the loan process | High |
| US-013 | HR Admin | Suspend problematic loans | Can handle special cases | Medium |
| US-014 | Payroll Officer | Enter manual deductions | Can handle exceptions | Medium |
| US-015 | HR Manager | Generate loan reports | Can analyze trends | Low |

---

## 6. Business Rules

### 6.1 Eligibility Rules

| Rule ID | Rule Description | Enforcement |
|---------|------------------|-------------|
| BR-001 | Employee must have active payroll profile | System validation |
| BR-002 | Requested amount ≤ max allowed for loan type | System validation |
| BR-003 | If loan type doesn't allow multiple, employee cannot have active loan of same type | System validation |
| BR-004 | Monthly installment ≤ 50% of net salary (configurable) | Warning/Validation |
| BR-005 | Repayment start date must be in current or future payroll period | System validation |
| BR-006 | Minimum employment period may be required (configurable per type) | System validation |

### 6.2 Calculation Rules

| Rule ID | Rule Description | Implementation |
|---------|------------------|----------------|
| BR-007 | Flat interest: Interest = Principal × Rate × (Months/12) | Calculator |
| BR-008 | Reducing balance: Interest = Outstanding × Monthly Rate | Calculator |
| BR-009 | Topup: New principal = Old outstanding + Topup amount | Calculator |
| BR-010 | Consolidation: Old loan marked paid off when topup disbursed | Loan Service |
| BR-011 | Early payoff: No penalty (configurable) | Loan Service |
| BR-012 | Partial payment: Applied to current installment first | Payment Service |

### 6.3 Operational Rules

| Rule ID | Rule Description | Enforcement |
|---------|------------------|-------------|
| BR-013 | HR-created loans are auto-approved | System |
| BR-014 | Employee applications require approval | Workflow |
| BR-015 | Disbursement must occur before loan becomes active | State machine |
| BR-016 | Automatic deductions occur in payroll processing | Payroll Service |
| BR-017 | Manual deductions override automatic for the period | Payroll Service |
| BR-018 | Written-off loans cannot be reactivated | State machine |
| BR-019 | Suspended loans resume from where they left off | Loan Service |
| BR-020 | Loan reference numbers are auto-generated | Model |

---

## 7. Data Flow Diagrams

### 7.1 Context Diagram (Level 0)

```
                        ┌─────────────────┐
         ┌─────────────│  EMPLOYEE       │─────────────┐
         │             │  (Apply/View)   │             │
         │             └─────────────────┘             │
         │                                             │
         ▼                                             ▼
┌─────────────────┐                         ┌─────────────────┐
│   HR/FINANCE    │                         │    PAYROLL      │
│  (Manage/Disb)  │                         │   (Deduct)      │
└────────┬────────┘                         └────────┬────────┘
         │                                         │
         │              ┌─────────────┐             │
         └─────────────▶│             │◀────────────┘
                      │ LOAN SYSTEM │
         ┌─────────────│             │─────────────┐
         │             └──────┬──────┘             │
         │                    │                   │
         ▼                    ▼                   ▼
┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐
│   LOAN_TYPES   │  │  NOTIFICATION   │  │    REPORTS      │
│   (Config)      │  │   (Email/SMS)   │  │  (Analytics)    │
└─────────────────┘  └─────────────────┘  └─────────────────┘
```

### 7.2 Level 1 DFD

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         LOAN MANAGEMENT SYSTEM                          │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│  ┌──────────┐    ┌──────────────┐    ┌─────────────┐    ┌──────────┐  │
│  │ 1.0      │    │ 2.0          │    │ 3.0         │    │ 4.0      │  │
│  │ Process  │───▶│ Manage       │───▶│ Process     │───▶│ Process  │  │
│  │ Application│   │ Loan Lifecycle│   │ Repayment   │   │ Reports  │  │
│  └──────────┘    └──────────────┘    └─────────────┘    └──────────┘  │
│       │                │                   │                 │        │
│       ▼                ▼                   ▼                 ▼        │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │                    LOAN DATABASE                                  │  │
│  │  [loan_applications, loans, schedules, payments, types]          │  │
│  └──────────────────────────────────────────────────────────────────┘  │
│                                                                         │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 8. Approval Workflows

### 8.1 Standard Loan Approval

```
┌──────────┐    ┌──────────┐    ┌──────────┐    ┌──────────┐
│ Employee │───▶│  System  │───▶│ HR Review│───▶│ Approved │
│ Submits  │    │ Validates│    │ Approver │    │  /Reject │
└──────────┘    └──────────┘    └──────────┘    └──────────┘
     │                               │
     │                               │
     ▼                               ▼
┌──────────┐                  ┌──────────┐
│  Invalid │                  │  Finance │
│  Return  │                  │Disburses │
└──────────┘                  └──────────┘
```

### 8.2 HR Direct Loan (Auto-Approval)

```
┌──────────┐    ┌──────────┐    ┌──────────┐
│  HR      │───▶│  System  │───▶│ Finance  │
│  Creates │    │ Auto-    │    │Disburses │
│  Loan    │    │ Approves │    │          │
└──────────┘    └──────────┘    └──────────┘
     │                               │
     │                               │
     └───────────────┬───────────────┘
                     ▼
               ┌──────────┐
               │  Active  │
               │   Loan   │
               └──────────┘
```

### 8.3 Approval Matrix

| Loan Type | Employee Apply | HR Create | Approval Required | Approver |
|-----------|-----------------|-----------|---------------------|----------|
| Emergency | Yes | Yes | Yes | HR Manager |
| Personal | Yes | Yes | Yes | HR Officer |
| Housing | Yes | Yes | Yes | HR Manager + Finance |
| Education | Yes | Yes | Yes | HR Officer |
| Topup | Yes | Yes | Yes (if > threshold) | HR Officer |

---

## 9. Integration Points

### 9.1 Payroll Integration

| Integration | Direction | Trigger | Data |
|-------------|-----------|---------|------|
| Payroll Calculation | System → Payroll | Payroll processing | Deduction amounts |
| Payment Recording | Payroll → System | Payroll approval | Payment confirmation |
| Manual Deduction | User → Payroll | Entry form | Override amounts |
| Salary Validation | System → Payroll | Eligibility check | Salary data |

### 9.2 Notification Integration

| Event | Channel | Recipients | Template |
|-------|---------|------------|----------|
| Application Submitted | Email | Employee, HR | Confirmation |
| Approval Required | Email, In-app | Approvers | Action needed |
| Application Approved | Email, SMS | Employee | Congratulations |
| Application Rejected | Email | Employee | With reason |
| Funds Disbursed | Email, SMS | Employee | Disbursement info |
| Payment Deducted | In-app | Employee | Payslip notification |
| Loan Paid Off | Email | Employee | Completion certificate |
| Topup Consolidated | Email | Employee | New terms |

### 9.3 Accounting Integration (Future)

| Transaction | Debit | Credit | When |
|-------------|-------|--------|------|
| Loan Disbursement | Employee Loan A/c | Bank/Cash | Disbursement |
| Loan Repayment | Bank/Payroll A/c | Employee Loan A/c | Payment |
| Interest Income | Employee Loan A/c | Interest Income A/c | Monthly |
| Write-off | Bad Debt A/c | Employee Loan A/c | Write-off |

---

## 10. Exception Handling

### 10.1 Error Scenarios

| Scenario | Detection | Handling | Notification |
|----------|-----------|----------|--------------|
| Insufficient net pay for deduction | Payroll calculation | Reduce to available, flag for review | Payroll officer |
| Employee termination with active loan | Termination process | Alert HR, provide options | HR Manager |
| Duplicate disbursement | Disbursement process | Block, show warning | Finance |
| Invalid bank account | Disbursement | Block, request update | Employee, HR |
| Schedule calculation error | Schedule generation | Log error, use fallback | Admin |
| Payroll reversal with loan payment | Payroll reversal | Reverse loan payment, adjust balance | System |

### 10.2 Recovery Procedures

| Situation | Recovery Action | Responsible |
|-----------|-----------------|-------------|
| Over-deduction | Refund via next payroll or separate payment | Payroll |
| Under-deduction | Adjust next installment or create manual entry | Payroll |
| Wrong employee credited | Reverse and re-assign | HR Admin |
| System error during processing | Restore from backup, re-process | IT/Admin |
| Partial installment paid | Track separately, adjust schedule | System |

---

## 11. Report Specifications

### 11.1 Dashboard Reports

| Report | Frequency | Data Points | Filters |
|--------|-----------|-------------|---------|
| Loan Portfolio Summary | Real-time | Total loans, amounts, statuses | Date, type, dept |
| Monthly Collections | Monthly | Amounts collected, count | Period |
| Aging Report | Weekly | Overdue loans by days | Status |
| Top Borrowers | Monthly | Employees by total borrowed | Amount range |
| Application Pipeline | Daily | Applications by status | Date range |

### 11.2 Export Formats

- PDF: Statements, schedules
- Excel: Reports, bulk data
- CSV: Import/Export for integrations

---

## 12. Appendix

### 12.1 Glossary

| Term | Definition |
|------|------------|
| **Topup** | Adding to an existing loan, creating a consolidated new loan |
| **Amortization** | The process of spreading out loan payments over time |
| **Flat Interest** | Interest calculated on original principal throughout |
| **Reducing Balance** | Interest calculated on remaining principal |
| **Consolidation** | Combining multiple loans into one |
| **EMI** | Equated Monthly Installment - fixed monthly payment |
| **Write-off** | Cancelling a loan as uncollectible |

### 12.2 Calculation Examples

#### Flat Interest Example
```
Principal: 100,000
Interest Rate: 12% per annum
Term: 12 months

Monthly Interest = (100,000 × 12%) / 12 = 1,000
Monthly Principal = 100,000 / 12 = 8,333.33
Monthly Installment = 8,333.33 + 1,000 = 9,333.33
Total Interest = 1,000 × 12 = 12,000
Total Repayment = 100,000 + 12,000 = 112,000
```

#### Reducing Balance Example
```
Principal: 100,000
Interest Rate: 12% per annum (1% monthly)
Term: 12 months

Month 1: Interest = 100,000 × 1% = 1,000
Month 2: Interest = 91,666 × 1% = 916.66
...
Total Interest ≈ 6,500 (varies by rounding)
```

#### Topup Consolidation Example
```
Existing Loan:
- Original: 100,000
- Paid: 40,000
- Outstanding: 60,000
- Remaining months: 6

Topup Request:
- Additional: 50,000
- New term: 12 months

New Consolidated Loan:
- Principal: 60,000 + 50,000 = 110,000
- New installment: Calculated on 110,000 for 12 months
- Old loan: Marked paid off
```

---

*Document Version: 1.0*
*Last Updated: 2026-05-01*
*Author: Business Analyst*
