# Leave Management API Documentation

## Base URL

```
/api/leave
```

## Authentication

All endpoints require Bearer Token authentication.

```
Authorization: Bearer {token}
```

---

## Overview

This API is organized into two main sections:

1. **ESS (Employee Self Service) Endpoints** - For employees to manage their own leave applications
2. **Supervisor Endpoints** - For supervisors to approve/reject leaves and view team reports

---

# ESS (Employee Self Service) Endpoints

These endpoints allow employees to manage their own leave applications.

### 1. List My Leave Applications

Get all leave applications for the authenticated employee.

**Endpoint:** `GET /api/leave`

**Query Parameters:**
| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| per_page | integer | 10 | Number of items per page |

**Response (200 Success):**

```json
{
  "status": "success",
  "message": "Leave applications retrieved successfully",
  "data": {
    "current_page": 1,
    "data": [
      {
        "leave_application_id": 1,
        "employee_id": 123,
        "leave_type_id": 2,
        "application_from_date": "2026-02-01",
        "application_to_date": "2026-02-05",
        "application_date": "2026-01-31",
        "number_of_day": 5,
        "purpose": "Annual vacation",
        "status": 0,
        "final_status": 0,
        "approve_by": 456,
        "approve_date": null,
        "reject_by": null,
        "reject_date": null,
        "remarks": null,
        "evidence": "leave_evidence/filename.jpg",
        "financial_year_id": 1,
        "ceo_approval_type": 0,
        "hr_approval": 0,
        "reliever_id": null,
        "created_at": "2026-01-31T10:00:00.000000Z",
        "updated_at": "2026-01-31T10:00:00.000000Z",
        "employee": {
          "employee_id": 123,
          "first_name": "John",
          "last_name": "Doe"
        },
        "leaveType": {
          "leave_type_id": 2,
          "leave_type_name": "Annual Leave"
        },
        "approveBy": {
          "employee_id": 456,
          "first_name": "Jane",
          "last_name": "Smith"
        },
        "rejectBy": null
      }
    ],
    "first_page_url": "http://example.com/api/leave?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://example.com/api/leave?page=1",
    "next_page_url": null,
    "path": "http://example.com/api/leave",
    "per_page": 10,
    "prev_page_url": null,
    "to": 1,
    "total": 1
  },
  "financial_year": {
    "start_date": "2026-01-01",
    "end_date": "2026-12-31"
  }
}
```

---

### 2. Create Leave Application

Submit a new leave application.

**Endpoint:** `POST /api/leave`

**Content-Type:** `multipart/form-data`

**Request Body:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| leave_type_id | integer | Yes | ID of the leave type |
| application_from_date | date | Yes | Start date (format: Y-m-d or d/m/Y) |
| application_to_date | date | Yes | End date (format: Y-m-d or d/m/Y) |
| number_of_day | numeric | Yes | Number of days requested |
| purpose | string | Yes | Reason for leave |
| reliever_id | integer | No | ID of the relieving employee |
| justification_file[] | file | No | Supporting documents (multiple files allowed, max 2048KB each) |

**Request Example:**

```json
{
  "leave_type_id": 2,
  "application_from_date": "01/02/2026",
  "application_to_date": "05/02/2026",
  "number_of_day": 5,
  "purpose": "Annual vacation with family",
  "reliever_id": 789
}
```

**Response (201 Created):**

```json
{
  "status": "success",
  "message": "Leave application submitted successfully",
  "data": {
    "application_id": 15,
    "leave_type": "Annual Leave",
    "dates": {
      "from": "01/02/2026",
      "to": "05/02/2026"
    },
    "number_of_days": 5,
    "supervisor": {
      "id": 456,
      "name": "Jane Smith",
      "email": "jane.smith@company.com"
    }
  }
}
```

**Response (400 Bad Request - Overlapping Leave):**

```json
{
  "status": "error",
  "message": "You already have a leave application within this period. Please select different periods"
}
```

**Response (400 Bad Request - No Supervisor):**

```json
{
  "status": "error",
  "message": "You do not have a supervisor assigned. Please contact HR to assign a supervisor before applying for leave."
}
```

