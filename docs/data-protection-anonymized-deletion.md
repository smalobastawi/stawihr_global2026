# Anonymized Soft Delete (Data Protection)

StawiHR uses an **anonymized soft delete** pattern when administrators remove users from User Management. This protects personal data while preserving historical HR, payroll, and audit records linked to the account.

## Why not hard delete?

Hard deletion (`forceDelete`) often fails because users and employees are referenced by attendance, payroll, leave, roles, and other records. Removing rows entirely would break reporting and compliance trails.

Plain deactivation leaves real email addresses, phone numbers, and names in the database, which creates privacy risk and blocks re-use of those identifiers for new accounts (unique constraints on `user.email`, `user.user_name`, `employee.email`, `employee.staff_no`, etc.).

## What happens on delete

When an administrator deletes a user from **User Management**:

1. **Backup** — Original personal fields are stored in `anonymized_record_backups` (user data, employee data if linked, and assigned role names).
2. **Anonymize** — Identifiers and contact details are replaced with non-identifying placeholders, for example:
   - User email → `deleted.user.{id}.{timestamp}@anonymized.stawihr.local`
   - Username → `deleted_user_{id}.{timestamp}`
   - Employee name → `Deleted Employee`
   - National ID, phone, bank details, and similar fields → cleared or placeholder values
3. **Deactivate** — `status` is set to inactive and login roles are removed. Password is rotated to a random value.
4. **Soft delete** — `deleted_at` is set on the user row and on the linked employee row (if any). Related transactional history (payroll, attendance, leave, etc.) remains intact and still references the same internal IDs.

The original email, username, and staff number become available for new records because the live rows no longer hold those values.

## Restore

Anonymized users appear on **Inactive Users** with an **Anonymized** status and a restore action.

Restore:

1. Loads the latest restorable backup for that user.
2. Verifies that original email, username, and staff number are not already taken by another account.
3. Restores user and employee personal fields, reactivates both records, and re-applies saved roles.
4. Marks the backup as restored (restored timestamp and administrator ID).

If identifiers were re-used elsewhere, restore is blocked with a clear error so administrators can resolve the conflict manually.

## Data stored in backups

| Area | Examples |
|------|----------|
| User | `user_name`, `email`, `msisdn`, OAuth tokens |
| Employee | Names, emails, phone, national ID, tax/social IDs, bank details, staff number |
| Access | Role names assigned at deletion time |

Backups are intended for controlled restoration by authorized administrators, not for routine reporting. Access should be limited through existing admin permissions (`user.edit` / `user.destroy`).

## Technical entry points

| Action | Location |
|--------|----------|
| Anonymize user (+ linked employee) | `UserController@destroy` → `AnonymizedDeletionService::anonymizeUser()` |
| Anonymize employee (+ linked user) | `EmployeeController@destroy` → `AnonymizedDeletionService::anonymizeEmployee()` |
| Restore (user list) | `UserController@restore` → `AnonymizedDeletionService::restoreUser()` |
| Restore (employee profile) | `EmployeeController@restore` / `@enable` → `AnonymizedDeletionService::restoreEmployee()` |
| Backup model | `App\Models\AnonymizedRecordBackup` |
| Service | `App\Services\AnonymizedDeletionService` |

## Compliance notes

- **Right to erasure / privacy**: Personal identifiers visible in the UI are anonymized; operational history retained for legitimate business purposes (payroll, audit).
- **Re-hire / account reuse**: Same email or username can be registered again after anonymization.
- **Audit trail**: Internal user and employee IDs are unchanged, so historical records remain joinable without exposing former PII in active profile fields.

For questions about retention periods or permanent erasure of backup rows, define your organisation’s policy and extend this flow (e.g. scheduled purge of `anonymized_record_backups` after a defined period).
