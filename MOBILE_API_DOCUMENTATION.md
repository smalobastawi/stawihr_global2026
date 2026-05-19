# ESS / Employee Self-Service API Documentation

> **Base URL:** `https://your-domain.com/api`  
> **Authentication:** Laravel Sanctum (`Bearer <token>`)  
> **Document Version:** 1.0  
> **Generated:** 2026-05-05

---

## Table of Contents

1. [Authentication & Login](#1-authentication--login)
   - [1.1 Check Login Options](#11-check-login-options)
   - [1.2 Password Login](#12-password-login)
   - [1.3 Google Login (Firebase-ready)](#13-google-login-firebase-ready)
   - [1.4 Logout](#14-logout)
   - [1.5 Get Authenticated User](#15-get-authenticated-user)
   - [1.6 Forgot Password](#16-forgot-password)
   - [1.7 Reset Password](#17-reset-password)
   - [1.8 Validate Reset Token](#18-validate-reset-token)
   - [1.9 Verify Email & Generate Token](#19-verify-email--generate-token)
2. [Leave Management](#2-leave-management)
   - [2.1 Get Leave Types](#21-get-leave-types)
   - [2.2 Get Leave Balance](#22-get-leave-balance)
   - [2.3 Calculate Leave Days (skips holidays)](#23-calculate-leave-days-skips-holidays)
   - [2.4 Apply for Leave](#24-apply-for-leave)
   - [2.5 Get My Leave Applications](#25-get-my-leave-applications)
   - [2.6 Get Leave Application Details](#26-get-leave-application-details)
   - [2.7 Get Leave Form Data](#27-get-leave-form-data)
   - [2.8 Update Leave Application](#28-update-leave-application)
   - [2.9 Recall Leave Application](#29-recall-leave-application)
   - [2.10 Delete Justification Document](#210-delete-justification-document)
   - [2.11 Supervisor: Pending Leaves](#211-supervisor-pending-leaves)
   - [2.12 Supervisor: Approve Leave](#212-supervisor-approve-leave)
   - [2.13 Supervisor: Reject Leave](#213-supervisor-reject-leave)
   - [2.14 Supervisor: Today’s Leaves](#214-supervisor-todays-leaves)
   - [2.15 Supervisor: Employees on Leave Today](#215-supervisor-employees-on-leave-today)
   - [2.16 Check if Supervisor](#216-check-if-supervisor)
   - [2.17 Get Supervisor](#217-get-supervisor)
3. [Attendance](#3-attendance)
   - [3.1 Check In / Out](#31-check-in--out)
   - [3.2 Get My Attendance](#32-get-my-attendance)
   - [3.3 Daily Attendance Report](#33-daily-attendance-report)
   - [3.4 Weekly Attendance Report](#34-weekly-attendance-report)
   - [3.5 Monthly Attendance Report](#35-monthly-attendance-report)
   - [3.6 My Attendance Summary](#36-my-attendance-summary)
   - [3.7 Get My Work Shift](#37-get-my-work-shift)
   - [3.8 Supervisor: Supervised Employees Today](#38-supervisor-supervised-employees-today)
   - [3.9 Supervisor: Today’s Attendance Count](#39-supervisor-todays-attendance-count)
4. [Payroll (Self-Service)](#4-payroll-self-service)
   - [4.1 Get My Salary Details](#41-get-my-salary-details)
   - [4.2 Get Yearly Salary](#42-get-yearly-salary)
   - [4.3 Get Recent Payslips](#43-get-recent-payslips)
   - [4.4 Get Payslip URL](#44-get-payslip-url)
5. [Approvals](#5-approvals)
   - [5.1 Get Pending Approvals](#51-get-pending-approvals)
   - [5.2 Get Approval History](#52-get-approval-history)
   - [5.3 Get My Approval Requests](#53-get-my-approval-requests)
   - [5.4 Take Action on Approval](#54-take-action-on-approval)
   - [5.5 Get All Pending Approvals (Admin)](#55-get-all-pending-approvals-admin)
   - [5.6 Get All Approval History (Admin)](#56-get-all-approval-history-admin)
6. [Documents](#6-documents)
   - [6.1 List My Documents](#61-list-my-documents)
   - [6.2 Upload Document](#62-upload-document)
   - [6.3 View Document](#63-view-document)
   - [6.4 Update Document](#64-update-document)
   - [6.5 Delete Document](#65-delete-document)
7. [Feedback](#7-feedback)
   - [7.1 Get Feedback Categories](#71-get-feedback-categories)
   - [7.2 Submit Employee Feedback](#72-submit-employee-feedback)
   - [7.3 Get My Feedback](#73-get-my-feedback)
   - [7.4 Get Feedback Details](#74-get-feedback-details)
   - [7.5 Delete Feedback](#75-delete-feedback)
   - [7.6 Submit Anonymous Feedback](#76-submit-anonymous-feedback)
8. [Employee Profile](#8-employee-profile)
   - [8.1 Get My Profile](#81-get-my-profile)
   - [8.2 Get Department Profile](#82-get-department-profile)

---

## 1. Authentication & Login

All protected endpoints require the `Authorization: Bearer <token>` header.

### 1.1 Check Login Options

**Purpose:** Discover which login methods are enabled on the server.

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/checkloginOptions` |
| **Auth Required** | No |

**Response (200 OK)**
```json
{
  "passwordLogin": true,
  "googleLogin": true,
  "azureLogin": false
}
```

**Field Types**
| Field | Type | Description |
|-------|------|-------------|
| `passwordLogin` | `boolean` | Username/password login enabled |
| `googleLogin` | `boolean` | Google login enabled |
| `azureLogin` | `boolean` | Azure/SSO login enabled |

---

### 1.2 Password Login

**Purpose:** Authenticate with username and password. Returns a Sanctum Bearer token.

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/auth/login` |
| **Auth Required** | No |

**Request Body**
```json
{
  "username": "string (required)",
  "password": "string (required)"
}
```

**Response (200 OK)**
```json
{
  "success": true,
  "message": "Login successful",
  "accessToken": "1|laravel_sanctum_token...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "user_name": "john.doe",
    "profile_pic_url": "https://your-domain.com/Uploads/employeePhoto/photo.jpg"
  },
  "roles": ["Employee", "Supervisor"],
  "permissions": ["view_leave", "apply_leave"],
  "expires_at": "2026-05-05 17:55:04"
}
```

**Response (401 Unauthorized)**
```json
{
  "success": false,
  "message": "Invalid credentials"
}
```

**Field Types**
| Field | Type | Description |
|-------|------|-------------|
| `username` | `string` | User’s login username |
| `password` | `string` | User’s password |
| `accessToken` | `string` | Sanctum personal access token |
| `expires_at` | `string` | ISO 8601 datetime of token expiry |
| `roles` | `array<string>` | Assigned role names |
| `permissions` | `array<string>` | Effective permission names |

---

### 1.3 Google Login (Firebase-ready)

**Purpose:** Log in via Google / Firebase Authentication. The mobile app obtains an `idToken` from Firebase Auth (or directly from Google Sign-In), extracts the user’s email, name, photo, and Google ID, then sends them to this endpoint. The backend verifies the email exists, links/verifies the Google ID, and returns a Sanctum token.

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/auth/google-login` |
| **Auth Required** | No |

**Request Body**
```json
{
  "email": "string (required)",
  "google_id": "string (required)",
  "name": "string (required)",
  "photo": "string (nullable)",
  "platform": "string (nullable, enum: web|mobile)"
}
```

**Response (200 OK)**
```json
{
  "success": true,
  "message": "Successfully logged in with Google",
  "accessToken": "2|laravel_sanctum_token...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "user_name": "john.doe",
    "profile_pic_url": "https://your-domain.com/Uploads/employeePhoto/photo.jpg"
  },
  "roles": ["Employee"],
  "permissions": ["view_leave", "apply_leave"],
  "expires_at": "2026-05-05 17:55:04"
}
```

**Response (404 Not Found)**
```json
{
  "success": false,
  "message": "No account found with this email address. Please contact your administrator."
}
```

**Response (403 Forbidden)**
```json
{
  "success": false,
  "message": "Google account mismatch. Please contact your administrator."
}
```

**Field Types**
| Field | Type | Description |
|-------|------|-------------|
| `email` | `string` | Google account email (must match a User record) |
| `google_id` | `string` | Google unique user ID (`sub` from Firebase/Google) |
| `name` | `string` | Full name from Google profile |
| `photo` | `string\|null` | URL to Google profile photo |
| `platform` | `string\|null` | `"mobile"` recommended for mobile apps |

**Firebase Mobile Workflow**
1. Integrate Firebase Auth in your mobile app (Android/iOS/Flutter/React Native).
2. Trigger Google Sign-In via Firebase.
3. On success, Firebase returns an `idToken` and user info (`email`, `displayName`, `photoUrl`, `uid`).
4. **Do NOT send the Firebase `idToken` to this backend.** Instead, send the extracted `email`, `google_id` (Firebase `uid`), `name`, and `photo` to `POST /api/auth/google-login`.
5. The backend looks up the user by `email`, verifies the `google_id` against the stored `google_ids` array, and issues a Sanctum Bearer token.
6. Use the returned `accessToken` in the `Authorization: Bearer <token>` header for all subsequent requests.

> **Note:** The backend stores multiple Google IDs per user in the `google_ids` JSON array column, allowing the same account to work across different devices or Google accounts.

---

### 1.4 Logout

**Purpose:** Revoke all Sanctum tokens for the authenticated user.

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/auth/logout` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Response (200 OK)**
```json
{
  "success": true,
  "message": "Successfully logged out"
}
```

---

### 1.5 Get Authenticated User

**Purpose:** Retrieve the currently logged-in user’s profile, roles, and permissions.

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/auth/user` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Response (200 OK)**
```json
{
  "success": true,
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "user_name": "john.doe",
    "profile_pic_url": "https://your-domain.com/Uploads/employeePhoto/photo.jpg",
    "roles": ["Employee"],
    "permissions": ["view_leave", "apply_leave"]
  }
}
```

---

### 1.6 Forgot Password

**Purpose:** Request a password reset link via email.

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/forgot-password` |
| **Auth Required** | No |

**Request Body**
```json
{
  "email": "string (required, email)"
}
```

**Response (200 OK)**
```json
{
  "status": "success",
  "message": "Password reset link has been sent to your email"
}
```

---

### 1.7 Reset Password

**Purpose:** Reset password using token received via email.

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/reset-password` |
| **Auth Required** | No |

**Request Body**
```json
{
  "email": "string (required, email)",
  "password": "string (required, min:8)",
  "password_confirmation": "string (required, same as password)",
  "token": "string (required)"
}
```

**Response (200 OK)**
```json
{
  "status": "success",
  "message": "Password has been reset successfully"
}
```

---

### 1.8 Validate Reset Token

**Purpose:** Check if a password reset token is still valid (expires after 60 minutes).

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/validate-token` |
| **Auth Required** | No |

**Request Body**
```json
{
  "token": "string (required)",
  "email": "string (required, email)"
}
```

**Response (200 OK)**
```json
{
  "status": "success",
  "message": "Token is valid"
}
```

---

### 1.9 Verify Email & Generate Token

**Purpose:** Verify an email address exists and immediately generate a Sanctum token (useful for Firebase email-verification flows).

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/verify-email` |
| **Auth Required** | No |

**Request Body**
```json
{
  "email": "string (required, email)"
}
```

**Response (200 OK)**
```json
{
  "success": true,
  "message": "Email verified successfully",
  "accessToken": "3|laravel_sanctum_token...",
  "token_type": "Bearer",
  "user_id": 1,
  "roles": ["Employee"],
  "permissions": ["view_leave", "apply_leave"],
  "expires_at": "2026-05-05 17:55:04"
}
```

---

## 2. Leave Management

### 2.1 Get Leave Types

**Purpose:** Retrieve leave types applicable to the authenticated employee, based on their leave group assignment.

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/leave-types` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Response (200 OK)**
```json
{
  "status": "success",
  "message": "Employee leave types retrieved successfully",
  "data": [
    {
      "leave_type_id": 1,
      "leave_type_name": "Annual Leave",
      "status": 1,
      "annual_entitlement": 21,
      "max_carryover_days": 5
    }
  ]
}
```

**Field Types**
| Field | Type | Description |
|-------|------|-------------|
| `leave_type_id` | `integer` | Unique leave type ID |
| `leave_type_name` | `string` | Display name of leave type |
| `status` | `integer` | `1` = active, `0` = inactive |
| `annual_entitlement` | `integer\|null` | Days entitled per year (from leave group settings) |
| `max_carryover_days` | `integer\|null` | Max days that can be carried over |

---

### 2.2 Get Leave Balance

**Purpose:** Check remaining leave balance for a specific leave type in the current financial year.

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/leave/balance` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Query Parameters**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `leave_type` | `string` | Yes | Name of the leave type (e.g. `"Annual Leave"`) |

**Response (200 OK)**
```json
{
  "status": "success",
  "data": {
    "leave_type": "Annual Leave",
    "balance": 12,
    "fiscal_year": "2026"
  }
}
```

**Field Types**
| Field | Type | Description |
|-------|------|-------------|
| `leave_type` | `string` | Leave type name queried |
| `balance` | `number` | Remaining days (can be integer or float) |
| `fiscal_year` | `string` | Current financial year label |

---

### 2.3 Calculate Leave Days (skips holidays)

**Purpose:** Calculate the actual number of leave days between two dates, **excluding weekends and public holidays** based on the employee’s leave group settings. This is the core logic used by the web ESS leave application.

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/leave/calculate-days` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Request Body**
```json
{
  "leave_type_id": 1,
  "application_from_date": "05/05/2026",
  "application_to_date": "12/05/2026"
}
```

**Field Types (Request)**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `leave_type_id` | `integer` | Yes | ID of the leave type |
| `application_from_date` | `string` | Yes | Start date in `d/m/Y` format |
| `application_to_date` | `string` | Yes | End date in `d/m/Y` format |

**Response (200 OK)**
```json
{
  "status": "success",
  "data": 6
}
```

> **How it works:** The backend checks the employee’s `leave_group` settings. If the leave type is configured for `working_days`, it iterates each date in the range and skips dates that fall on:
> - Weekly holidays (e.g., Saturday, Sunday) configured for the leave group.
> - Public holidays configured for the leave group.
>
> If configured for `calendar_days`, it counts all days inclusive.

---

### 2.4 Apply for Leave

**Purpose:** Submit a new leave application.

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/leave/apply` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Request Body**
```json
{
  "leave_type": "Annual Leave",
  "from_date": "2026-05-10",
  "to_date": "2026-05-14",
  "purpose": "Family vacation",
  "evidence": "<file (jpg,jpeg,png,pdf, max 2MB)>"
}
```

**Field Types (Request)**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `leave_type` | `string` | Yes | Name of the leave type |
| `from_date` | `string (date)` | Yes | Start date (`Y-m-d`) |
| `to_date` | `string (date)` | Yes | End date (`Y-m-d`), must be >= `from_date` |
| `purpose` | `string\|null` | No | Reason for leave |
| `evidence` | `file\|null` | No | Supporting document (image or PDF, max 2MB) |

**Response (200 OK)**
```json
{
  "status": "success",
  "message": "Leave application submitted successfully",
  "data": {
    "application_id": 42,
    "leave_type": "Annual Leave",
    "current_balance": 12,
    "requested_days": 5,
    "projected_balance": 7,
    "fiscal_year": "2026",
    "dates": {
      "from": "2026-05-10",
      "to": "2026-05-14"
    },
    "supervisor": {
      "id": 5,
      "name": "Jane Smith",
      "email": "jane@example.com"
    },
    "balance_calculation_note": "Current balance is based on leave type and fiscal year. Projected balance assumes leave approval."
  }
}
```

**Response (400 Bad Request - Overlapping Leave)**
```json
{
  "status": "error",
  "message": "You already have a leave application within this period. Please select different periods"
}
```

**Response (400 Bad Request - Insufficient Balance)**
```json
{
  "status": "error",
  "message": "Insufficient leave balance",
  "data": {
    "available_balance": 3,
    "requested_days": 5,
    "deficit": 2,
    "fiscal_year": "2026",
    "note": "Balance is calculated purely based on leave type, not dates"
  }
}
```

---

### 2.5 Get My Leave Applications

**Purpose:** List all leave applications for the authenticated employee.

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/leaves` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Query Parameters**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `per_page` | `integer` | No | Pagination page size (default: 10) |

**Response (200 OK)**
```json
{
  "status": "success",
  "message": "Leave applications retrieved successfully",
  "data": {
    "current_page": 1,
    "data": [
      {
        "leave_application_id": 42,
        "employee_id": 10,
        "leave_type_id": 1,
        "application_from_date": "2026-05-10",
        "application_to_date": "2026-05-14",
        "number_of_day": 5,
        "purpose": "Family vacation",
        "status": 1,
        "final_status": 1,
        "application_date": "2026-05-05",
        "employee": {
          "employee_id": 10,
          "first_name": "John",
          "last_name": "Doe"
        },
        "leaveType": {
          "leave_type_id": 1,
          "leave_type_name": "Annual Leave"
        },
        "approveBy": null,
        "rejectBy": null
      }
    ],
    "total": 1
  },
  "financial_year": {
    "start_date": "2026-01-01",
    "end_date": "2026-12-31"
  }
}
```

**Status Codes**
| Value | Meaning |
|-------|---------|
| `0` | Pending |
| `1` | Approved |
| `2` | Rejected |

---

### 2.6 Get Leave Application Details

**Purpose:** Retrieve a single leave application by ID.

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/leaves/{id}` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Response (200 OK)**
```json
{
  "status": "success",
  "data": { /* LeaveApplication object */ }
}
```

---

### 2.7 Get Leave Form Data

**Purpose:** Retrieve dropdown data needed to render a leave application form (leave types, employee info, employee list).

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/leaves/create` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Response (200 OK)**
```json
{
  "status": "success",
  "data": {
    "leave_type": [ /* array of LeaveType objects */ ],
    "employee_info": { /* Employee details object */ },
    "employee_list": [ /* array of employees for leave delegation/selection */ ]
  }
}
```

---

### 2.8 Update Leave Application

**Purpose:** Update an existing leave application (if still pending).

| | |
|---|---|
| **Method** | `PUT` |
| **Endpoint** | `/leaves/{id}` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Request Body**
Same fields as **Apply for Leave** (all optional for update).

**Response (200 OK)**
```json
{
  "status": "success",
  "message": "Leave application updated successfully",
  "data": { /* updated LeaveApplication */ }
}
```

---

### 2.9 Recall Leave Application

**Purpose:** Recall (cancel) a submitted leave application.

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/leaves/{id}/recall` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Response (200 OK)**
```json
{
  "status": "success",
  "message": "Leave application recalled successfully"
}
```

---

### 2.10 Delete Justification Document

**Purpose:** Remove an uploaded evidence/justification file from a leave application.

| | |
|---|---|
| **Method** | `DELETE` |
| **Endpoint** | `/leaves/justification` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Request Body**
```json
{
  "leave_application_id": 42
}
```

**Response (200 OK)**
```json
{
  "status": "success",
  "message": "Justification document deleted successfully"
}
```

---

### 2.11 Supervisor: Pending Leaves

**Purpose:** Get pending leave applications for employees under the supervisor’s supervision.

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/leaves/supervisor/pending` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Response (200 OK)**
```json
{
  "status": "success",
  "count": 2,
  "financial_year": {
    "start_date": "2026-01-01",
    "end_date": "2026-12-31"
  },
  "data": [
    {
      "leave_application_id": 42,
      "employee_id": 10,
      "leave_type_id": 1,
      "application_from_date": "2026-05-10",
      "application_to_date": "2026-05-14",
      "number_of_day": 5,
      "status": 1,
      "final_status": 1,
      "employee": { /* ... */ },
      "leaveType": { /* ... */ }
    }
  ]
}
```

---

### 2.12 Supervisor: Approve Leave

**Purpose:** Approve a leave application as a supervisor.

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/leave/approve` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Request Body**
```json
{
  "leave_application_id": 42,
  "remarks": "Approved, enjoy your leave"
}
```

**Field Types**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `leave_application_id` | `integer` | Yes | ID of the leave application |
| `remarks` | `string\|null` | No | Approval comments |

**Response (200 OK)**
```json
{
  "status": "success",
  "message": "Leave application approved successfully",
  "data": { /* updated LeaveApplication */ }
}
```

---

### 2.13 Supervisor: Reject Leave

**Purpose:** Reject a leave application as a supervisor.

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/leave/reject` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Request Body**
```json
{
  "leave_application_id": 42,
  "remarks": "Insufficient coverage during this period"
}
```

**Field Types**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `leave_application_id` | `integer` | Yes | ID of the leave application |
| `remarks` | `string` | Yes | Rejection reason |

**Response (200 OK)**
```json
{
  "status": "success",
  "message": "Leave application rejected successfully",
  "data": { /* updated LeaveApplication */ }
}
```

---

### 2.14 Supervisor: Today’s Leaves

**Purpose:** Get leave applications submitted today by supervised employees.

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/leaves/supervisor/today` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Response (200 OK)**
```json
{
  "status": "success",
  "today": "2026-05-05",
  "count": 1,
  "data": [ /* LeaveApplication objects */ ]
}
```

---

### 2.15 Supervisor: Employees on Leave Today

**Purpose:** Get supervised employees who are currently on approved leave today.

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/supervisor/employees-on-leave-today` |
| **Auth Required** | Yes (`auth:api` or `auth:sanctum`) |

**Response (200 OK)**
```json
{
  "status": "success",
  "today": "2026-05-05",
  "count": 2,
  "data": [ /* LeaveApplication objects with employee & leaveType */ ]
}
```

---

### 2.16 Check if Supervisor

**Purpose:** Determine whether the authenticated user is a supervisor.

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/leave/is-supervisor` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Response (200 OK)**
```json
{
  "status": "success",
  "is_supervisor": true,
  "message": "User is a supervisor"
}
```

---

### 2.17 Get Supervisor

**Purpose:** Get the supervisor details of the currently logged-in employee.

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/supervisor` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Response (200 OK)**
```json
{
  "status": "success",
  "data": {
    "supervisor_id": 5,
    "first_name": "Jane",
    "last_name": "Smith",
    "email": "jane@example.com"
  }
}
```

---

## 3. Attendance

### 3.1 Check In / Out

**Purpose:** Record attendance check-in or check-out via mobile app.

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/attendance/checkin` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Request Body**
```json
{
  "attendanceType": "checkIn",
  "ip_check_status": 0
}
```

**Field Types**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `attendanceType` | `string` | Yes | `"checkIn"` or `"checkOut"` |
| `ip_check_status` | `integer` | No | `1` = enforce IP whitelist, `0` = skip |

**Response (200 OK)**
```json
{
  "success": "Attendance updated."
}
```

**Response (400 Bad Request)**
```json
{
  "error": "You have already checked in today."
}
```

---

### 3.2 Get My Attendance

**Purpose:** Retrieve all attendance records for the authenticated employee.

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/attendance/get` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Response (200 OK)**
```json
{
  "attendances": [
    {
      "id": 1,
      "employee_id": 10,
      "date": "2026-05-05",
      "time_in": "2026-05-05 08:30:00",
      "time_out": "2026-05-05 17:30:00",
      "presence_status": "PRESENT",
      "working_time": 9,
      "over_time": 0,
      "late_time": 0
    }
  ]
}
```

---

### 3.3 Daily Attendance Report

**Purpose:** Get daily attendance. Supervisors see team data; regular employees see only their own.

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/attendance/daily` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Query Parameters**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `date` | `string` | No | Date in `d/m/Y` (default: today) |
| `department_id` | `integer` | No | Filter by department (supervisor only) |
| `employee_type_id` | `integer` | No | Filter by employee type (supervisor only) |
| `work_shift_id` | `integer` | No | Filter by work shift (supervisor only) |

**Response (200 OK)**
```json
{
  "success": true,
  "results": [ /* attendance records */ ],
  "date": "05/05/2026"
}
```

---

### 3.4 Weekly Attendance Report

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/attendance/weekly` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Query Parameters**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `date` | `string` | No | Date in `Y-m-d` (default: today) |

---

### 3.5 Monthly Attendance Report

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/attendance/monthly` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Query Parameters**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `from_date` | `string` | No | Start date `d/m/Y` |
| `to_date` | `string` | No | End date `d/m/Y` |
| `employee_id` | `integer` | No | Specific employee (supervisor only) |

---

### 3.6 My Attendance Summary

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/attendance/my-summary` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Query Parameters**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `from_date` | `string` | No | `d/m/Y` |
| `to_date` | `string` | No | `d/m/Y` |

**Response (200 OK)**
```json
{
  "success": true,
  "results": [ /* monthly attendance summary rows */ ],
  "from_date": "26/04/2026",
  "to_date": "25/05/2026",
  "employee_id": 10
}
```

---

### 3.7 Get My Work Shift

**Purpose:** Retrieve the work shift assigned to the logged-in employee.

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/my-work-shift` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Response (200 OK)**
```json
{
  "success": true,
  "work_shift": {
    "id": 1,
    "name": "Morning Shift",
    "start_time": "08:00:00",
    "end_time": "17:00:00",
    "late_count_time": "08:15:00",
    "overtime_count_time": "17:30:00",
    "formatted": {
      "start_time": "08:00 AM",
      "end_time": "05:00 PM",
      "late_count_time": "08:15 AM",
      "overtime_count_time": "05:30 PM"
    }
  }
}
```

---

### 3.8 Supervisor: Supervised Employees Today

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/attendance/supervised-employees/today` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Response (200 OK)**
```json
{
  "success": true,
  "date": "2026-05-05",
  "total_records": 5,
  "results": [ /* attendance records */ ]
}
```

---

### 3.9 Supervisor: Today’s Attendance Count

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/attendance/supervised-employees/today/count` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Response (200 OK)**
```json
{
  "success": true,
  "date": "2026-05-05",
  "total_supervised_employees": 10,
  "present_count": 8,
  "absent_count": 2,
  "late_count": 1,
  "attendance_percentage": 80.0
}
```

---

## 4. Payroll (Self-Service)

### 4.1 Get My Salary Details

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/payroll/salary-details` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Response (200 OK)**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "employee_id": 10,
    "basic_salary": 100000.00,
    "total_allowances": 15000.00,
    "gross_salary": 115000.00,
    "total_deductions": 5000.00,
    "paye_tax": 15000.00,
    "nssf_contribution": 2000.00,
    "shif_contribution": 1700.00,
    "housing_levy": 1500.00,
    "net_salary": 90500.00,
    "payment_date": "2026-04-30",
    "payment_reference": "PAY-2026-04-001",
    "period_name": "April 2026",
    "period_month": "April 2026"
  }
}
```

---

### 4.2 Get Yearly Salary

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/payroll/yearly-salary` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Query Parameters**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `year` | `integer` | No | Year to summarize (default: current year) |

**Response (200 OK)**
```json
{
  "status": "success",
  "data": {
    "employee_id": 10,
    "year": 2026,
    "number_of_months": 4,
    "total_basic_salary": 400000.00,
    "total_allowances": 60000.00,
    "total_deductions": 20000.00,
    "total_paye": 60000.00,
    "total_nssf": 8000.00,
    "total_shif": 6800.00,
    "total_housing_levy": 6000.00,
    "total_net_pay": 362000.00,
    "total_gross_pay": 460000.00
  }
}
```

---

### 4.3 Get Recent Payslips

**Purpose:** Get payslips for the last 6 months.

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/payroll/recent-payslips` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Response (200 OK)**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "month": 4,
      "year": 2026,
      "month_name": "April",
      "basic_salary": 100000.00,
      "total_allowance": 15000.00,
      "total_deduction": 5000.00,
      "tax": 15000.00,
      "nssf_amount": 2000.00,
      "net_salary": 90500.00,
      "gross_salary": 115000.00,
      "shif_contribution": 1700.00,
      "housing_levy": 1500.00
    }
  ]
}
```

---

### 4.4 Get Payslip URL

**Purpose:** Get a web URL to view/download a specific payslip.

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/payroll/payslip/{id}` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Response (200 OK)**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "url": "https://your-domain.com/ess/payroll/myPayroll/generatePayslip/1",
    "period_name": "April 2026",
    "period_month": "April 2026"
  }
}
```

---

## 5. Approvals

### 5.1 Get Pending Approvals

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/pending-approvals` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Response (200 OK)**
```json
{
  "status": "success",
  "data": [ /* approval request objects */ ]
}
```

---

### 5.2 Get Approval History

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/approval-history` |
| **Auth Required** | Yes (`auth:sanctum`) |

---

### 5.3 Get My Approval Requests

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/approval-requests` |
| **Auth Required** | Yes (`auth:sanctum`) |

---

### 5.4 Take Action on Approval

**Purpose:** Approve or reject a leave/approval request.

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/approval-action` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Request Body**
```json
{
  "leave_application_id": 42,
  "employee_id": 10,
  "action": "approved",
  "notes": "Looks good"
}
```

**Field Types**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `leave_application_id` | `integer` | Yes | Leave application ID |
| `employee_id` | `integer` | Yes | Employee user ID |
| `action` | `string` | Yes | `"approved"` or `"rejected"` |
| `notes` | `string\|null` | No | Comments |

**Response (200 OK)**
```json
{
  "status": "success",
  "message": "Leave application has been approved",
  "data": {
    "leave_application_id": 42,
    "employee_id": 10,
    "action": "approved",
    "action_by": 5
  }
}
```

---

### 5.5 Get All Pending Approvals (Admin)

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/approvals/pending/all` |
| **Auth Required** | Yes (`auth:sanctum`) |

---

### 5.6 Get All Approval History (Admin)

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/all-approval-history` |
| **Auth Required** | Yes (`auth:sanctum`) |

---

## 6. Documents

### 6.1 List My Documents

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/documents` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Query Parameters**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `document_type` | `string` | No | Filter by document type |
| `location_id` | `integer` | No | Filter by location |

**Response (200 OK)**
```json
{
  "status": "success",
  "data": [ /* EmployeeDocuments objects */ ]
}
```

---

### 6.2 Upload Document

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/documents` |
| **Auth Required** | Yes (`auth:sanctum`) |
| **Content-Type** | `multipart/form-data` |

**Request Body (multipart)**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `document_name` | `string` | Yes | Name of the document |
| `document_type` | `string` | Yes | Type/category |
| `national_id` | `string` | Yes | Employee national ID |
| `location_id` | `integer` | No | Location ID |
| `document` | `file` | No | File (PDF, JPEG, PNG, max 5MB) |

**Response (201 Created)**
```json
{
  "status": "success",
  "message": "Document uploaded successfully",
  "data": { /* EmployeeDocuments object */ }
}
```

---

### 6.3 View Document

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/documents/{uuid}` |
| **Auth Required** | Yes (`auth:sanctum`) |

---

### 6.4 Update Document

| | |
|---|---|
| **Method** | `PUT` |
| **Endpoint** | `/documents/{uuid}` |
| **Auth Required** | Yes (`auth:sanctum`) |
| **Content-Type** | `multipart/form-data` |

---

### 6.5 Delete Document

| | |
|---|---|
| **Method** | `DELETE` |
| **Endpoint** | `/documents/{uuid}` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Response (200 OK)**
```json
{
  "status": "success",
  "message": "Document deleted successfully"
}
```

---

## 7. Feedback

### 7.1 Get Feedback Categories

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/feedback/categories` |
| **Auth Required** | No |

**Response (200 OK)**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "General",
      "status": "active"
    }
  ]
}
```

---

### 7.2 Submit Employee Feedback

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/feedback` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Request Body**
```json
{
  "title": "string (required, max:255)",
  "content": "string (required)",
  "category_id": 1
}
```

**Response (201 Created)**
```json
{
  "success": true,
  "message": "Feedback submitted successfully",
  "data": { /* EmployeeFeedback object */ }
}
```

---

### 7.3 Get My Feedback

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/feedback` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Response (200 OK)**
```json
{
  "success": true,
  "data": [ /* EmployeeFeedback objects with category & response */ ]
}
```

---

### 7.4 Get Feedback Details

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/feedback/{id}` |
| **Auth Required** | Yes (`auth:sanctum`) |

---

### 7.5 Delete Feedback

| | |
|---|---|
| **Method** | `DELETE` |
| **Endpoint** | `/feedback/{id}` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Response (200 OK)**
```json
{
  "success": true,
  "message": "Feedback deleted successfully"
}
```

---

### 7.6 Submit Anonymous Feedback

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/feedback/anonymous` |
| **Auth Required** | No |

**Request Body**
```json
{
  "title": "string (required, max:255)",
  "content": "string (required)",
  "category_id": 1
}
```

**Response (201 Created)**
```json
{
  "success": true,
  "message": "Anonymous feedback submitted successfully",
  "data": { /* AnonymousFeedback object */ }
}
```

---

## 8. Employee Profile

### 8.1 Get My Profile

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/employee/profile` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Response (200 OK)**
```json
{
  "employee_id": 10,
  "first_name": "John",
  "last_name": "Doe",
  "email": "john@example.com",
  "department": { /* Department object */ },
  "branch": { /* Branch object */ },
  "payGrade": { /* PayGrade object */ }
}
```

---

### 8.2 Get Department Profile

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/department/profile` |
| **Auth Required** | Yes (`auth:sanctum`) |

**Response (200 OK)**
```json
{
  "department_id": 3,
  "department_name": "Human Resources"
}
```

---

## Appendix A: Common Data Types

### LeaveApplication Object
```json
{
  "leave_application_id": 42,
  "employee_id": 10,
  "leave_type_id": 1,
  "application_from_date": "2026-05-10",
  "application_to_date": "2026-05-14",
  "application_date": "2026-05-05",
  "number_of_day": 5,
  "purpose": "Family vacation",
  "status": 1,
  "final_status": 1,
  "approve_by": null,
  "approve_date": null,
  "reject_by": null,
  "reject_date": null,
  "remarks": null,
  "evidence": "leave_evidence/file.jpg",
  "financial_year_id": 1,
  "created_at": "2026-05-05T10:00:00.000000Z",
  "updated_at": "2026-05-05T10:00:00.000000Z"
}
```

**Status Values**
| Value | Meaning |
|-------|---------|
| `0` | Pending |
| `1` | Approved |
| `2` | Rejected |

### Employee Object (Summary)
```json
{
  "employee_id": 10,
  "first_name": "John",
  "last_name": "Doe",
  "email": "john@example.com",
  "phone": "254712345678",
  "department_id": 3,
  "designation_id": 5,
  "supervisor_id": 5,
  "work_shift_id": 1,
  "date_of_joining": "2020-01-15",
  "status": 1
}
```

---

## Appendix B: Firebase Google Login Mobile Integration Guide

### Recommended Workflow for Mobile Apps

```
┌─────────────────┐
│  Mobile App     │
│  (Firebase Auth)│
└────────┬────────┘
         │ 1. Sign in with Google via Firebase
         ▼
┌─────────────────┐
│  Firebase Auth  │
│  (returns user  │
│   email, uid,   │
│   displayName,  │
│   photoUrl)     │
└────────┬────────┘
         │ 2. Extract user info
         ▼
┌─────────────────────────────┐
│  POST /api/auth/google-login │
│  Body: {                     │
│    email: user.email,        │
│    google_id: user.uid,      │
│    name: user.displayName,   │
│    photo: user.photoUrl,     │
│    platform: "mobile"        │
│  }                           │
└────────┬─────────────────────┘
         │ 3. Receive Sanctum token
         ▼
┌─────────────────────────────┐
│  Store accessToken locally   │
│  Use in Authorization header │
│  for all subsequent requests │
└─────────────────────────────┘
```

### Key Points
- The backend **does not verify Firebase ID tokens** directly. It trusts the `email` + `google_id` pair after the first successful link.
- On first login, the backend adds the provided `google_id` to the user’s `google_ids` array.
- Subsequent logins from the same or different devices with the same `google_id` will succeed.
- If the `google_id` is new but the email exists, it is automatically appended to `google_ids` (allowing multiple Google accounts per user).
- If the email does not exist in the `user` table, the request returns **404**.

### Environment Variables (Server-side)
The following `.env` variables control login behavior:

| Variable | Type | Description |
|----------|------|-------------|
| `PASSWORD_LOGIN` | `boolean` | Enable username/password login |
| `GOOGLE_LOGIN` | `boolean` | Enable Google login |
| `AZURE_LOGIN` | `boolean` | Enable Azure/SSO login |
| `2FA_LOGIN` | `boolean` | Enable OTP-based 2FA |
| `SANCTUM_EXPIRATION` | `integer` | Token lifetime in minutes |

---

## Appendix C: Error Response Patterns

Most endpoints return errors in one of these shapes:

**Standard Error (Validation)**
```json
{
  "status": "error",
  "message": "Validation error",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

**Standard Error (General)**
```json
{
  "status": "error",
  "message": "Human-readable error message",
  "error": "Debug message (only in debug mode)"
}
```

**Auth Error**
```json
{
  "status": "error",
  "message": "User not authenticated"
}
```

**Success Pattern**
```json
{
  "status": "success",
  "message": "Human-readable success message",
  "data": { /* payload */ }
}
```

---

*End of Document*
