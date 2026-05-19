# Leave API Technical Documentation

## Overview

This document describes the leave management functionality exposed through the REST API (`routes/api.php`). The API covers:

1. Leave Application (apply, list, view, update, recall)
2. Leave Approval (supervisor, HR, CEO approval flows)
3. Leave Balance Check (real-time balance with adjustments & advance leave)
4. Leave Reports (personal, approved, rejected, today’s leaves)

The API is consumed by the mobile app and any third-party integrations. It mirrors the functionality available in the ESS (Employee Self-Service) web interface (`EssIndexController`) and the admin leave module (`ApplyForLeaveController`).

---

## 1. Authentication

All leave endpoints (except public biometric/attendance routes) require **Sanctum** authentication.

| Header | Value |
|--------|-------|
| `Authorization` | `Bearer {sanctum_token}` |
| `Accept` | `application/json` |

---

## 2. Leave Application

### 2.1 List My Leave Applications
**Endpoint:** `GET /api/leaves`  
**Controller:** `Api\LeaveController@index`  
**Auth:** Sanctum

**Description:** Returns paginated leave applications for the authenticated employee, scoped to the current financial year.

**Query Parameters:**
| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `per_page` | integer | 10 | Pagination page size |

**Response Structure:**
```json
{
  "status": "success",
  "message": "Leave applications retrieved successfully",
  "data": {
    "current_page": 1,
    "data": [ /* LeaveApplication models with employee, leaveType, approveBy, rejectBy relations */ ],
    "total": 25
  },
  "financial_year": {
    "start_date": "2025-07-01",
    "end_date": "2026-06-30"
  }
}
```

**ESS Equivalent:** `EssIndexController::leave()` — web view lists the same data with Blade pagination.

---

### 2.2 Get Leave Types
**Endpoint:** `GET /api/leave-types`  
**Controller:** `Api\LeaveController@getLeaveTypes`  
**Auth:** Sanctum

**Description:** Returns leave types applicable to the authenticated employee based on their assigned leave group. Includes annual entitlement and max carryover from `leave_group_settings`.

**Response:**
```json
{
  "status": "success",
  "message": "Employee leave types retrieved successfully",
  "data": [
    {
      "leave_type_id": 1,
      "leave_type_name": "Annual Leave",
      "annual_entitlement": 21,
      "max_carryover_days": 5
    }
  ]
}
```

**ESS Equivalent:** `EssIndexController::leaveApplyForm()` uses `$this->employee->applicableLeaveTypes()->pluck(...)` to populate the leave type dropdown.

---

### 2.3 Get Leave Application Form Data
**Endpoint:** `GET /api/leaves/create`  
**Controller:** `Api\LeaveController@create`  
**Auth:** Sanctum

**Description:** Returns metadata needed to render a leave application form: leave types, employee info, and employee list (for proxy applications).

**Response:**
```json
{
  "status": "success",
  "data": {
    "leave_type": [ /* array of LeaveType */ ],
    "employee_info": { /* employee details */ },
    "employee_list": [ /* for proxy applications */ ]
  }
}
```

**ESS Equivalent:** `EssIndexController::leaveApplyForm()` returns the same data to the Blade view.

---

### 2.4 Apply for Leave
**Endpoint:** `POST /api/leave/apply` (also aliased by `/api/leaves` and `/api/apply`)  
**Controller:** `Api\LeaveController@applyLeave`  
**Auth:** Sanctum

**Description:** Submits a new leave application. Performs overlapping leave validation, balance validation, and sends email notifications.

**Request Body:**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `leave_type` | string | Yes | Leave type name (e.g., "Annual Leave") |
| `from_date` | date (Y-m-d) | Yes | Start date |
| `to_date` | date (Y-m-d) | Yes | End date (≥ from_date) |
| `purpose` | string | No | Reason for leave |
| `evidence` | file (jpg,png,pdf) | No | Supporting document (max 2MB) |