---

### 3. Get Leave Application Details

Get detailed information about a specific leave application.

**Endpoint:** `GET /api/leave/{id}`

**Path Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| id | integer | Leave application ID |

**Response (200 Success):**

```json
{
  "status": "success",
  "data": {
    "leave_application": {
      "leave_application_id": 1,
      "employee_id": 123,
      "leave_type_id": 2,
      "application_from_date": "2026-02-01",
      "application_to_date": "2026-02-05",
      "number_of_day": 5,
      "purpose": "Annual vacation",
      "status": 0,
      "final_status": 0,
      "employee": {
        "employee_id": 123,
        "first_name": "John",
        "last_name": "Doe",
        "designation": {
          "designation_id": 5,
          "designation_name": "Software Engineer"
        }
      },
      "leaveType": {
        "leave_type_id": 2,
        "leave_type_name": "Annual Leave"
      },
      "justification": [
        {
          "id": 1,
          "file_name": "medical_certificate.pdf",
          "created_at": "2026-01-31T10:00:00.000000Z"
        }
      ],
      "approveBy": null,
      "rejectBy": null
    },
    "supervisor_details": {
      "employee_id": 456,
      "first_name": "Jane",
      "last_name": "Smith",
      "email": "jane.smith@company.com"
    },
    "current_balance": 15
  }
}
```

---

### 4. Get Leave Application for Editing

Get leave application data pre-populated for editing form.

**Endpoint:** `GET /api/leave/{id}/edit`

**Path Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| id | integer | Leave application ID |

**Response (200 Success):**

```json
{
  "status": "success",
  "data": {
    "leave_application": {
      "leave_application_id": 1,
      "leave_type_id": 2,
      "application_from_date": "2026-02-01",
      "application_to_date": "2026-02-05",
      "number_of_day": 5,
      "purpose": "Annual vacation",
      "reliever_id": 789,
      "justification": [
        {
          "id": 1,
          "file_name": "document.pdf"
        }
      ]
    },
    "leave_types": {
      "1": "Sick Leave",
      "2": "Annual Leave",
      "3": "Maternity Leave"
    },
    "employee_info": {
      "employee_id": 123,
      "first_name": "John",
      "last_name": "Doe",
      "email": "john.doe@company.com",
      "supervisor_id": 456
    },
    "employee_list": [
      {
        "employee_id": 789,
        "first_name": "Alice",
        "last_name": "Johnson"
      }
    ]
  }
}
```

**Response (400 Bad Request - Cannot Edit):**

```json
{
  "status": "error",
  "message": "Past approved leaves cannot be edited."
}
```

---

### 5. Update Leave Application

Update an existing leave application.

**Endpoint:** `PUT /api/leave/{id}`

**Content-Type:** `multipart/form-data`

**Path Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| id | integer | Leave application ID |

**Request Body:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| leave_type_id | integer | Yes | ID of the leave type |
| application_from_date | date | Yes | Start date (format: d/m/Y) |
| application_to_date | date | Yes | End date (format: d/m/Y) |
| number_of_day | numeric | Yes | Number of days |
| purpose | string | Yes | Reason for leave |
| reliever_id | integer | No | ID of the relieving employee |
| justification_file[] | file | No | Additional supporting documents |

**Response (200 Success):**

```json
{
  "status": "success",
  "message": "Leave application updated successfully",
  "data": {
    "application_id": 1,
    "leave_type": "Annual Leave",
    "dates": {
      "from": "01/02/2026",
      "to": "07/02/2026"
    },
    "number_of_days": 7
  }
}
```

---

### 6. Recall Leave Application

Recall a submitted leave application.

**Endpoint:** `POST /api/leave/{id}/recall`

**Path Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| id | integer | Leave application ID |

**Response (200 Success):**

```json
{
  "status": "success",
  "message": "Leave application recalled successfully.",
  "data": {
    "application_id": 1,
    "status": "recalled"
  }
}
```

**Response (400 Bad Request - Cannot Recall):**

```json
{
  "status": "error",
  "message": "Past approved leaves cannot be recalled."
}
```

---

### 7. Delete Justification Document

