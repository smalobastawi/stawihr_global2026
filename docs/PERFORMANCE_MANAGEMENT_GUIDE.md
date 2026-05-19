# Performance Management Module — User Guide

## Table of Contents
1. [Overview](#overview)
2. [Module Structure](#module-structure)
3. [Setting Up Performance Metrics](#setting-up-performance-metrics)
   - [Rating Guidelines (Rating Scales)](#rating-guidelines-rating-scales)
   - [Focus Areas (Categories)](#focus-areas-categories)
   - [Goals (KPIs)](#goals-kpis)
   - [Behavioral Expectations](#behavioral-expectations)
4. [Performance Appraisal Workflow](#performance-appraisal-workflow)
   - [Creating an Appraisal](#creating-an-appraisal)
   - [Self Evaluation](#self-evaluation)
   - [Supervisor Evaluation](#supervisor-evaluation)
   - [HOD Review](#hod-review)
   - [Finalizing and Sign-Off](#finalizing-and-sign-off)
5. [Performance Improvement Plan (PIP) Management](#performance-improvement-plan-pip-management)
   - [Creating a PIP](#creating-a-pip)
   - [PIP Goals](#pip-goals)
   - [Support & Resources](#support--resources)
   - [Review Schedule](#review-schedule)
   - [PIP Workflow & Outcomes](#pip-workflow--outcomes)
6. [Reports](#reports)
7. [Roles & Responsibilities Summary](#roles--responsibilities-summary)
8. [Tips & Best Practices](#tips--best-practices)

---

## Overview

The **Performance Management Module** provides a structured way to evaluate employee performance over a defined review period. It supports:

- **Setup** of rating scales, focus areas, goals (KPIs), and behavioral expectations.
- **Appraisal workflow** with self-evaluation, supervisor review, and HOD review.
- **Development and learning plans** for employee growth.
- **Performance Improvement Plans (PIP)** for employees who need structured support to meet expectations.
- **Reports** for departments, individual employees, and summary analytics.

---

## Module Structure

The module is organized into two main areas accessible from the sidebar:

| Menu Section | Sub-section | Purpose |
|-------------|-------------|---------|
| **Performance Management** | Rating Guidelines | Define the scoring scale used in appraisals |
| | Focus Areas | Create performance categories with weights |
| | Performance Goals | Define KPIs under each focus area |
| | Behavioral Items | Define behavioral expectations and their weights |
| | Performance Appraisal | Manage appraisal records and workflow |
| | Performance Reports | View/download department, employee, and summary reports |
| **PIP Management** | PIP Plans | Create and manage improvement plans |
| | PIP Goals | Define specific improvement goals |
| | PIP Support | Allocate support resources |
| | PIP Schedule | Schedule and conduct review meetings |
| | PIP Reports | Dashboard and analytics |

---

## Setting Up Performance Metrics

Before appraisals can be created, the system must be configured with the metrics that will be used for evaluation. This is typically done by **HR** or an administrator.

### Rating Guidelines (Rating Scales)

Rating scales define the point system and labels used to score performance.

**Default Rating Scale:**

| Points | Rating Label | Score Range | Description |
|--------|-------------|-------------|-------------|
| 5 | Outstanding | 90% - 100% | Exceptional performance exceeding all expectations |
| 4 | Exceeds Expectations | 80% - 89% | Performance exceeds expectations in most areas |
| 3 | Meets Expectations | 60% - 79% | Performance meets the required standards |
| 2 | Needs Improvement | 40% - 59% | Performance below expected standards |
| 1 | Unsatisfactory | 0% - 39% | Performance significantly below standards |

**How to manage:**
1. Navigate to **Performance Management > Rating Guidelines**.
2. Click **Add Rating Scale** to create a new scale.
3. Enter: Points, Rating Label, Description, Definition, Score Range, and Status.
4. Use the edit/delete actions to maintain existing scales.

> **Tip:** Keep rating scales active (`is_active = 1`) so they are available during appraisals.

### Focus Areas (Categories)

Focus Areas are the high-level performance categories that group related goals. Each focus area carries a **weight** that contributes to the final score.

**Example Focus Areas:**

| Focus Area | Weight | Description |
|-----------|--------|-------------|
| Financial Accuracy | 40% | Error-free entries, reconciliations, and reporting accuracy |
| Reporting | 25% | Timely submission and quality of reports |
| Compliance | 20% | Tax compliance and policy adherence |
| Discipline | 15% | Attendance and conduct |

**How to manage:**
1. Navigate to **Performance Management > Focus Areas**.
2. Click **Add Focus Area**.
3. Enter: Focus Area Name, Weight (%), Description, and optionally link to a Department or Designation.
4. Click the **Goals** icon (list button) on a focus area row to manage its KPIs.

> **Important:** The sum of all focus area weights for an employee should ideally total **100%** for accurate final scoring.

### Goals (KPIs)

Goals are the specific, measurable objectives under each Focus Area. Each goal has an **itemized weighting** that contributes to its parent focus area.

**Goal fields:**
- **Strategic Objective** — What the goal aims to achieve
- **Performance Metric** — How performance is measured
- **Performance Target** — The specific target to be met
- **Key Initiatives** — Actions to achieve the target
- **Itemized Weighting** — The weight of this goal within its focus area

**Example Goals under "Financial Accuracy":**

| Strategic Objective | Performance Metric | Target | Weight |
|---------------------|-------------------|--------|--------|
| Ensure financial data accuracy | Error-free entries | 100% accuracy in daily bookkeeping | 20.00 |
| Maintain account reconciliations | Reconciliation accuracy | Complete monthly reconciliations by 5th | 20.00 |

**How to manage:**
1. From the Focus Areas list, click the **Goals** icon for the desired focus area.
2. Click **Add Goal**.
3. Fill in all fields and set the itemized weight.
4. Save. The goal will now appear in appraisals linked to this focus area.

### Behavioral Expectations

Behavioral items evaluate conduct, attendance, teamwork, and other soft skills.

**Example Behavioral Items:**

| Item | Weight |
|------|--------|
| Attendance | 7.50 |
| Conduct / Professional Behavior | 7.50 |

**How to manage:**
1. Navigate to **Performance Management > Behavioral Items**.
2. Click **Add Behavioral Item**.
3. Enter: Item Name, Weight, Description, and Sort Order.
4. Save.

---

## Performance Appraisal Workflow

The appraisal lifecycle moves through several stages:

```
Draft → Self Review → Supervisor Review → HOD Review → Finalized → Closed
```

### Creating an Appraisal

1. Navigate to **Performance Management > Performance Appraisal**.
2. Click **Add Appraisal**.
3. Fill in the form:
   - **Employee** — The person being evaluated (required)
   - **Supervisor** — The direct supervisor who will review
   - **Review Period** — e.g., "Jan - June 2026" (required)
   - **Review Start Date / End Date** — The date range of the review period
4. The system will **auto-populate** the goals and behavioral items based on the employee's department and designation.
5. Click **Save**. The appraisal is created in **Draft** status.

> **Note:** Once saved, the appraisal moves to **Self Review** status and the employee can begin their self-evaluation.

### Self Evaluation

**Who:** Employee  
**When:** Status is `draft` or `self_review`

1. From the Performance Appraisal list, click the **Self Review** icon (user button) next to the appraisal.
2. The employee sees:
   - **Section A: Performance Measure** — All focus areas and their goals with itemized weights
   - **Behavioral Expectations** — All behavioral items
3. For each goal and behavioral item, the employee enters:
   - **Self Weighting** — A score from 0 up to the itemized weight (required)
   - **Comments** — Justification for the self-assigned score
4. Click **Save Self Review**.

> After saving, the appraisal status automatically moves to **Supervisor Review**.

### Supervisor Evaluation

**Who:** Supervisor  
**When:** Status is `self_review` or `supervisor_review`

1. From the Performance Appraisal list, click the **Supervisor Review** icon (user-secret button).
2. The supervisor sees:
   - The employee's **Self Weight** for each item
   - Fields to enter the **Review Weighting** (required)
   - Fields for **Comments**
3. The supervisor enters their scores and overall comments.
4. Click **Save Supervisor Review**.

> After saving, the appraisal status moves to **HOD Review**.

### HOD Review

**Who:** Head of Department (HOD)  
**When:** Status is `supervisor_review` or `hod_review`

1. From the list, click the **HOD Review** icon (user-md button).
2. The HOD sees a summary of self and review scores.
3. The HOD can manage:
   - **Section B: Development Plan** — Competency gaps, SMART objectives, ratings
   - **Section C: Learning Plan** — Training courses, due dates, progress status
4. Enter overall **HOD Comments**.
5. Click **Save HOD Review**.

### Finalizing and Sign-Off

**Who:** HOD or authorized user  
**When:** Status is `hod_review`

1. Once the HOD review is complete, click the **Finalize** icon (check-circle button) on the appraisal row.
2. The system calculates the **Total Review Score** based on weighted focus area scores plus behavioral scores.
3. After finalization, the appraisal status becomes **Finalized**.

**Sign-offs:**

| Role | Action | When |
|------|--------|------|
| Employee | Click **Sign** | After finalized |
| Supervisor | Click **Sign** | After finalized |
| HOD | Click **Sign** | After finalized |

4. Open the appraisal **View** (eye icon) to see sign-off buttons for each party.
5. Once all parties have signed, the appraisal can be considered **Closed**.

---

## Performance Improvement Plan (PIP) Management

A **Performance Improvement Plan (PIP)** is created when an employee's appraisal results indicate a need for structured improvement. PIPs can be created manually or auto-generated from a low-scoring appraisal.

### Creating a PIP

1. Navigate to **PIP Management > PIP Plans**.
2. Click **Create PIP**.
3. Fill in the form:
   - **Employee** — Select the employee (auto-fills designation, department, and supervisor)
   - **Department** — Confirm or adjust
   - **Linked Appraisal** — Optionally link to the appraisal that triggered the PIP
   - **Supervisor / HR Manager** — Assign responsible parties
   - **Start Date / End Date** — Define the PIP period (required)
   - **Trigger Type** — Automatic, Manual (Supervisor), or Manual (HR)
   - **Trigger Score** — The appraisal score that triggered the PIP (if applicable)
   - **Purpose / Reason** — Describe why the PIP is being initiated (required)
4. If linked to an appraisal with low scores, the system will **auto-detect concerns** and list them for selection.
5. Add **Initial Improvement Goals** (objective, action required, target KPI, deadline).
6. Click **Save**.

> The PIP is created in **Draft** status.

### PIP Goals

1. Open a PIP and click the **Goals** button, or navigate to **PIP Management > PIP Goals**.
2. Add goals by entering:
   - **Objective** — What needs to improve
   - **Action Required** — Steps the employee must take
   - **Target KPI** — Measurable success criteria
   - **Deadline** — Due date
3. Track goal status: `pending`, `completed`, or `overdue`.

### Support & Resources

1. Open a PIP and click the **Support** button.
2. Add support resources:
   - **Type** — Training, Mentorship, Tools, Counseling, Other
   - **Description** — Details of the support
   - **Provider** — HR, Supervisor, External, Peer
   - **Scheduled Date** — When the support will be provided
3. Track resource status: `scheduled`, `completed`, or `cancelled`.

### Review Schedule

1. Open a PIP and click the **Reviews** button.
2. Review meetings are scheduled automatically or can be managed here.
3. For each scheduled review:
   - **Conduct** — Record findings and comments when the review is held
   - **Reschedule** — Move the date if needed
4. Track review status: `pending`, `completed`, `missed`, or `rescheduled`.

### PIP Workflow & Outcomes

| Status | Meaning |
|--------|---------|
| Draft | PIP created but not yet active |
| Active | PIP is in progress |
| In Review | Under evaluation at the end of the period |
| Completed | PIP cycle finished |
| Extended | PIP period extended |
| Cancelled | PIP cancelled |

**Workflow steps:**

1. **Activate** — Click **Activate PIP** to move from Draft to Active.
2. **Acknowledge** — The employee clicks **Acknowledge** to confirm they have received the PIP.
3. **Supervisor Sign** — The supervisor clicks **Sign** to confirm ownership.
4. **HR Validate** — HR clicks **Validate** to confirm the plan is appropriate.
5. **Finalize Outcome** — At the end of the period, click **Finalize Outcome** and select:
   - **Successful Completion** — Employee met expectations
   - **Partial Improvement** — Some progress made
   - **Failure to Improve** — Employee did not meet expectations
6. **Lock PIP** — Once the outcome is recorded, click **Lock PIP** to prevent further edits.

---

## Reports

### Performance Reports

1. Navigate to **Performance Management > Performance Reports**.
2. Available reports:
   - **Department Report** — Average scores and appraisal counts per department
   - **Employee Report** — Detailed score breakdown for a selected employee and period
   - **Summary Report** — Organization-wide summary by review period

### PIP Reports

1. Navigate to **PIP Management > PIP Reports**.
2. Available reports:
   - **Dashboard** — Overview of all PIPs
   - **By Department** — PIP distribution across departments
   - **By Outcome** — Analysis of PIP results (success, partial, failure)

---

## Roles & Responsibilities Summary

| Role | Setup Metrics | Create Appraisal | Self Eval | Supervisor Eval | HOD Review | Manage PIP | Sign Off |
|------|--------------|------------------|-----------|-----------------|------------|------------|----------|
| **HR** | Yes | Yes | — | — | — | Yes (validate, lock) | — |
| **Supervisor** | — | — | — | Yes | — | Yes (create, sign, goals, reviews) | Yes |
| **HOD** | — | — | — | — | Yes | — | Yes |
| **Employee** | — | — | Yes | — | — | — (acknowledge PIP) | Yes |

---

## Tips & Best Practices

1. **Configure metrics before appraisals** — Ensure rating scales, focus areas, goals, and behavioral items are set up before the review cycle begins.
2. **Balance weights** — Focus area weights should sum to 100% for fair final scoring.
3. **Be specific with goals** — Use clear performance targets and key initiatives so employees know exactly what is expected.
4. **Encourage detailed comments** — Both self-evaluations and supervisor reviews should include justifications for scores.
5. **Use PIPs constructively** — PIPs are tools for improvement, not punishment. Provide adequate support resources and regular reviews.
6. **Monitor deadlines** — Keep track of PIP goal deadlines and review schedules to ensure timely follow-up.
7. **Review reports regularly** — Use department and summary reports to identify trends and address systemic performance issues.

---

*Document Version: 1.0*  
*Last Updated: 2026-04-30*
