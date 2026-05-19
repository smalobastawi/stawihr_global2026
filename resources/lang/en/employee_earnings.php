<?php

return [
    // General
    'employee_earnings' => 'Employee Earnings',
    'employee_earnings_list' => 'Employee Earnings List',
    'employee_earning_details' => 'Employee Earning Details',
    'add_employee_earning' => 'Add Employee Earning',
    'edit_employee_earning' => 'Edit Employee Earning',
    'view_employee_earnings' => 'View Employee Earnings',
    'approve_earning' => 'Approve Earning',
    'suspend_earning' => 'Suspend Earning',

    // Form Fields
    'employee' => 'Employee',
    'employee_name' => 'Employee Name',
    'staff_number' => 'Staff Number',
    'payroll_earning_type' => 'Payroll Earning Type',
    'earning_name' => 'Earning Name',
    'earning_category' => 'Category',
    'earning_type' => 'Earning Type',
    'calculation_type' => 'Calculation Type',
    'amount' => 'Amount',
    'percentage' => 'Percentage',
    'rate' => 'Rate',
    'units' => 'Units',
    'calculated_amount' => 'Calculated Amount',
    'limit_per_month' => 'Limit Per Month',
    'limit_per_year' => 'Limit Per Year',
    'effective_from' => 'Effective From',
    'effective_to' => 'Effective To',
    'effective_period' => 'Effective Period',
    'payroll_year' => 'Payroll Year',
    'payroll_month' => 'Payroll Month',
    'payroll_period' => 'Payroll Period',
    'frequency' => 'Frequency',
    'is_taxable' => 'Is Taxable',
    'is_pensionable' => 'Is Pensionable',
    'is_recurring' => 'Is Recurring',
    'description' => 'Description',
    'reference_number' => 'Reference Number',

    // Categories
    'basic_salary' => 'Basic Income',
    'allowance' => 'Allowance',
    'bonus' => 'Bonus',
    'overtime' => 'Overtime',
    'commission' => 'Commission',
    'other' => 'Other',

    // Calculation Types
    'fixed_amount' => 'Fixed Amount',
    'percentage_of_basic' => 'Percentage of Basic Income',
    'percentage_of_gross' => 'Percentage of Gross Salary',
    'hourly_rate' => 'Hourly Rate',
    'daily_rate' => 'Daily Rate',

    // Frequencies
    'monthly' => 'Monthly',
    'weekly' => 'Weekly',
    'bi_weekly' => 'Bi-Weekly',
    'quarterly' => 'Quarterly',
    'annually' => 'Annually',
    'one_time' => 'One Time',

    // Status
    'active' => 'Active',
    'inactive' => 'Inactive',
    'suspended' => 'Suspended',
    'expired' => 'Expired',

    // Information Sections
    'employee_information' => 'Employee Information',
    'earning_information' => 'Earning Information',
    'calculation_details' => 'Calculation Details',
    'period_and_frequency' => 'Period and Frequency',
    'tax_and_benefits' => 'Tax and Benefits',
    'approval_information' => 'Approval Information',

    // Employee Details
    'department' => 'Department',
    'designation' => 'Designation',
    'location' => 'Location',

    // Approval
    'approved_by' => 'Approved By',
    'approved_at' => 'Approved At',
    'approval_notes' => 'Approval Notes',
    'created_by' => 'Created By',
    'created_at' => 'Created At',
    'updated_by' => 'Updated By',
    'updated_at' => 'Updated At',

    // Placeholders
    'search_placeholder' => 'Search by earning name, employee name, staff number, or reference number...',
    'approval_notes_placeholder' => 'Enter approval notes (optional)',
    'suspend_reason_placeholder' => 'Enter reason for suspension',

    // Messages
    'earning_created_successfully' => 'Employee earning created successfully.',
    'earning_updated_successfully' => 'Employee earning updated successfully.',
    'earning_deleted_successfully' => 'Employee earning deleted successfully.',
    'earning_approved_successfully' => 'Employee earning approved successfully.',
    'earning_rejected_successfully' => 'Employee earning rejected successfully.',
    'earning_suspended_successfully' => 'Employee earning suspended successfully.',
    'earning_not_found' => 'Employee earning not found.',
    'error_creating_earning' => 'Error creating employee earning.',
    'error_updating_earning' => 'Error updating employee earning.',
    'error_deleting_earning' => 'Error deleting employee earning.',
    'error_approving_earning' => 'Error approving employee earning.',
    'error_suspending_earning' => 'Error suspending employee earning.',

    // Validation Messages
    'employee_required' => 'Employee is required.',
    'earning_type_required' => 'Earning type is required.',
    'earning_name_required' => 'Earning name is required.',
    'earning_category_required' => 'Earning category is required.',
    'calculation_type_required' => 'Calculation type is required.',
    'amount_required' => 'Amount is required when calculation type is fixed amount.',
    'percentage_required' => 'Percentage is required when calculation type is percentage-based.',
    'rate_required' => 'Rate is required when calculation type is rate-based.',
    'effective_from_required' => 'Effective from date is required.',
    'effective_to_after_from' => 'Effective to date must be after effective from date.',
    'payroll_year_required' => 'Payroll year is required.',
    'payroll_month_required' => 'Payroll month is required.',
    'frequency_required' => 'Frequency is required.',
    'amount_must_be_positive' => 'Amount must be a positive number.',
    'percentage_must_be_valid' => 'Percentage must be between 0 and 100.',
    'rate_must_be_positive' => 'Rate must be a positive number.',
    'units_must_be_positive' => 'Units must be a positive number.',
    'limit_must_be_positive' => 'Limit must be a positive number.',

    // Help Text
    'earning_name_help' => 'Enter a descriptive name for this earning.',
    'calculation_type_help' => 'Select how this earning should be calculated.',
    'amount_help' => 'Enter the fixed amount for this earning.',
    'percentage_help' => 'Enter the percentage of basic/gross salary.',
    'rate_help' => 'Enter the hourly or daily rate.',
    'units_help' => 'Enter the number of hours or days.',
    'limit_per_month_help' => 'Maximum amount that can be earned per month (optional).',
    'limit_per_year_help' => 'Maximum amount that can be earned per year (optional).',
    'effective_from_help' => 'Date when this earning becomes effective.',
    'effective_to_help' => 'Date when this earning expires (leave blank for indefinite).',
    'is_taxable_help' => 'Check if this earning is subject to income tax.',
    'is_pensionable_help' => 'Check if this earning contributes to pension calculations.',
    'is_recurring_help' => 'Check if this earning should recur based on frequency.',
    'frequency_help' => 'How often this earning should be applied.',
    'description_help' => 'Additional notes or description for this earning.',

    // Filter Labels
    'all_categories' => 'All Categories',
    'all_years' => 'All Years',
    'all_months' => 'All Months',
    'all_status' => 'All Status',

    // Actions
    'calculate_earnings' => 'Calculate Earnings',
    'bulk_approve' => 'Bulk Approve',
    'bulk_suspend' => 'Bulk Suspend',
    'export_earnings' => 'Export Earnings',
    'import_earnings' => 'Import Earnings',

    // Reports
    'earnings_report' => 'Earnings Report',
    'monthly_earnings' => 'Monthly Earnings',
    'yearly_earnings' => 'Yearly Earnings',
    'employee_earnings_summary' => 'Employee Earnings Summary',
    'earnings_by_category' => 'Earnings by Category',
    'taxable_earnings' => 'Taxable Earnings',
    'non_taxable_earnings' => 'Non-Taxable Earnings',

    // Totals
    'total_earnings' => 'Total Earnings',
    'total_taxable' => 'Total Taxable',
    'total_non_taxable' => 'Total Non-Taxable',
    'monthly_total' => 'Monthly Total',
    'yearly_total' => 'Yearly Total',

    // Common Actions
    'view_details' => 'View Details',
    'edit_earning' => 'Edit Earning',
    'delete_earning' => 'Delete Earning',
    'approve_earning_action' => 'Approve Earning',
    'suspend_earning_action' => 'Suspend Earning',
    'activate_earning' => 'Activate Earning',

    // Confirmation Messages
    'confirm_delete' => 'Are you sure you want to delete this earning?',
    'confirm_approve' => 'Are you sure you want to approve this earning?',
    'confirm_suspend' => 'Are you sure you want to suspend this earning?',
    'confirm_bulk_action' => 'Are you sure you want to perform this action on selected earnings?',

    // Navigation
    'back_to_list' => 'Back to Earnings List',
    'add_new_earning' => 'Add New Earning',
    'earnings_management' => 'Earnings Management',

    // Statistics
    'total_active_earnings' => 'Total Active Earnings',
    'total_suspended_earnings' => 'Total Suspended Earnings',
    'total_expired_earnings' => 'Total Expired Earnings',
    'earnings_this_month' => 'Earnings This Month',
    'earnings_this_year' => 'Earnings This Year',
];