Delete a supporting document from a leave application.

**Endpoint:** `DELETE /api/leave/justification`

**Request Body:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | integer | Yes | Justification document ID |

**Response (200 Success):**

```json
{
  "status": "success",
  "message": "Document deleted successfully"
}
```

**Response (403 Forbidden):**

```json
{
  "status": "error",
  "message": "You are not authorized to delete this document"
}
```

---

### 8. Get Leave Form Data

Get leave types and employee information for the application form.

**Endpoint:** `GET /api/leave/create`

**Response (200 Success):**

```json
{
  "status": "success",
  "data": {
    "leave_type": [
      {
        "leave_type_id": 1,
        "leave_type_name": "Sick Leave"
      },
      {
        "leave_type_id": 2,
        "leave_type_name": "Annual Leave"
      },
      {
        "leave_type_id": 3,
        "leave_type_name": "Maternity Leave"
      }
    ],
    "employee_info": {
      "employee_id": 123,
      "first_name": "John",
      "last_name": "Doe",
      "email": "john.doe@company.com",
      "supervisor_id": 456,
      "department_id": 5,
      "designation_id": 10
    },
    "employee_list": [
      {
        "employee_id": 789,
        "first_name": "Alice",
        "last_name": "Johnson"
      },
      {
        "employee_id": 101,
        "first_name": "Bob",
        "last_name": "Williams"
      }
    ]
  }
}
```

---

### 9. Calculate Leave Days

Calculate the number of leave days between two dates.

**Endpoint:** `POST /api/leave/calculate-days`

**Request Body:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| leave_type_id | integer | Yes | ID of the leave type |
| application_from_date | date | Yes | Start date (format: d/m/Y) |
| application_to_date | date | Yes | End date (format: d/m/Y) |

**Request Example:**

```json
{
  "leave_type_id": 2,
  "application_from_date": "01/02/2026",
  "application_to_date": "05/02/2026"
}
```

**Response (200 Success):**

```json
{
  "status": "success",
  "data": 5
}
```

---

### 10. Get Employee Leave Balance

Check the available leave balance for a specific leave type.

**Endpoint:** `GET /api/leave/balance`

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| leave_type | string | Yes | Name of the leave type (e.g., "Annual Leave") |

**Response (200 Success):**

```json
{
  "status": "success",
  "data": {
    "leave_type": "Annual Leave",
    "balance": 15,
    "fiscal_year": "2026"
  }
}
```

---

### 11. Get Available Leave Types

Get leave types available for the authenticated employee.

**Endpoint:** `GET /api/leave/types`

**Response (200 Success):**

```json
{
  "status": "success",
  "message": "Employee leave types retrieved successfully",
  "data": [
    {
      "leave_type_id": 1,
      "leave_type_name": "Sick Leave",
      "status": 1,
      "annual_entitlement": 10,
      "max_carryover_days": 5
    },
    {
      "leave_type_id": 2,
      "leave_type_name": "Annual Leave",
      "status": 1,
      "annual_entitlement": 21,
      "max_carryover_days": 10
    }
  ]
}
```

---

### 12. Get My Supervisor Information

Get the supervisor details for the authenticated employee.

**Endpoint:** `GET /api/leave/supervisor`

**Response (200 Success):**

```json
{
  "status": "success",
  "data": {
    "supervisor_id": 456,
    "first_name": "Jane",
    "last_name": "Smith",
    "email": "jane.smith@company.com"
  }
}
```

**Response (404 Not Found - No Supervisor):**

```json
{
  "status": "error",
  "message": "No supervisor assigned to this employee."
}
```

---

### 13. Get Personal Leave Report

Get personal leave report for the authenticated employee.

**Endpoint:** `GET /api/leave/personal-report`

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| status | integer | No | Filter by status (0=Pending, 1=Approved, 2=Rejected) |

**Response (200 Success):**

