# Multi-Currency Payroll — Feature Guide

**Product:** StawiHR Payroll  
**Document type:** Client feature documentation  
**Version:** 1.0  
**Last updated:** June 2026

---

## 1. Purpose

This document explains how StawiHR handles **multi-currency payroll**. It is intended for HR managers, payroll administrators, finance teams, and client stakeholders who need to understand how salaries, statutory deductions, and bank payments work when employees are paid in a different currency from the company’s statutory payroll currency.

### The problem this feature solves

Many international and regional employers face a common situation:

- The company is registered and domiciled in one country (for example, **Rwanda**).
- **Statutory payroll** — PAYE, pension, social security, and government returns — must be calculated and reported in the **local currency** (for example, **RWF**).
- Some employees are **contracted or paid in a foreign currency** (for example, **USD**) because the company holds funds in that currency or because of expatriate arrangements.
- Other employees in the same company are paid entirely in the local currency.

StawiHR separates these two concerns:

| Concept | Description |
|--------|-------------|
| **Payroll base currency (statutory currency)** | The currency used for all statutory calculations and government reporting |
| **Payment currency (disbursement currency)** | The currency actually paid to the employee’s bank account |

---

## 2. Core principles

The system follows four non-negotiable rules:

1. **All statutory calculations are done in the payroll base currency.**  
   PAYE, pension, social security, and other statutory deductions never run in a foreign currency.

2. **Employee payment may be in a different currency.**  
   After statutory calculations are complete, net pay is converted to the employee’s payment currency for bank disbursement.

3. **Exchange rates are controlled and auditable.**  
   Exchange rates are used in payroll as soon as they are saved. Once payroll is processed, the rate applied is **saved on the payslip** and the rate record is **locked** so historical payslips are never recalculated with new rates.

4. **Single-currency employees are unaffected.**  
   If an employee is paid in the same currency as the company base currency, the system behaves exactly as before — no extra steps or complexity.

---

## 3. Key terms

| Term | Meaning |
|------|---------|
| **Payroll country** | The country whose statutory tax and deduction rules apply (e.g. Rwanda, Kenya, Uganda). |
| **Payroll base currency** | The statutory currency for that country (e.g. RWF for Rwanda, KES for Kenya). All gross pay, taxable income, PAYE, and deductions are calculated in this currency. |
| **Salary currency** | The currency in which the employee’s contract basic salary and fixed earnings are expressed. |
| **Payment currency** | The currency sent to the employee’s bank account. |
| **Bank payment currency** | Optional override for the currency expected by the employee’s bank file (defaults to payment currency). |
| **Exchange rate** | The conversion factor between two currencies on a given effective date. |
| **Active rate** | An exchange rate that is saved and immediately available for payroll. |
| **Locked rate** | An exchange rate that was used in a processed payroll and can no longer be edited. |

---

## 4. Company payroll currency settings

Company settings are configured under **Companies → Edit Company** (payroll section).

### 4.1 Fields

| Setting | Description |
|---------|-------------|
| **Payroll country** | Determines which country’s statutory rules apply (PAYE bands, pension, social security, etc.). |
| **Payroll base currency (statutory)** | The currency for all statutory calculations and local government reports. For Rwanda, this should be **RWF**. |
| **Explicit payroll base currency override** | Optional. If blank, the system uses the base currency above or derives it from payroll country. |
| **Default payment currency** | Default currency for employee bank payments when no employee-level override is set. |
| **Exchange rate source** | How rates are maintained: **Manual entry** (currently supported) or **External API** (for future integration). |
| **Exchange rate effective date policy** | Which date is used to pick the exchange rate during payroll (see Section 6). |
| **Allow employee payment currency** | When enabled, individual employees may be paid in a currency different from the base currency. When disabled, all employees are paid in the base currency. |

### 4.2 How base currency is determined

The system resolves payroll base currency in this order:

1. Explicit **payroll base currency override** (if set)
2. Company **currency** field (payroll base currency)
3. Default currency for the selected **payroll country** (e.g. RWF for Rwanda, KES for Kenya)

This ensures Rwanda-based companies always calculate statutory payroll in RWF even if not every field is filled in manually.

---

## 5. Employee payroll currency settings

Employee currency settings are configured under **Payroll → Employees → Create/Edit Employee Payroll**.

### 5.1 Fields