**Business Rules (mirroring ESS):**
1. **Overlapping Check:** Queries existing non-rejected leaves for date overlaps using four SQL cases (start-in, end-in, contained, contains).
2. **Balance Check:** Calls `LeaveRepository::calculateEmployeeLeaveBalance()` for the current financial year. Rejects if `balance < requested_days`.
3. **Day Calculation:** Calls `LeaveRepository::calculateTotalNumberOfLeaveDays()` which delegates to `Employee::appliedLeaveDays()` — respects working-days vs calendar-days settings and excludes weekends/holidays.
4. **Supervisor Assignment:** Automatically sets `approve_by = employee.supervisor_id`.
5. **Status Defaults:** `status = PENDING(1)`, `final_status = PENDING(1)`, `hr_approval = PENDING(1)`, `ceo_approval_type = PENDING(1)`.
6. **Notifications:** Sends `StaffLeaveApplicationMail` to employee and `SupervisorLeaveApplicationMail` to supervisor (if email exists).

**Response (Success):**
```json
{
  "status": "success",
  "message": "Leave application submitted successfully",
  "data": {
    "application_id": 123,
    "leave_type": "Annual Leave",
    "current_balance": 15,
    "requested_days": 3,
    "projected_balance": 12,
    "fiscal_year": "2025-2026",
    "dates": { "from": "2026-06-01", "to": "2026-06-03" },
    "supervisor": { "id": 5, "name": "Jane Doe", "email": "jane@example.com" }
  }
}
```

**ESS Equivalent:** `EssIndexController::leaveStore()` and `ApplyForLeaveController::store()` perform the same overlapping check, balance validation, and notification flow. The ESS web version additionally supports `justification_file` uploads stored in `uploads/leaveApplication/`.

---

### 2.5 Calculate Leave Days
**Endpoint:** `POST /api/leave/calculate-days`  
**Controller:** `Api\LeaveController@calculateLeaveDays`  
**Auth:** Sanctum

**Request:**
| Field | Type | Required |
|-------|------|----------|
| `leave_type_id` | integer | Yes |
| `application_from_date` | string (d/m/Y) | Yes |
| `application_to_date` | string (d/m/Y) | Yes |

**Response:**
```json
{ "status": "success", "data": 3 }
```

**ESS Equivalent:** `EssIndexController` does not expose a standalone calculate-days endpoint; the web frontend calculates days via JavaScript or inline repository calls.

---

### 2.6 Update a Leave Application
**Endpoint:** `PUT /api/leaves/{id}`  
**Controller:** `Api\LeaveController@update`  
**Auth:** Sanctum

**Description:** Updates an existing leave application before it is approved. (Implementation stub exists in controller.)

---

### 2.7 Recall a Leave Application
**Endpoint:** `POST /api/leaves/{id}/recall`  
**Controller:** `Api\LeaveController@recall`  
**Auth:** Sanctum

**Description:** Recalls an already approved leave application, triggering the recall approval workflow.

**ESS Equivalent:** Recall functionality exists in the ESS web interface via dedicated recall views.

---

### 2.8 Delete Justification Document
**Endpoint:** `DELETE /api/leaves/justification`  
**Controller:** `Api\LeaveController@deleteJustification`  
**Auth:** Sanctum

**Description:** Removes an uploaded justification file from a leave application.

---

## 3. Leave Approval

### 3.1 Supervisor: Approve Leave
**Endpoint:** `POST /api/leave/approve`  
**Controller:** `Api\LeaveController@approveLeave`  
**Auth:** Sanctum

**Request:**
| Field | Type | Required |
|-------|------|----------|
| `leave_application_id` | integer | Yes |
| `remarks` | string | No (max 500) |

**Business Rules:**
1. Validates that the authenticated user is a supervisor (`getSupervisorCode()` returns their `employee_id`).
2. Verifies the leave applicant is under their supervision (`employee.supervisor_id == supervisor_code`).
3. Updates:
   - `status = APPROVE(2)`
   - `approve_by = supervisor_code`
   - `approve_date = today`
   - `final_status = APPROVE(2)`
   - `remarks = request remarks`
4. Sends `StaffLeaveApprovalMail` to the employee.

**Response:**
```json
{
  "status": "success",
  "message": "Leave application approved successfully",
  "data": { /* updated LeaveApplication */ }
}
```

---

### 3.2 Supervisor: Reject Leave
**Endpoint:** `POST /api/leave/reject`  
**Controller:** `Api\LeaveController@rejectLeave`  
**Auth:** Sanctum

**Request:**
| Field | Type | Required |
|-------|------|----------|
| `leave_application_id` | integer | Yes |
| `remarks` | string | **Yes** (max 500) |