```json
{
  "status": "success",
  "count": 5,
  "financial_year": {
    "start_date": "2026-01-01",
    "end_date": "2026-12-31"
  },
  "data": [
    {
      "leave_application_id": 1,
      "employee_id": 123,
      "leave_type_id": 2,
      "application_from_date": "2026-02-01",
      "application_to_date": "2026-02-05",
      "number_of_day": 5,
      "purpose": "Annual vacation",
      "status": 0,
      "final_status": 0,
      "employee": {
        "employee_id": 123,
        "first_name": "John",
        "last_name": "Doe"
      },
      "leaveType": {
        "leave_type_id": 2,
        "leave_type_name": "Annual Leave"
      }
    }
  ]
}
```

---

### 14. Apply for Leave (Alternative)

Alternative endpoint to submit a leave application.

**Endpoint:** `POST /api/leave/apply`

**Content-Type:** `multipart/form-data`

**Request Body:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| leave_type | string | Yes | Name of the leave type |
| from_date | date | Yes | Start date (Y-m-d) |
| to_date | date | Yes | End date (Y-m-d) |
| purpose | string | No | Reason for leave |
| evidence | file | No | Supporting document (jpg, jpeg, png, pdf, max 2048KB) |

**Response (200 Success):**

```json
{
  "status": "success",
  "message": "Leave application submitted successfully",
  "data": {
    "application_id": 15,
    "leave_type": "Annual Leave",
    "current_balance": 21,
    "requested_days": 5,
    "projected_balance": 16,
    "fiscal_year": "2026",
    "dates": {
      "from": "2026-02-01",
      "to": "2026-02-05"
    },
    "supervisor": {
      "id": 456,
      "name": "Jane Smith",
      "email": "jane.smith@company.com"
    },
    "balance_calculation_note": "Current balance is based on leave type and fiscal year. Projected balance assumes leave approval."
  }
}
```

---

# Supervisor Endpoints

These endpoints are for supervisors to manage leaves of employees under their supervision.

### 15. Get Pending Leaves (Supervisor)

Get pending leave applications from supervised employees.

**Endpoint:** `GET /api/leave/supervisor/pending`

**Response (200 Success):**

```json
{
  "status": "success",
  "count": 3,
  "fiscal_year": {
    "start_date": "2026-01-01",
    "end_date": "2026-12-31"
  },
  "data": [
    {
      "leave_application_id": 10,
      "employee_id": 789,
      "leave_type_id": 1,
      "application_from_date": "2026-02-10",
      "application_to_date": "2026-02-12",
      "number_of_day": 3,
      "purpose": "Medical appointment",
      "status": 0,
      "final_status": 0,
      "employee": {
        "employee_id": 789,
        "first_name": "Alice",
        "last_name": "Johnson"
      },
      "leaveType": {
        "leave_type_id": 1,
        "leave_type_name": "Sick Leave"
      }
    }
  ]
}
```

**Response (401 Unauthorized):**

```json
{
  "status": "error",
  "message": "Supervisor code not found. Are you sure you're a supervisor?"
}
```

**Response (200 Success - No Subordinates):**

```json
{
  "status": "success",
  "message": "No employees under your supervision",
  "data": []
}
```

---

### 16. Get Approved Leaves Report (Supervisor)

Get approved leave applications from supervised employees.

**Endpoint:** `GET /api/leave/supervisor/approved`

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| leave_type_id | integer | No | Filter by leave type ID |
| employee_id | integer | No | Filter by employee ID |

**Response (200 Success):**

```json
{
  "status": "success",
  "count": 5,
  "financial_year": {
    "start_date": "2026-01-01",
    "end_date": "2026-12-31"
  },
  "data": [
    {
      "leave_application_id": 5,
      "employee_id": 789,
      "leave_type_id": 2,
      "application_from_date": "2026-01-15",
      "application_to_date": "2026-01-19",
      "number_of_day": 5,
      "purpose": "Vacation",
      "status": 1,
      "final_status": 1,
      "approve_by": 456,
      "approve_date": "2026-01-10",
      "employee": {
        "employee_id": 789,
        "first_name": "Alice",
        "last_name": "Johnson"
      },
      "leaveType": {
        "leave_type_id": 2,
        "leave_type_name": "Annual Leave"
      }
    }
  ]
}
```

---

### 17. Get Rejected Leaves Report (Supervisor)