| Field | Description |
|-------|-------------|
| **Salary currency** | Currency of the employee’s basic salary and fixed contract amounts. Example: USD for an expatriate on a USD contract. |
| **Payment currency** | Currency paid to the employee. Example: USD if the bank account receives USD. Leave blank to use the company default. |
| **Bank payment currency** | Optional. Used for bank upload files when the bank expects a specific currency label. Defaults to payment currency. |

### 5.2 Typical configurations

| Employee type | Salary currency | Payment currency | Result |
|---------------|-----------------|------------------|--------|
| Local Rwanda staff | RWF | RWF (or blank) | No conversion. Standard local payroll. |
| USD-paid expatriate in Rwanda | USD | USD | Salary converted to RWF for statutory calc; net pay converted back to USD for bank payment. |
| USD contract, RWF payment (unusual) | USD | RWF | Salary converted to RWF for statutory calc; net pay remains in RWF. |

> **Note:** Payment currency selection is only available when **Allow employee payment currency** is enabled at company level.

---

## 6. Exchange rate management

Exchange rates are managed under **Payroll → Settings → Exchange Rates** (`/payroll/settings/exchange-rates`).

### 6.1 Exchange rate record

Each rate includes:

| Field | Description |
|-------|-------------|
| **From currency / To currency** | Currency pair (e.g. USD → RWF, RWF → USD) |
| **Rate** | Conversion factor: 1 unit of *from* currency = *rate* units of *to* currency |
| **Effective date** | Date from which the rate applies |
| **Payroll period** | Optional. Rate can be tied to a specific period or apply generally |
| **Source** | Manual or API |
| **Status** | Active → Locked (after use in payroll) |

### 6.2 Rate lifecycle

```
  Active  ──(Used in payroll)──►  Locked
    │
    └── Edit / Delete (until used in payroll)
```

| Status | Can edit? | Used in payroll? |
|--------|-----------|------------------|
| **Active** | Yes | Yes — immediately after saving |
| **Locked** | No | Yes (historical — already applied to a payslip) |

### 6.3 Effective date policy (company setting)

When payroll runs, the system selects the rate based on the company’s **exchange rate effective date policy**:

| Policy | Rate selected using |
|--------|---------------------|
| Payroll period end date *(default)* | Last day of the payroll period |
| Payroll period start date | First day of the payroll period |
| Payment date | Current payment date |
| Latest rate on or before period end | Most recent active rate on or before period end |

If a period-specific rate exists for the payroll period, it takes priority over a general rate.

---

## 7. Payroll processing flow

When payroll is run for an employee, the system follows this sequence:

```
┌─────────────────────────────────────────────────────────────────┐
│  1. RESOLVE CURRENCIES                                          │
│     • Company base currency (e.g. RWF)                          │
│     • Employee salary currency (e.g. USD)                       │
│     • Employee payment currency (e.g. USD)                      │
└────────────────────────────┬────────────────────────────────────┘
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│  2. CONVERT INPUTS TO BASE CURRENCY (if needed)                 │
│     • Basic salary: USD → RWF                                   │
│     • Fixed allowances/earnings in foreign currency → RWF         │
│     • Percentage-based earnings auto-scale with converted salary│
└────────────────────────────┬────────────────────────────────────┘
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│  3. STATUTORY CALCULATIONS (in base currency only)              │
│     • Gross pay, taxable income                                 │
│     • PAYE, pension, social security, housing levy, etc.        │
│     • Non-statutory deductions, loans, claims                   │
│     • Net pay (in base currency)                                │
└────────────────────────────┬────────────────────────────────────┘
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│  4. CONVERT NET PAY TO PAYMENT CURRENCY (if needed)             │
│     • Net pay: RWF → USD                                        │
└────────────────────────────┬────────────────────────────────────┘
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│  5. SAVE PAYSLIP WITH BOTH CURRENCY SETS                        │
│     • All statutory values in base currency (RWF)               │
│     • Payment amount in payment currency (USD)                  │
│     • Exchange rate used and effective date (snapshotted)       │
│     • Lock exchange rate record                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 7.1 Pre-payroll validation

Before payroll is processed, the system checks whether every foreign-currency employee has the required exchange rates. If rates are missing, a **warning** is displayed on the payroll processing screen listing affected employees. Payroll for those employees will **fail** until rates are entered.

### 7.2 What is stored on each payslip

Every processed payroll record stores:

| Stored value | Currency | Purpose |
|--------------|----------|---------|
| Basic salary, gross pay, all deductions, PAYE, net pay | Base currency | Statutory reporting, audits, local compliance |
| Gross payment, total deductions payment, net pay payment | Payment currency | Bank disbursement |
| Exchange rate used | — | Audit trail |
| Exchange rate effective date | — | Audit trail |
| Currency conversion notes | — | Human-readable explanation on payslip |

Historical payslips are **never recalculated** when exchange rates change later.

---

## 8. Payslips

Payslips clearly show both currency contexts when they differ.

### 8.1 Statutory section (base currency)

All earnings, statutory deductions (PAYE, pension, social security), and net pay in the **payroll base currency** are displayed with the correct currency code (e.g. **RWF**).

### 8.2 Payment section (payment currency)

When payment currency differs from base currency, the payslip additionally shows:

- **Amount paid** in payment currency (e.g. USD)
- **Exchange rate used** (e.g. 1 RWF = 0.00075 USD)
- **Effective date** of the rate
- **Conversion notes** explaining what was converted

### 8.3 Single-currency payslips

Employees paid entirely in the base currency see a standard payslip with one currency throughout — no conversion section is shown.

---

## 9. Reports and bank files

### 9.1 Statutory reports (PAYE, pension, social security, etc.)

| Report type | Currency used | Notes |
|-------------|---------------|-------|
| PAYE returns | Base currency only | Cannot be generated in a foreign currency |
| Pension / social security returns | Base currency only | Figures match local regulator requirements |
| Local statutory summaries | Base currency only | |

Statutory reports always use the **base currency amounts** stored on payroll records. This ensures Rwanda PAYE reports show RWF figures regardless of how employees are paid.

### 9.2 Payment and bank reports

| Report type | Currency used | Notes |
|-------------|---------------|-------|
| Bank upload file | Payment currency | Net pay uses the disbursement amount |
| Payment currency summary | Payment currency | Totals grouped by currency |
| Combined payroll export | Both | Base currency columns + payment currency columns |

Bank upload files use the **net pay in payment currency** (`net_pay_payment_currency`). Files can be generated **per payment currency** so USD and RWF payments are not mixed in the same bank batch unless the bank template supports multiple currencies.

---

## 10. Rounding rules

Different currencies use different decimal precision when converting amounts:

| Currency type | Decimal places | Examples |
|---------------|----------------|----------|
| Zero-decimal currencies | 0 | RWF, UGX, BIF, JPY |
| Standard currencies | 2 | USD, KES, EUR, GBP |
| Three-decimal currencies | 3 | BHD, KWD, OMR |

Rounding is applied at conversion time according to the target currency, avoiding fractional amounts that banks cannot process.

---

## 11. Worked example: Rwanda company with USD-paid employee

### Company setup

| Setting | Value |
|---------|-------|
| Payroll country | Rwanda |
| Payroll base currency | RWF |
| Allow employee payment currency | Yes |
| Default payment currency | RWF |
| Exchange rate policy | Payroll period end date |

### Exchange rates (enter before payroll)

| From | To | Rate | Effective date |
|------|-----|------|----------------|
| USD | RWF | 1,300.00 | 2026-06-30 |
| RWF | USD | 0.00076923 | 2026-06-30 |

### Employee: John (USD contract, USD bank account)

| Setting | Value |
|---------|-------|
| Basic salary | 3,000 USD |
| Salary currency | USD |
| Payment currency | USD |

### Payroll calculation (simplified)

| Step | Calculation | Amount |
|------|-------------|--------|
| 1. Convert salary to RWF | 3,000 × 1,300 | 3,900,000 RWF |
| 2. Calculate gross pay (RWF) | Basic + allowances | 3,900,000 RWF |
| 3. Calculate PAYE (RWF) | Rwanda PAYE bands | e.g. 850,000 RWF |
| 4. Calculate pension & other statutory (RWF) | RSSB, etc. | e.g. 195,000 RWF |
| 5. Net pay (RWF) | Gross − deductions | e.g. 2,855,000 RWF |
| 6. Convert net pay to USD | 2,855,000 × 0.00076923 | **2,196.15 USD** |

### What appears on the payslip

| Section | Display |
|---------|---------|
| Statutory (RWF) | Gross: 3,900,000 RWF · PAYE: 850,000 RWF · Net: 2,855,000 RWF |
| Payment (USD) | **Amount paid: 2,196.15 USD** |
| Exchange rate | 1 RWF = 0.00076923 USD (effective 2026-06-30) |

### What goes to the bank

Bank upload file shows **2,196.15 USD** for John’s account — not the RWF figure.

### What goes to RRA (Rwanda Revenue Authority)

PAYE and statutory returns show **RWF figures** — the 850,000 RWF PAYE, not a USD equivalent.

---

## 12. Worked example: Local Rwanda employee (no conversion)

### Employee: Marie (local staff)

| Setting | Value |
|---------|-------|
| Basic salary | 500,000 RWF |
| Salary currency | RWF |
| Payment currency | RWF (or blank) |

No exchange rates are required. Payroll runs entirely in RWF. The payslip shows one currency. No conversion section appears. This is identical to standard single-currency payroll.

---

## 13. User roles and responsibilities

| Role | Responsibility |
|------|----------------|
| **System administrator** | Configure company payroll country, base currency, and payment currency policy |
| **HR / Payroll administrator** | Set employee salary and payment currencies; enter exchange rates |
| **Payroll processor** | Run payroll; review warnings for missing rates before final approval |
| **Auditor / Compliance** | Review snapshotted rates and dual-currency payslip records |

### Recommended monthly workflow

1. **Before payroll cut-off:** Enter exchange rates for the period (they are used immediately).
2. **During payroll review:** Processor checks the exchange rate warning panel on the payroll processing screen.
3. **After approval:** Verify payslips show correct base currency (statutory) and payment currency (bank) amounts.
4. **Bank file generation:** Generate separate bank upload files per payment currency if required by the bank.
5. **Statutory filing:** Submit reports using base currency figures only.

---

## 14. Validation and controls

The system enforces the following controls:

| Rule | Behaviour |
|------|-----------|
| Missing exchange rate | Payroll cannot complete for affected employees |
| Edit locked rate | Blocked — protects historical integrity |
| Statutory report in foreign currency | Not supported — reports always use base currency |
| Mixed currencies in bank upload | Prevented unless filtered by payment currency |
| Historical payslip recalculation | Never — snapshotted rates preserved |

---

## 15. Frequently asked questions

**Q: Can we pay some employees in USD and others in RWF in the same payroll run?**  
A: Yes. Each employee’s payment currency is resolved individually. Statutory calculations for all employees remain in the company base currency (RWF).

**Q: What happens if we forget to enter an exchange rate before payroll?**  
A: The payroll processing screen shows a warning listing affected employees. Those employees’ payroll will fail until a rate exists for the required currency pair and effective date.

**Q: Can we change an exchange rate after payroll is processed?**  
A: Once a rate is used in payroll it is locked and cannot be edited. To correct an error, the payroll must be reversed/reopened (per your existing payroll approval workflow), a new rate entered, and payroll reprocessed.

**Q: Do statutory reports mix USD and RWF amounts?**  
A: No. All statutory reports use base currency (RWF) amounts only.

**Q: Does this work for other countries besides Rwanda?**  
A: Yes. The same architecture applies to any supported payroll country (Kenya, Uganda, Tanzania, Burundi, South Africa, etc.). The base currency is determined by the company’s payroll country.

**Q: Is an external exchange rate API connected?**  
A: The system supports a manual rate entry workflow today. API-sourced rates are supported in the data model for future integration.

**Q: What if salary currency and payment currency are the same foreign currency (e.g. both USD)?**  
A: The system converts USD → RWF for statutory calculations, then converts the net RWF amount back to USD for bank payment. Two rates are needed: USD→RWF and RWF→USD.

**Q: Are percentage-based allowances affected by currency conversion?**  
A: Percentage-based allowances are calculated from the converted basic salary in base currency, so they scale correctly. Fixed-amount allowances in a foreign currency are converted individually to base currency before statutory calculations.

---

## 16. System menu reference

| Task | Navigation path |
|------|-----------------|
| Configure company currency settings | **Companies → Edit Company** |
| Set employee salary/payment currency | **Payroll → Employees → Create/Edit** |
| Manage exchange rates | **Payroll → Settings → Exchange Rates** |
| Process payroll (with rate warnings) | **Payroll → Process Payroll** |
| View payslip with currency details | **Payroll → Records → View → Print Payslip** |
| Generate bank upload by currency | **Payroll → Period → Bank Upload Report** |

---

## 17. Summary

StawiHR multi-currency payroll provides a clean separation between **compliance** and **disbursement**:

- **Compliance** always happens in the local statutory currency.
- **Disbursement** happens in whatever currency the employee’s bank account requires.
- **Exchange rates** are saved and used immediately, then locked for a full audit trail.
- **Local employees** experience no change to their payroll workflow.

This design supports Rwanda-based employers (and similar international setups) who must report PAYE and statutory deductions in RWF while paying selected employees in USD or other currencies.

---

*For technical implementation details or setup assistance, contact your StawiHR implementation team.*