**Business Rules:**
1. Same supervisor authorization check as approve.
2. Updates:
   - `status = REJECT(3)`
   - `reject_by = supervisor_code`
   - `reject_date = today`
   - `final_status = REJECT(3)`
3. Sends `StaffLeaveRejectionMail` to the employee.

---

### 3.3 HR / CEO Approval (Legacy/Admin Flow)
**Endpoint:** `POST /api/leave/approval`  
**Controller:** `Api\LeaveApprovalController@processApproval`  
**Auth:** Sanctum

**Request:**
| Field | Type | Required |
|-------|------|----------|
| `approval_id` | integer | Yes |
| `action` | enum (`approved`, `rejected`) | Yes |
| `notes` | string | No |

**Business Rules:**
1. Determines role from `Auth::user()->role`.
2. **HR role:** updates `hr_approval` (2=approve, 3=reject) + `hr_approval_date` + `hr_approval_comments`.
3. **CEO role:** updates `ceo_approval_type` (2=approve, 3=reject) + `ceo_approval_date` + `ceo_approval_comments`.
4. **Final status auto-update:** If both `hr_approval == 2` and `ceo_approval_type == 2`, sets `status = 2` and `approve_by = user.id`.
5. If rejected, sets `status = 3`, `reject_date = now`, `reject_by = user.id`.

> **Note:** The ESS web interface does not expose a separate HR/CEO approval screen in `EssIndexController`; those approvals are handled through the general approval workflow (`approvalShow`, `HasApprovalWorkflow` trait). The API `LeaveApprovalController` provides a dedicated endpoint for HR/CEO leave actions.

---

### 3.4 List Pending Approvals (Supervisor)
**Endpoint:** `GET /api/leave/supervisor/pending`  
**Controller:** `Api\LeaveController@supervisorPendingLeaves`  
**Auth:** Sanctum

**Description:** Returns all pending leave applications for employees supervised by the authenticated user.

**ESS Equivalent:** The ESS approvals page (`EssIndexController::approval()`) uses the generic `HasApprovalWorkflow` trait and shows all pending approvals (not just leave). The API endpoint is leave-specific.

---

### 3.5 List Pending Leave Approvals (HR/CEO Dashboard)
**Endpoint:** `GET /api/leave-approvals/pending`  
**Controller:** `Api\LeaveApprovalController@getPendingApprovals`  
**Auth:** Sanctum

**Description:** Returns paginated pending leaves where `status`, `hr_approval`, `ceo_approval_type`, or `final_status` is pending (`1`).

---

### 3.6 Approval History
**Endpoint:** `GET /api/leave-approval/history`  
**Controller:** `Api\LeaveApprovalController@getApprovalHistory`  
**Auth:** Sanctum

**Query Parameters:**
| Parameter | Values | Description |
|-----------|--------|-------------|
| `approval_type` | `hr`, `ceo` | Filter by who acted |
| `status` | integer | Filter by leave status |

---

## 4. Leave Balance Check

### 4.1 Get Leave Balance
**Endpoint:** `GET /api/leave/balance`  
**Controller:** `Api\LeaveController@getEmployeeLeaveBalance`  
**Auth:** Sanctum

**Query Parameters:**
| Parameter | Type | Required |
|-----------|------|----------|
| `leave_type` | string (name) | Yes |

**Business Rules (mirroring ESS):**
1. Resolves `leave_type` name → `leave_type_id`.
2. Identifies the current financial year.
3. Calls `LeaveRepository::calculateEmployeeLeaveBalance()` which computes:
   - **Earned days** via `Employee::getEarnedLeaveDays()`
   - **Rollover days** from `LeaveRollover`
   - **Adjustments** (additions/deductions) from `LeaveAdjustment` (status = approved)
   - **Days used** from approved `LeaveApplication` records overlapping the fiscal year, respecting `working_days` vs `calendar_days` and excluding weekends/holidays.
4. Returns `balance = earned + rollover + adjustments - used`.

**Response:**
```json
{
  "status": "success",
  "data": {
    "leave_type": "Annual Leave",
    "balance": 12.5,
    "fiscal_year": "2025-2026"
  }
}
```