Get rejected leave applications from supervised employees.

**Endpoint:** `GET /api/leave/supervisor/rejected`

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| leave_type_id | integer | No | Filter by leave type ID |
| employee_id | integer | No | Filter by employee ID |

**Response (200 Success):**

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
      "leave_application_id": 3,
      "employee_id": 101,
      "leave_type_id": 2,
      "application_from_date": "2026-03-01",
      "application_to_date": "2026-03-10",
      "number_of_day": 10,
      "purpose": "Extended vacation",
      "status": 2,
      "final_status": 2,
      "reject_by": 456,
      "reject_date": "2026-02-25",
      "remarks": "Insufficient staffing during this period",
      "employee": {
        "employee_id": 101,
        "first_name": "Bob",
        "last_name": "Williams"
      },
      "leaveType": {
        "leave_type_id": 2,
        "leave_type_name": "Annual Leave"
      }
    }
  ]
}
```

---

### 18. Approve Leave Application (Supervisor)

Approve a pending leave application.

**Endpoint:** `POST /api/leave/supervisor/approve`

**Request Body:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| leave_application_id | integer | Yes | ID of the leave application |
| remarks | string | No | Approval remarks (max 500 chars) |

**Request Example:**

```json
{
  "leave_application_id": 10,
  "remarks": "Approved. Have a good rest!"
}
```

**Response (200 Success):**

```json
{
  "status": "success",
  "message": "Leave application approved successfully",
  "data": {
    "leave_application_id": 10,
    "employee_id": 789,
    "status": 1,
    "final_status": 1,
    "approve_by": 456,
    "approve_date": "2026-01-31",
    "remarks": "Approved. Have a good rest!"
  }
}
```

**Response (403 Forbidden):**

```json
{
  "status": "error",
  "message": "You are not authorized to approve this leave application"
}
```

---

### 19. Reject Leave Application (Supervisor)

Reject a pending leave application.

**Endpoint:** `POST /api/leave/supervisor/reject`

**Request Body:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| leave_application_id | integer | Yes | ID of the leave application |
| remarks | string | Yes | Rejection reason (max 500 chars) |

**Request Example:**

```json
{
  "leave_application_id": 10,
  "remarks": "Cannot approve due to project deadline commitments"
}
```

**Response (200 Success):**

```json
{
  "status": "success",
  "message": "Leave application rejected successfully",
  "data": {
    "leave_application_id": 10,
    "employee_id": 789,
    "status": 2,
    "final_status": 2,
    "reject_by": 456,
    "reject_date": "2026-01-31",
    "remarks": "Cannot approve due to project deadline commitments"
  }
}
```

**Response (403 Forbidden):**

```json
{
  "status": "error",
  "message": "You are not authorized to reject this leave application"
}
```

---

### 20. Get Today's Leave Applications (Supervisor)

Get leave applications submitted today by supervised employees.

**Endpoint:** `GET /api/leave/supervisor/today`

**Response (200 Success):**

```json
{
  "status": "success",
  "today": "2026-01-31",
  "count": 2,
  "data": [
    {
      "leave_application_id": 15,
      "employee_id": 789,
      "leave_type_id": 2,
      "application_from_date": "2026-02-01",
      "application_to_date": "2026-02-05",
      "number_of_day": 5,
      "purpose": "Family vacation",
      "status": 0,
      "employee": {
        "employee_id": 789,
        "first_name": "Alice",
        "last_name": "Johnson"
      },
      "leaveType": {
        "leave_type_id": 2,
        "leave_type_name": "Annual Leave"
      }
    }
  ]
}
```

---

### 21. Get Employees on Leave Today (Supervisor)

Get list of supervised employees who are on approved leave today.

**Endpoint:** `GET /api/leave/supervisor/on-leave-today`

**Response (200 Success):**

```json
{
  "status": "success",
  "today": "2026-01-31",
  "count": 3,
  "data": [
    {
      "leave_application_id": 5,
      "employee_id": 789,
      "leave_type_id": 2,
      "application_from_date": "2026-01-28",
      "application_to_date": "2026-02-02",
      "number_of_day": 6,
      "purpose": "Vacation",
      "status": 1,
      "final_status": 1,
      "employee": {
        "employee_id": 789,
        "first_name": "Alice",
        "last_name": "Johnson"
      },
      "leaveType": {
        "leave_type_id": 2,
        "leave_type_name": "Annual Leave"
      }
    }
  ]
}
```

---

### 22. Check Supervisor Status

Check if the authenticated user is a supervisor.

**Endpoint:** `GET /api/leave/is-supervisor`

**Response (200 Success):**

```json
{
  "status": "success",
  "is_supervisor": true,
  "message": "User is a supervisor"
}
```

**Response (200 Success - Not Supervisor):**

```json
{
  "status": "success",
  "is_supervisor": false,
  "message": "User is not a supervisor"
}
```

---

## Data Models

### Leave Application

| Field                 | Type    | Description                                        |
| --------------------- | ------- | -------------------------------------------------- |
| leave_application_id  | integer | Unique identifier                                  |
| employee_id           | integer | Employee ID                                        |
| leave_type_id         | integer | Leave type ID                                      |
| application_from_date | date    | Leave start date                                   |
| application_to_date   | date    | Leave end date                                     |
| application_date      | date    | Date of application                                |
| number_of_day         | numeric | Number of days                                     |
| purpose               | string  | Reason for leave                                   |
| status                | integer | Current status (0=Pending, 1=Approved, 2=Rejected) |
| final_status          | integer | Final approval status                              |
| approve_by            | integer | Approver's employee ID                             |
| approve_date          | date    | Approval date                                      |
| reject_by             | integer | Rejector's employee ID                             |
| reject_date           | date    | Rejection date                                     |
| remarks               | string  | Approval/rejection remarks                         |
| evidence              | string  | Path to uploaded evidence file                     |
| financial_year_id     | integer | Financial year ID                                  |
| ceo_approval_type     | integer | CEO approval status                                |
| hr_approval           | integer | HR approval status                                 |
| reliever_id           | integer | Reliever's employee ID                             |

### Leave Status Values

| Value | Status   |
| ----- | -------- |
| 0     | Pending  |
| 1     | Approved |
| 2     | Rejected |
| 3     | Recall   |

### Leave Type

| Field              | Type    | Description                           |
| ------------------ | ------- | ------------------------------------- |
| leave_type_id      | integer | Unique identifier                     |
| leave_type_name    | string  | Name of leave type                    |
| status             | integer | Active status (1=Active, 0=Inactive)  |
| annual_entitlement | integer | Days entitled per year                |
| max_carryover_days | integer | Maximum days that can be carried over |

### Leave Justification

| Field                | Type     | Description                     |
| -------------------- | -------- | ------------------------------- |
| id                   | integer  | Unique identifier               |
| leave_application_id | integer  | Associated leave application ID |
| file_name            | string   | Name of uploaded file           |
| employee_id          | integer  | Employee who uploaded           |
| created_at           | datetime | Upload timestamp                |

---

## Error Codes

| HTTP Code | Description                                               |
| --------- | --------------------------------------------------------- |
| 200       | Success                                                   |
| 201       | Created successfully                                      |
| 400       | Bad Request (validation errors, overlapping leaves, etc.) |
| 401       | Unauthorized (not authenticated or not a supervisor)      |
| 403       | Forbidden (not authorized for this action)                |
| 404       | Not Found (employee, leave type, or supervisor not found) |
| 422       | Validation Error                                          |
| 500       | Internal Server Error                                     |

---

## Notes

1. **Date Formats**: The API accepts dates in both `Y-m-d` and `d/m/Y` formats for most endpoints
2. **File Uploads**: Maximum file size is 2048KB (2MB). Allowed types: jpg, jpeg, png, pdf
3. **Leave Balance**: Calculated based on leave type and fiscal year
4. **Notifications**: Email notifications are sent to employees, supervisors, and HR approvers automatically
5. **Overlapping Leaves**: Applications are rejected if they overlap with existing non-rejected leaves
6. **Edit Restrictions**: Past approved leaves cannot be edited
7. **Recall Restrictions**: Past approved leaves cannot be recalled
