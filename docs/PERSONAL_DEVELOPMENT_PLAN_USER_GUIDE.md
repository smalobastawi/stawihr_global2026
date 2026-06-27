# Personal Development Plans (PDP) — User Guide

This guide explains how to use the **Personal Development Plans** module in StawiHR. The module helps staff set SMART development goals, record progress at intervals defined by HR policy, and gives supervisors and HR managers visibility through reports.

---

## 1. Overview

Personal Development Plans (PDPs) are annual (or period-based) plans where employees document:

- What they want to develop (goals)
- How they will achieve it (actions and success criteria)
- Progress over time (quarterly, bi-annually, or annually)

The module is separate from **Performance Appraisals** and **Performance Improvement Plans (PIP)**. PDPs focus on voluntary career and competency growth, not corrective performance action.

---

## 2. Who Uses What

| Role | Main activities |
|------|-----------------|
| **Employee** | Create own plan, add goals, record progress, acknowledge plan |
| **Supervisor** | Review team plans, approve plans, comment on progress entries |
| **HR Administrator** | Configure policy, view all plans, run filtered reports |
| **Admin / SuperAdmin** | Full access including settings and reports |

---

## 3. HR Policy Settings

**Menu:** Performance Management → **PDP Policy Settings**

HR can configure:

| Setting | Description |
|---------|-------------|
| **Default Review Frequency** | How often staff should record progress: Quarterly, Bi-Annually, or Annually |
| **Allow Employee Self-Service** | Whether staff can create their own plans from Self Service |
| **Require Supervisor Approval** | Whether supervisor sign-off is expected on plans |
| **Require HR Review** | Whether HR review is expected on plans |
| **Policy Notes** | Guidance text shown to staff and managers |

These settings apply as defaults when new plans are created.

---

## 4. For Employees (Self Service)

### 4.1 Access your plans

1. Log in to StawiHR.
2. Open **Self Service** in the left menu.
3. Click **My Development Plans**.

You will see all plans linked to your employee record, including year, review frequency, number of goals, overall progress, and status.

### 4.2 Create a new plan

1. From **My Development Plans**, click **Create Plan** (if enabled by HR).
2. Complete the form:
   - **Plan Title** — e.g. “2026 Leadership Development Plan”
   - **Plan Year** — the calendar or performance year
   - **Start / End Date** — plan period
   - **Review Frequency** — how often you will update progress
   - **Development Focus** — main themes (optional)
   - **Career Aspirations** — longer-term direction (optional)
3. Click **Create Plan**.

Each employee may have **one plan per year**.

### 4.3 Add development goals

1. Open your plan and click **Manage Goals**.
2. Enter:
   - **Goal Title** — short name
   - **SMART Objective** — Specific, Measurable, Achievable, Relevant, Time-bound description
   - **Competency Area** — e.g. Leadership, Technical Skills (optional)
   - **Priority** — Low, Medium, or High
3. Click **Add Goal**.

Edit goals anytime while the plan is in **Draft** or **Active** status.

### 4.4 Activate and acknowledge your plan

1. Open your plan.
2. Click **Activate Plan** when you are ready to start tracking (moves status from Draft to Active).
3. Click **Acknowledge Plan** to confirm you accept the plan contents.

If HR policy requires supervisor approval, your supervisor will approve from the admin view.

### 4.5 Record progress

Progress should be recorded at the frequency set on your plan (quarterly, bi-annually, or annually).

1. Open your plan and click **Record Progress** (or **Progress Updates**).
2. Click **Record Progress**.
3. Select:
   - **Development Goal**
   - **Review Period** — e.g. Q1 2026, H1 2026, or 2026
   - **Progress (%)** — 0–100
4. Complete:
   - **Achievement Summary** — what you accomplished in this period
   - **Challenges** — obstacles faced (optional)
   - **Support Needed** — help required from manager or HR (optional)
   - **Next Steps** — actions for the next period (optional)
5. Click **Submit Progress**.

Your supervisor can add comments and mark the entry as reviewed.

---

## 5. For Supervisors

### 5.1 View team plans

**Menu:** Performance Management → **Personal Development Plans**

You will see plans where you are the assigned supervisor, plus plans for employees who report to you (depending on your permissions).

Use filters at the top of the list:

- **Year**
- **Department**
- **Status**

### 5.2 Approve and review

From a plan detail page you can:

- **Supervisor Approve** — confirm the plan is agreed
- **Review progress entries** — add supervisor comments on each progress update

Encourage staff to record progress on schedule and discuss support needed during 1:1 meetings.

---

## 6. For HR Administrators

### 6.1 Manage all plans

**Menu:** Performance Management → **Personal Development Plans**

HR can create plans on behalf of employees, edit plans in Draft/Active status, and mark plans as completed.

### 6.2 Run reports

**Menu:** Performance Management → **Reports**

| Report | Purpose | Filters |
|--------|---------|---------|
| **PDP Dashboard** | High-level counts: total, active, completed plans and progress entries | Year, Department |
| **PDP By Department** | Plan counts and average progress by department | Year, Quarter |
| **PDP By Employee** | Individual plan detail or department listing | Year, Department, Employee, Quarter |
| **PDP Progress Summary** | All progress submissions with summaries | Year, Quarter, Half-year, Department |

Use these reports for workforce development reviews, talent discussions, and compliance with internal HR policy.

---

## 7. Plan Status Reference

| Status | Meaning |
|--------|---------|
| **Draft** | Plan created but not yet active |
| **Active** | Plan in use; goals and progress can be updated |
| **Completed** | Plan period finished |
| **Cancelled** | Plan withdrawn |

---

## 8. Goal Status Reference

| Status | Typical use |
|--------|-------------|
| **Not Started** | Goal defined but work not begun |
| **In Progress** | Work underway |
| **On Track** | Good progress (often 70%+ completion) |
| **At Risk** | Behind schedule or blocked |
| **Completed** | Goal achieved |
| **Deferred** | Postponed to a future period |

Progress percentage is updated automatically when you submit a progress entry.

---

## 9. Best Practice Tips

1. **Keep goals SMART** — vague goals are hard to measure and review.
2. **Align with role and career path** — link goals to current job needs and future aspirations.
3. **Update on schedule** — record progress each quarter (or per HR policy), not only at year-end.
4. **Be honest about challenges** — this helps supervisors provide meaningful support.
5. **Review as a conversation** — use progress updates as input for regular development discussions, not just form-filling.

---

## 10. Permissions and Access

Access to PDP menus and actions is controlled by **Role Permissions** (Administration → Role Permissions). After the module is installed, run:

```bash
php artisan permission:create-permission-routes
```

Then assign the relevant `pdp.*` and `ess.pdp.*` permissions to appropriate roles (Employee, Supervisor, HR Administrator).

Typical permission groups:

- **Employees:** `ess.pdp.myPlans`, `ess.pdp.create`, `ess.pdp.store`, `ess.pdp.show`, plus goal/progress routes as needed
- **Supervisors:** `pdp.plan.index`, `pdp.plan.show`, `pdp.progress.review`, report view permissions
- **HR:** All `pdp.*` permissions including settings and reports

---

## 11. Installation (Administrators)

If deploying this module for the first time:

```bash
php artisan migrate
php artisan db:seed --class=PdpSeeder
php artisan permission:create-permission-routes
```

Assign permissions to roles via **Administration → Role Permissions**.

---

## 12. Support

For technical issues or access problems, contact your system administrator or HR team.

For policy questions (review frequency, approval requirements), refer to your organisation’s HR policy notes configured under **PDP Policy Settings**.