**ESS Equivalent:** `EssIndexController::leaveBalance()` returns the **enhanced balance** including advance leave:
```json
{
  "regular_balance": 12.5,
  "advance_available": 5.0,
  "total_available": 17.5,
  "is_advance_period": true,
  "advance_days_allowed": 5.0,
  "has_adjustments": true,
  "adjustment_additions": 2,
  "adjustment_deductions": 0,
  "net_adjustment": 2
}
```

> **Gap:** The API `getEmployeeLeaveBalance` only returns the regular balance. The ESS web version (`leaveBalance`) returns the advanced breakdown via `calculateEmployeeLeaveBalanceWithAdvanced()`. The API should be updated to use the same method for parity.

---

### 4.2 Check if Supervisor
**Endpoint:** `GET /api/leave/is-supervisor`  
**Controller:** `Api\LeaveController@isSupervisor`  
**Auth:** Sanctum

**Response:**
```json
{
  "status": "success",
  "is_supervisor": true,
  "message": "User is a supervisor"
}
```

---

## 5. Leave Reports

### 5.1 Personal Leave Report
**Endpoint:** `GET /api/leaves/reports/personal`  
**Controller:** `Api\LeaveController@personalLeaveReport`  
**Auth:** Sanctum

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| `status` | integer | Optional filter by `final_status` |

**Description:** Returns all leave applications for the authenticated employee within the current financial year.

---

### 5.2 Supervisor: Approved Leaves Report
**Endpoint:** `GET /api/leaves/reports/approved`  
**Controller:** `Api\LeaveController@approvedLeavesReport`  
**Auth:** Sanctum

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| `leave_type_id` | integer | Filter by leave type |
| `employee_id` | integer | Filter by specific supervised employee |

**Description:** Returns approved leaves for all supervised employees.

---

### 5.3 Supervisor: Rejected Leaves Report
**Endpoint:** `GET /api/supervisor/reports/rejected-leaves`  
**Controller:** `Api\LeaveController@rejectedLeavesReport`  
**Auth:** Sanctum

**Query Parameters:** Same as approved report.

---

### 5.4 Supervisor: Today’s Leave Applications
**Endpoint:** `GET /api/leaves/supervisor/today`  
**Controller:** `Api\LeaveController@supervisorTodayLeaves`  
**Auth:** Sanctum

**Description:** Returns leave applications created today by supervised employees.

---

### 5.5 Supervisor: Employees on Leave Today
**Endpoint:** `GET /api/employee/supervisor/employees-on-leave-today` (also `/api/supervisor/employees-on-leave-today`)  
**Controller:** `Api\LeaveController@supervisorEmployeesOnLeaveToday`  
**Auth:** Sanctum / API

**Description:** Returns approved leaves where `application_from_date <= today <= application_to_date` for supervised employees.

---

### 5.6 Supervisor: Today’s Leave Count
**Endpoint:** `GET /api/leaves/supervisor/today/count`  
**Controller:** `Api\LeaveController@supervisorTodayLeavesCount`  
**Auth:** Sanctum

**Description:** Returns the count of today’s leave applications for dashboard badges.

---

### 5.7 Get User Leaves (Legacy)
**Endpoint:** `GET /api/user/leaves`  
**Controller:** `Api\LeaveController@getUserLeaves`  
**Auth:** API token (`auth:api`)

**Description:** Simple list of all leave applications for the authenticated user, ordered by status then ID.

---

### 5.8 Get Supervisor Info
**Endpoint:** `GET /api/employee/supervisor`  
**Controller:** `Api\LeaveController@getSupervisor`  
**Auth:** Sanctum

**Response:**
```json
{
  "status": "success",
  "data": {
    "supervisor_id": 5,
    "first_name": "Jane",
    "last_name": "Doe",
    "email": "jane@example.com"
  }
}
```

---

## 6. Data Models & Status Codes

### 6.1 LeaveApplication Fillable Fields
```php
'employee_id',
'leave_type_id',
'application_from_date',
'application_to_date',
'application_date',
'number_of_day',
'approve_date', 'approve_by',
'reject_date', 'reject_by',
'purpose', 'remarks',
'status', 'hr_approval', 'hr_approval_date',
'final_status',
'ceo_approval_date', 'ceo_approval_type', 'ceo_approval_comments',
'application_type',
'financial_year_id'
```

