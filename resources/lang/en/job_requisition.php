<?php

return [
    // Main headings
    'job_requisition_list' => 'Job Requisition List',
    'create_new_requisition' => 'Create New Requisition',
    'create_new_job_requisition' => 'Create New Job Requisition',
    'edit_job_requisition' => 'Edit Job Requisition',
    'view_requisitions' => 'View Requisitions',
    'job_requisition_details' => 'Job Requisition Details',
    'approve_requisition' => 'Approve Requisition',
    'reject_requisition' => 'Reject Requisition',
    'requisition_number' => 'Requisition Number',
    'required_by' => 'Required By',

    // Form fields
    'position_title' => 'Job Title',
    'job_description' => 'Job Description',
    'job_requirements' => 'Job Requirements',
    'number_of_positions' => 'Number of Positions',
    'job_type' => 'Job Type',
    'employment_type' => 'Employment Type',
    'job_location' => 'Work Location',
    'department' => 'Department',
    'salary_range' => 'Salary Range',
    'minimum_salary' => 'Minimum Salary',
    'maximum_salary' => 'Maximum Salary',
    'currency' => 'Currency',
    'required_by_date' => 'Required By Date',
    'urgency_level' => 'Urgency Level',
    'reason_for_requisition' => 'Reason for Requisition',
    'budget_justification' => 'Budget Justification',
    'reporting_manager' => 'Reporting Manager',
    'recruitment_source' => 'Recruitment Source',

    // Job type options
    'job_types' => [
        'management' => 'Management',
        'executive' => 'Executive',
        'professional' => 'Professional',
        'technical' => 'Technical',
        'support' => 'Support',
        'sales' => 'Sales',
        'marketing' => 'Marketing',
        'finance' => 'Finance',
        'hr' => 'HR',
        'it' => 'IT',
    ],

    // Employment type options
    'employment_types' => [
        'permanent' => 'Permanent',
        'contract' => 'Contract',
        'casual' => 'Casual',
        'internship' => 'Internship',
        'full_time' => 'Full Time',
        'part_time' => 'Part Time',
        'temporary' => 'Temporary',
    ],

    // Urgency levels
    'urgency_levels' => [
        'low' => 'Low',
        'normal' => 'Normal',
        'high' => 'High',
        'critical' => 'Critical',
    ],

    // Recruitment sources
    'recruitment_sources' => [
        'internal' => 'Internal',
        'external' => 'External',
        'both' => 'Both',
    ],

    // Requisition types
    'requisition_types' => [
        'new_position' => 'New Position',
        'replacement' => 'Replacement',
    ],

    // Replacement reasons
    'replacement_reasons' => [
        'resignation' => 'Resignation',
        'termination' => 'Termination',
        'transfer' => 'Transfer',
        'other' => 'Other',
    ],

    // Status and actions
    'basic_information' => 'Basic Information',
    'location_timing' => 'Location & Timing',
    'salary_information' => 'Salary Information',
    'job_details' => 'Job Details',
    'justification' => 'Justification',
    'approval_information' => 'Approval Information',
    'conversion_information' => 'Conversion Information',
    'audit_information' => 'Audit Information',
    'requisition_summary' => 'Requisition Summary',

    // Status labels
    'requested_by' => 'Requested By',
    'approved_by' => 'Approved By',
    'approved_at' => 'Approved At',
    'approval_comments' => 'Approval Comments',
    'rejection_reason' => 'Rejection Reason',
    'converted_to_job' => 'Converted to Job',
    'converted_at' => 'Converted At',

    // Action buttons and confirmations
    'submit_for_approval' => 'Submit for Approval',
    'convert_to_job' => 'Convert to Job',
    'confirm_submit' => 'Are you sure you want to submit this requisition for approval?',
    'confirm_approve' => 'Are you sure you want to approve this requisition?',
    'confirm_reject' => 'Are you sure you want to reject this requisition?',
    'confirm_convert' => 'Are you sure you want to convert this approved requisition to a job post?',

    // Form placeholders and help text
    'search_placeholder' => 'Search by position title, requisition number, or reporting manager...',
    'select_location' => 'Select Location',
    'select_department' => 'Select Department',
    'to' => 'to',
    'approval_comments_placeholder' => 'Enter any comments about the approval decision...',
    'approval_comments_help' => 'Optional comments to provide context for your approval decision.',
    'rejection_reason_placeholder' => 'Please provide a detailed reason for rejection...',
    'rejection_reason_help' => 'Please provide a detailed explanation of why this requisition is being rejected.',

    // Validation messages
    'position_title_required' => 'Position title is required.',
    'job_description_required' => 'Job description is required.',
    'job_requirements_required' => 'Job requirements are required.',
    'number_of_positions_required' => 'Number of positions is required.',
    'job_type_required' => 'Job type is required.',
    'employment_type_required' => 'Employment type is required.',
    'required_by_date_required' => 'Required by date is required.',
    'urgency_level_required' => 'Urgency level is required.',
    'reason_for_requisition_required' => 'Reason for requisition is required.',
    'reporting_manager_required' => 'Reporting manager is required.',
    'recruitment_source_required' => 'Recruitment source is required.',
    'rejection_reason_required' => 'Rejection reason is required.',

    // Success and error messages
    'requisition_created_successfully' => 'Job requisition created successfully.',
    'requisition_updated_successfully' => 'Job requisition updated successfully.',
    'requisition_deleted_successfully' => 'Job requisition deleted successfully.',
    'requisition_submitted_successfully' => 'Job requisition submitted for approval successfully.',
    'requisition_approved_successfully' => 'Job requisition approved successfully.',
    'requisition_rejected_successfully' => 'Job requisition rejected successfully.',
    'requisition_converted_successfully' => 'Job requisition converted to job post successfully.',

    // Common terms
    'create' => 'Create',
    'edit' => 'Edit',
    'update' => 'Update',
    'delete' => 'Delete',
    'submit' => 'Submit',
    'approve' => 'Approve',
    'reject' => 'Reject',
    'cancel' => 'Cancel',
    'convert' => 'Convert',
    'view' => 'View',
    'save' => 'Save',
    'reset' => 'Reset',
    'search' => 'Search',
    'back' => 'Back',

    // Status values
    'draft' => 'Draft',
    'pending_approval' => 'Pending Approval',
    'approved' => 'Approved',
    'rejected' => 'Rejected',
    'cancelled' => 'Cancelled',

    // Information messages
    'no_budget_justification' => 'No budget justification provided.',
    'can_not_edit' => 'This job requisition cannot be edited in its current status.',
    'can_not_submit' => 'This job requisition cannot be submitted for approval.',
    'can_not_approve' => 'This job requisition cannot be approved in its current status.',
    'can_not_reject' => 'This job requisition cannot be rejected in its current status.',
    'can_not_convert' => 'This job requisition cannot be converted to a job post.',
    'can_not_cancel' => 'Approved job requisitions cannot be cancelled.',
    'already_converted' => 'This requisition has already been converted to a job post.',

    // Section headers
    'requisition_details' => 'Requisition Details',
    'job_information' => 'Job Information',
    'approval_workflow' => 'Approval Workflow',
    'conversion_options' => 'Conversion Options',

    // Helper text
    'select_location_helper' => 'Choose the location where this position will be based.',
    'select_department_helper' => 'Select the department that this position belongs to.',
    'urgency_helper' => 'Indicate how urgently this position needs to be filled.',
    'recruitment_source_helper' => 'Choose where candidates should be sourced from.',
    'salary_helper' => 'Provide the expected salary range for this position.',

    // Time related
    'created_at' => 'Created At',
    'updated_at' => 'Updated At',
    'days_remaining' => 'days remaining',
    'overdue' => 'overdue',
    'urgent' => 'urgent',

    // Permission related
    'can_create' => 'Create Job Requisition',
    'can_edit' => 'Edit Job Requisition',
    'can_approve' => 'Approve Job Requisition',
    'can_convert' => 'Convert to Job Post',
    'permission_denied' => 'You do not have permission to perform this action.',
];