### 6.2 LeaveStatus Enum
| Constant | Value | Meaning |
|----------|-------|---------|
| `PENDING` | 1 | Pending / Awaiting action |
| `APPROVE` | 2 | Approved |
| `REJECT` | 3 | Rejected |
| `RECALL` | 4 | Recalled |
| `RECALL_APPROVED` | 5 | Recall approved |

### 6.3 Approval Field Meanings
| Field | Value 1 | Value 2 | Value 3 |
|-------|---------|---------|---------|
| `status` | Pending | Approved | Rejected |
| `hr_approval` | Pending | Approved | Rejected |
| `ceo_approval_type` | Pending | Approved | Rejected |
| `final_status` | Pending | Approved | Rejected |

---

## 7. Comparison: ESS Web vs API

| Feature | ESS Web (`EssIndexController`) | API (`Api\LeaveController`) | Status |
|---------|-------------------------------|----------------------------|--------|
| List my leaves | `leave()` — Blade view | `index()` — JSON paginated | ✅ Aligned |
| Apply for leave | `leaveStore()` — form post | `applyLeave()` — JSON post | ✅ Aligned |
| Overlapping check | Yes (date range SQL) | Yes (4-case SQL) | ✅ Aligned |
| Balance check | `leaveBalance()` — advanced + adjustments | `getEmployeeLeaveBalance()` — basic only | ⚠️ **Gap: API missing advance/adjustment details** |
| Calculate days | Inline JS / repo call | `calculateLeaveDays()` | ✅ Aligned |
| Supervisor approve | Via generic approval workflow | `approveLeave()` | ✅ Aligned |
| Supervisor reject | Via generic approval workflow | `rejectLeave()` | ✅ Aligned |
| HR/CEO approve | Via generic approval workflow | `LeaveApprovalController@processApproval()` | ✅ Aligned |
| Recall leave | Web recall views | `recall()` stub | ⚠️ **Gap: API stub not fully implemented** |
| Justification upload | `leaveStore()` saves to disk | `applyLeave()` accepts `evidence` file | ✅ Aligned |
| Notifications | Email + database events | Email only (no event broadcast) | ⚠️ **Gap: API does not fire `LeaveApplicationEvent`** |
| Personal report | Web view | `personalLeaveReport()` | ✅ Aligned |
| Approved report | Web view | `approvedLeavesReport()` | ✅ Aligned |
| Rejected report | Web view | `rejectedLeavesReport()` | ✅ Aligned |
| Today’s leaves | Not explicit | `supervisorTodayLeaves()` | ✅ API only |
| Employees on leave today | Not explicit | `supervisorEmployeesOnLeaveToday()` | ✅ API only |

---

## 8. Identified Gaps & Recommendations

1. **Balance API should return advance info:**
   - `getEmployeeLeaveBalance()` calls `calculateEmployeeLeaveBalance()` which returns a single number.
   - ESS calls `calculateEmployeeLeaveBalanceWithAdvanced()` which returns regular, advance, total, adjustment details.
   - **Recommendation:** Update `getEmployeeLeaveBalance()` to use `calculateEmployeeLeaveBalanceWithAdvanced()` and return the same JSON structure as ESS.

2. **Missing event notifications in API:**
   - `EssIndexController::leaveStore()` fires `LeaveApplicationEvent` and sends `LeaveApplicationSubmitted` notifications to location approvers.
   - `Api\LeaveController::applyLeave()` only sends emails.
   - **Recommendation:** Add event broadcast and database notification logic to `applyLeave()`.

3. **Recall endpoint is a stub:**
   - `Api\LeaveController@recall` exists in routes but the implementation is incomplete.
   - **Recommendation:** Implement recall logic matching the web workflow (set `status = RECALL`, require supervisor re-approval).

4. **Duplicate routes:**
   - `POST /api/leave/apply` is registered multiple times under different middleware groups.
   - **Recommendation:** Consolidate routes in `routes/api.php` to avoid confusion.

5. **Supervisor code logic inconsistency:**
   - `getSupervisorCode()` returns the **current user’s own** `employee_id`, then checks if any employee has `supervisor_id == that id`. This is correct, but the method name is misleading.
   - **Recommendation:** Rename to `getCurrentUserEmployeeId()` or add inline documentation.

6. **Missing `number_of_day` auto-calculation in API apply:**
   - The API `applyLeave()` calculates `numberOfDays` via the repository but does not enforce the `number_of_day` field in validation.
   - The ESS web form requires `number_of_day` in `ApplyForLeaveRequest`.
   - **Recommendation:** Add `number_of_day` to API validation or auto-calculate and ignore the input.

---

## 9. Route Reference (Consolidated)

| Method | URI | Controller@Method | Middleware | Description |
|--------|-----|-------------------|------------|-------------|
| GET | `/api/leave-types` | `LeaveController@getLeaveTypes` | sanctum | List applicable leave types |
| GET | `/api/leaves` | `LeaveController@index` | sanctum | List my applications |
| GET | `/api/leaves/create` | `LeaveController@create` | sanctum | Form metadata |
| POST | `/api/leaves` | `LeaveController@store` | sanctum | Apply for leave (REST style) |
| PUT | `/api/leaves/{id}` | `LeaveController@update` | sanctum | Update application |
| GET | `/api/leaves/{id}` | `LeaveController@show` | sanctum | View application |
| GET | `/api/leaves/{id}/edit` | `LeaveController@edit` | sanctum | Edit metadata |
| POST | `/api/leaves/{id}/recall` | `LeaveController@recall` | sanctum | Recall application |
| DELETE | `/api/leaves/justification` | `LeaveController@deleteJustification` | sanctum | Remove evidence |
| POST | `/api/leave/apply` | `LeaveController@applyLeave` | sanctum | Apply for leave (legacy/mobile) |
| GET | `/api/leave/balance` | `LeaveController@getEmployeeLeaveBalance` | sanctum | Check balance |
| POST | `/api/leave/calculate-days` | `LeaveController@calculateLeaveDays` | sanctum | Calculate days |
| GET | `/api/leave/requested` | `LeaveController@requestedApplications` | sanctum | Requested apps |
| GET | `/api/leave/all-requested` | `LeaveController@allRequestedApplications` | sanctum | All requested |
| GET | `/api/leave/supervisor/pending` | `LeaveController@supervisorPendingLeaves` | sanctum | Pending for my team |
| POST | `/api/leaves/approve-reject` | `LeaveController@approveOrReject` | sanctum | Approve/reject |
| GET | `/api/leaves/reports/personal` | `LeaveController@personalLeaveReport` | sanctum | My report |
| GET | `/api/leaves/reports/approved` | `LeaveController@approvedLeavesReport` | sanctum | Approved team report |
| POST | `/api/leave/approve` | `LeaveController@approveLeave` | sanctum | Supervisor approve |
| POST | `/api/leave/reject` | `LeaveController@rejectLeave` | sanctum | Supervisor reject |
| GET | `/api/leave-approvals/pending` | `LeaveApprovalController@getPendingApprovals` | sanctum | HR/CEO pending list |
| GET | `/api/leave-approval/pending` | `LeaveApprovalController@getPendingApprovals` | sanctum | Alias |
| GET | `/api/leave-approval/history` | `LeaveApprovalController@getApprovalHistory` | sanctum | Approval history |
| POST | `/api/leave/approval` | `LeaveApprovalController@processApproval` | sanctum | HR/CEO action |
| GET | `/api/leaves/supervisor/today` | `LeaveController@supervisorTodayLeaves` | sanctum | Today’s apps |
| GET | `/api/leaves/supervisor/today/count` | `LeaveController@supervisorTodayLeavesCount` | sanctum | Today’s count |
| GET | `/api/supervisor/employees-on-leave-today` | `LeaveController@supervisorEmployeesOnLeaveToday` | api/sanctum | On-leave today |
| GET | `/api/employee/supervisor` | `LeaveController@getSupervisor` | sanctum | My supervisor info |
| GET | `/api/user/leaves` | `LeaveController@getUserLeaves` | api | Legacy user leaves |
| GET | `/api/leave/is-supervisor` | `LeaveController@isSupervisor` | sanctum | Check supervisor role |

---

*Document generated on 2026-05-14 based on codebase review of `EssIndexController`, `Api\LeaveController`, `Api\LeaveApprovalController`, `LeaveRepository`, `routes/api.php`, and related models.*
