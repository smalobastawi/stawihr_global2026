<?php

namespace App\Console\Commands;

class GroupedRoutePermissions
{
    public function groupedPermissions()
    {

        return [
            [
                "permission_group" => "advance_types",
                "group_description" => "Advance Types",
                "permissions" => [
                    [
                        "name" => "advance_types.create",
                        "description" => "Create Advance Types",
                    ],
                    [
                        "name" => "advance_types.destroy",
                        "description" => "Delete Advance Types",
                    ],
                    [
                        "name" => "advance_types.edit",
                        "description" => "Edit Advance Types",
                    ],
                    [
                        "name" => "advance_types.index",
                        "description" => "View Advance Types",
                    ],
                    [
                        "name" => "advance_types.show",
                        "description" => "Show Advance Types",
                    ],
                    [
                        "name" => "advance_types.store",
                        "description" => "Create Advance Types",
                    ],
                    [
                        "name" => "advance_types.update",
                        "description" => "Update Advance Types",
                    ],
                ],
            ],

            [
                "permission_group" => "advances",
                "group_description" => "Advances",
                "permissions" => [
                    [
                        "name" => "advances.create",
                        "description" => "Create Advances",
                    ],
                    [
                        "name" => "advances.destroy",
                        "description" => "Delete Advances",
                    ],
                    [
                        "name" => "advances.edit",
                        "description" => "Edit Advances",
                    ],
                    [
                        "name" => "advances.index",
                        "description" => "View Advances",
                    ],
                    [
                        "name" => "advances.show",
                        "description" => "Show Advances",
                    ],
                    [
                        "name" => "advances.store",
                        "description" => "Create Advances",
                    ],
                    [
                        "name" => "advances.update",
                        "description" => "Update Advances",
                    ],
                ],
            ],
            [
                "permission_group" => "allowance",
                "group_description" => "Allowances",
                "permissions" => [
                    [
                        "name" => "allowance.create",
                        "description" => "Create Allowance",
                    ],
                    [
                        "name" => "allowance.delete",
                        "description" => "Delete Allowance",
                    ],
                    [
                        "name" => "allowance.edit",
                        "description" => "Edit Allowance",
                    ],
                    [
                        "name" => "allowance.index",
                        "description" => "View Allowances",
                    ],
                    [
                        "name" => "allowance.store",
                        "description" => "Create Allowance",
                    ],
                    [
                        "name" => "allowance.update",
                        "description" => "Update Allowance",
                    ],
                ],
            ],
            [
                "permission_group" => "applicant",
                "group_description" => "Applicants",
                "permissions" => [
                    [
                        "name" => "applicant.hire",
                        "description" => "Hire Applicant",
                    ],
                    [
                        "name" => "applicant.jobInterview",
                        "description" => "Schedule Job Interview",
                    ],
                    [
                        "name" => "applicant.jobInterviewStore",
                        "description" => "Store Job Interview",
                    ],
                    [
                        "name" => "applicant.reject",
                        "description" => "Reject Applicant",
                    ],
                    [
                        "name" => "applicant.shortlist",
                        "description" => "Shortlist Applicant",
                    ],
                ],
            ],
            [
                "permission_group" => "applyForLeave",
                "group_description" => "Apply For Leave",
                "permissions" => [
                    [
                        "name" => "applyForLeave.create",
                        "description" => "Create Leave Application",
                    ],
                    [
                        "name" => "applyForLeave.index",
                        "description" => "View Leave Applications",
                    ],
                    [
                        "name" => "applyForLeave.show",
                        "description" => "Show Leave Application",
                    ],
                    [
                        "name" => "applyForLeave.store",
                        "description" => "Store Leave Application",
                    ],
                    [
                        "name" => "applyForLeave.applyOnBehalf.create",
                        "description" => "Apply Leave On Behalf of Employee",
                    ],
                    [
                        "name" => "applyForLeave.applyOnBehalf.store",
                        "description" => "Store Leave Application On Behalf",
                    ],
                ],
            ],
            [
                "permission_group" => "approvals",
                "group_description" => "Approvals",
                "permissions" => [
                    [
                        "name" => "approvals.create",
                        "description" => "Create Approval",
                    ],
                    [
                        "name" => "approvals.delete",
                        "description" => "Delete Approval",
                    ],
                    [
                        "name" => "approvals.index",
                        "description" => "View Approvals",
                    ],
                    [
                        "name" => "approvals.store",
                        "description" => "Store Approval",
                    ],
                    [
                        "name" => "approvals.update",
                        "description" => "Update Approval",
                    ],
                    [
                        "name" => "approvals.view",
                        "description" => "View Approval",
                    ],
                ],
            ],
            [
                "permission_group" => "attendance",
                "group_description" => "Attendance",
                "permissions" => [
                    [
                        "name" => "attendance.anomalies",
                        "description" => "View Attendance Anomalies",
                    ],
                    [
                        "name" => "attendance.anomaliesStore",
                        "description" => "Store Attendance Anomalies",
                    ],
                    [
                        "name" => "attendance.anomalyReport",
                        "description" => "Generate Anomaly Report",
                    ],
                    [
                        "name" => "attendance.anomalyReportFilter",
                        "description" => "Filter Anomaly Report",
                    ],
                    [
                        "name" => "attendance.approveOvertimes",
                        "description" => "Approve Overtimes",
                    ],
                    [
                        "name" => "attendance.correctFromExcel",
                        "description" => "Correct Attendance From Excel",
                    ],
                    [
                        "name" => "attendance.dashboard",
                        "description" => "View Attendance Dashboard",
                    ],
                    [
                        "name" => "attendance.dashboard.post",
                        "description" => "Post Attendance Dashboard",
                    ],
                    [
                        "name" => "attendance.filterOvertime",
                        "description" => "Filter Overtime",
                    ],
                    [
                        "name" => "attendance.mealReport",
                        "description" => "Generate Meal Report",
                    ],
                    [
                        "name" => "attendance.mealReportFilter",
                        "description" => "Filter Meal Report",
                    ],
                    [
                        "name" => "attendance.overtimeApproval",
                        "description" => "Overtime Approval",
                    ],
                    [
                        "name" => "attendance.storeFromExcel",
                        "description" => "Store Attendance From Excel",
                    ],
                    [
                        "name" => "attendance.view_raw_logs",
                        "description" => "View Raw Attendance Logs",
                    ],
                ],
            ],
            [
                "permission_group" => "attendanceSummaryReport",
                "group_description" => "Attendance Summary Report",
                "permissions" => [
                    [
                        "name" => "attendanceSummaryReport.attendanceSummaryReport",
                        "description" => "Generate Attendance Summary Report",
                    ],
                    [
                        "name" => "attendanceSummaryReport.attendanceSummaryReportFilter",
                        "description" => "Filter Attendance Summary Report",
                    ],
                ],
            ],
            [
                "permission_group" => "award",
                "group_description" => "Awards",
                "permissions" => [
                    [
                        "name" => "award.create",
                        "description" => "Create Award",
                    ],
                    [
                        "name" => "award.delete",
                        "description" => "Delete Award",
                    ],
                    [
                        "name" => "award.edit",
                        "description" => "Edit Award",
                    ],
                    [
                        "name" => "award.index",
                        "description" => "View Awards",
                    ],
                    [
                        "name" => "award.store",
                        "description" => "Create Award",
                    ],
                    [
                        "name" => "award.update",
                        "description" => "Update Award",
                    ],
                ],
            ],
            [
                "permission_group" => "azure",
                "group_description" => "Azure",
                "permissions" => [
                    [
                        "name" => "azure.login",
                        "description" => "Login to Azure",
                    ],
                ],
            ],
            [
                "permission_group" => "ahlReportIndex",
                "group_description" => "AHL Report",
                "permissions" => [
                    [
                        "name" => "ahlReportIndex",
                        "description" => "View AHL Report",
                    ],
                ],
            ],
            [
                "permission_group" => "allLeaveApplications",
                "group_description" => "Leave Applications",
                "permissions" => [
                    [
                        "name" => "allLeaveApplications.allLeaveApplications",
                        "description" => "View All Leave Applications",
                    ],
                ],
            ],
            [
                "permission_group" => "addRolloverLeave1",
                "group_description" => "Rollover Leave",
                "permissions" => [
                    [
                        "name" => "addRolloverLeave1",
                        "description" => "Add Rollover Leave",
                    ],
                ],
            ],

            [
                'permission_group' => 'biometricDevices',
                'group_description' => 'Biometric Devices',
                'permissions' => [
                    [
                        'name' => 'biometricGet.index',
                        'description' => 'View Biometric Devices',
                    ],
                    [
                        'name' => 'biometricUpdate',
                        'description' => 'Update Biometric Devices',
                    ],
                    [
                        'name' => 'createDevice',
                        'description' => 'Create Devices',
                    ],
                    [
                        'name' => 'zkbiometricGet.index',
                        'description' => 'View ZK Biometric Data',
                    ],
                    [
                        'name' => 'storeDevice',
                        'description' => 'Store Device',
                    ],
                    [
                        'name' => 'deleteBioDevice',
                        'description' => 'Delete Bio Device',
                    ],
                    [
                        'name' => 'editBioDevice',
                        'description' => 'Edit Bio Device',
                    ],
                    [
                        'name' => 'posteditBioDevice',
                        'description' => 'Post Edit Bio Device',
                    ],
                    [
                        'name' => 'devices',
                        'description' => 'Manage Devices',
                    ],

                ],
            ],

            [
                'permission_group' => 'bonus_types',
                'group_description' => 'Bonus Types',
                'permissions' => [
                    [
                        'name' => 'bonus_types.create',
                        'description' => 'Create Bonus Types',
                    ],
                    [
                        'name' => 'bonus_types.destroy',
                        'description' => 'Delete Bonus Types',
                    ],
                    [
                        'name' => 'bonus_types.edit',
                        'description' => 'Edit Bonus Types',
                    ],
                    [
                        'name' => 'bonus_types.index',
                        'description' => 'View Bonus Types',
                    ],
                    [
                        'name' => 'bonus_types.show',
                        'description' => 'Show Bonus Types',
                    ],
                    [
                        'name' => 'bonus_types.store',
                        'description' => 'Create Bonus Types',
                    ],
                    [
                        'name' => 'bonus_types.update',
                        'description' => 'Update Bonus Types',
                    ],
                ],
            ],
            [
                'permission_group' => 'bonuses',
                'group_description' => 'Bonuses',
                'permissions' => [
                    [
                        'name' => 'bonuses.create',
                        'description' => 'Create Bonuses',
                    ],
                    [
                        'name' => 'bonuses.destroy',
                        'description' => 'Delete Bonuses',
                    ],
                    [
                        'name' => 'bonuses.edit',
                        'description' => 'Edit Bonuses',
                    ],
                    [
                        'name' => 'bonuses.index',
                        'description' => 'View Bonuses',
                    ],
                    [
                        'name' => 'bonuses.show',
                        'description' => 'Show Bonuses',
                    ],
                    [
                        'name' => 'bonuses.store',
                        'description' => 'Create Bonuses',
                    ],
                    [
                        'name' => 'bonuses.update',
                        'description' => 'Update Bonuses',
                    ],
                ],
            ],
            [
                'permission_group' => 'bonusSetting',
                'group_description' => 'Bonus Settings',
                'permissions' => [
                    [
                        'name' => 'bonusSetting.create',
                        'description' => 'Create Bonus Settings',
                    ],
                    [
                        'name' => 'bonusSetting.delete',
                        'description' => 'Delete Bonus Settings',
                    ],
                    [
                        'name' => 'bonusSetting.edit',
                        'description' => 'Edit Bonus Settings',
                    ],
                    [
                        'name' => 'bonusSetting.index',
                        'description' => 'View Bonus Settings',
                    ],
                    [
                        'name' => 'bonusSetting.store',
                        'description' => 'Create Bonus Settings',
                    ],
                    [
                        'name' => 'bonusSetting.update',
                        'description' => 'Update Bonus Settings',
                    ],
                ],
            ],
            [
                'permission_group' => 'branch',
                'group_description' => 'Locations',
                'permissions' => [
                    [
                        'name' => 'branch.create',
                        'description' => 'Create Locations',
                    ],
                    [
                        'name' => 'branch.delete',
                        'description' => 'Delete Locations',
                    ],
                    [
                        'name' => 'branch.edit',
                        'description' => 'Edit Locations',
                    ],
                    [
                        'name' => 'branch.index',
                        'description' => 'View Locations',
                    ],
                    [
                        'name' => 'branch.store',
                        'description' => 'Create Locations',
                    ],
                    [
                        'name' => 'branch.update',
                        'description' => 'Update Locations',
                    ],
                ],
            ],
            [
                'permission_group' => 'calculateManagementPay',
                'group_description' => 'Calculate Management Pay',
                'permissions' => [
                    [
                        'name' => 'calculateManagementPay',
                        'description' => 'Calculate Management Pay',
                    ],
                ],
            ],
            [
                'permission_group' => 'calculatePaye',
                'group_description' => 'Calculate PAYE',
                'permissions' => [
                    [
                        'name' => 'calculatePaye',
                        'description' => 'Calculate PAYE',
                    ],
                ],
            ],
            [
                'permission_group' => 'ceoPendingLeaveRequests',
                'group_description' => 'CEO Pending Leave Requests',
                'permissions' => [
                    [
                        'name' => 'ceoPendingLeaveRequests.ceoPendingLeaveRequests',
                        'description' => 'View CEO Pending Leave Requests',
                    ],
                ],
            ],
            [
                'permission_group' => 'changePassword',
                'group_description' => 'Change Passwords',
                'permissions' => [
                    [
                        'name' => 'changePassword.create',
                        'description' => 'Create Change Password Requests',
                    ],
                    [
                        'name' => 'changePassword.destroy',
                        'description' => 'Delete Change Password Requests',
                    ],
                    [
                        'name' => 'changePassword.edit',
                        'description' => 'Edit Change Password Requests',
                    ],
                    [
                        'name' => 'changePassword.index',
                        'description' => 'View Change Password Requests',
                    ],
                    [
                        'name' => 'changePassword.show',
                        'description' => 'Show Change Password Request Details',
                    ],
                    [
                        'name' => 'changePassword.store',
                        'description' => 'Create Change Password Requests',
                    ],
                    [
                        'name' => 'changePassword.update',
                        'description' => 'Update Change Password Requests',
                    ],
                ],
            ],
            [
                'permission_group' => 'company',
                'group_description' => 'Company Settings',
                'permissions' => [
                    [
                        'name' => 'company.setting',
                        'description' => 'View Company Settings',
                    ],
                    [
                        'name' => 'company.setting.post',
                        'description' => 'Update Company Settings',
                    ],
                ],
            ],
            [
                'permission_group' => 'contract',
                'group_description' => 'Contracts',
                'permissions' => [
                    [
                        'name' => 'contract.create',
                        'description' => 'Create Contracts',
                    ],
                    [
                        'name' => 'contract.delete',
                        'description' => 'Delete Contracts',
                    ],
                    [
                        'name' => 'contract.destroy',
                        'description' => 'Destroy Contracts',
                    ],
                    [
                        'name' => 'contract.edit',
                        'description' => 'Edit Contracts',
                    ],
                    [
                        'name' => 'contract.index',
                        'description' => 'View Contracts',
                    ],
                    [
                        'name' => 'contract.show',
                        'description' => 'Show Contract Details',
                    ],
                    [
                        'name' => 'contract.store',
                        'description' => 'Create Contracts',
                    ],
                    [
                        'name' => 'contract.update',
                        'description' => 'Update Contracts',
                    ],
                ],
            ],
            [
                'permission_group' => 'daily_pay',
                'group_description' => 'Daily Pay',
                'permissions' => [
                    [
                        'name' => 'daily_pay.create',
                        'description' => 'Create Daily Pay',
                    ],
                    [
                        'name' => 'daily_pay.destroy',
                        'description' => 'Delete Daily Pay',
                    ],
                    [
                        'name' => 'daily_pay.edit',
                        'description' => 'Edit Daily Pay',
                    ],
                    [
                        'name' => 'daily_pay.index',
                        'description' => 'View Daily Pay',
                    ],
                    [
                        'name' => 'daily_pay.show',
                        'description' => 'Show Daily Pay Details',
                    ],
                    [
                        'name' => 'daily_pay.store',
                        'description' => 'Create Daily Pay',
                    ],
                    [
                        'name' => 'daily_pay.update',
                        'description' => 'Update Daily Pay',
                    ],
                ],
            ],
            [
                'permission_group' => 'dailyAttendance',
                'group_description' => 'Daily Attendance',
                'permissions' => [
                    [
                        'name' => 'dailyAttendance.dailyAttendance',
                        'description' => 'View Daily Attendance',
                    ],
                    [
                        'name' => 'dailyAttendance.dailyAttendanceFilter',
                        'description' => 'Filter Daily Attendance',
                    ],
                ],
            ],
            [
                'permission_group' => 'DailyPay',
                'group_description' => 'Daily Pay Management',
                'permissions' => [
                    [
                        'name' => 'DailyPay.import',
                        'description' => 'Import Daily Pay',
                    ],
                    [
                        'name' => 'dailyPay.importView',
                        'description' => 'View Daily Pay Import',
                    ],
                ],
            ],
            [
                'permission_group' => 'deduction',
                'group_description' => 'Deductions',
                'permissions' => [
                    [
                        'name' => 'deduction.create',
                        'description' => 'Create Deduction',
                    ],
                    [
                        'name' => 'deduction.delete',
                        'description' => 'Delete Deduction',
                    ],
                    [
                        'name' => 'deduction.edit',
                        'description' => 'Edit Deduction',
                    ],
                    [
                        'name' => 'deduction.index',
                        'description' => 'View Deductions',
                    ],
                    [
                        'name' => 'deduction.store',
                        'description' => 'Create Deduction',
                    ],
                    [
                        'name' => 'deduction.update',
                        'description' => 'Update Deduction',
                    ],
                ],
            ],
            [
                'permission_group' => 'delete_salary_entry',
                'group_description' => 'Delete Salary Entry',
                'permissions' => [
                    [
                        'name' => 'delete_salary_entry',
                        'description' => 'Delete Salary Entry',
                    ],
                ],
            ],
            [
                'permission_group' => 'department',
                'group_description' => 'Departments',
                'permissions' => [
                    [
                        'name' => 'department.create',
                        'description' => 'Create Department',
                    ],
                    [
                        'name' => 'department.delete',
                        'description' => 'Delete Department',
                    ],
                    [
                        'name' => 'department.edit',
                        'description' => 'Edit Department',
                    ],
                    [
                        'name' => 'department.index',
                        'description' => 'View Departments',
                    ],
                    [
                        'name' => 'department.store',
                        'description' => 'Create Department',
                    ],
                    [
                        'name' => 'department.update',
                        'description' => 'Update Department',
                    ],
                ],
            ],
            [
                'permission_group' => 'designation',
                'group_description' => 'Designations',
                'permissions' => [
                    [
                        'name' => 'designation.create',
                        'description' => 'Create Designation',
                    ],
                    [
                        'name' => 'designation.delete',
                        'description' => 'Delete Designation',
                    ],
                    [
                        'name' => 'designation.edit',
                        'description' => 'Edit Designation',
                    ],
                    [
                        'name' => 'designation.index',
                        'description' => 'View Designations',
                    ],
                    [
                        'name' => 'designation.store',
                        'description' => 'Create Designation',
                    ],
                    [
                        'name' => 'designation.update',
                        'description' => 'Update Designation',
                    ],
                ],
            ],
            [
                'permission_group' => 'downloadPayslip',
                'group_description' => 'Download Payslip',
                'permissions' => [
                    [
                        'name' => 'downloadPayslip',
                        'description' => 'Download Payslips',
                    ],
                    [
                        'name' => 'downloadPayslip.self',
                        'description' => 'Download Your Payslip',
                    ],
                ],
            ],
            [
                'permission_group' => 'downloadStaffReport',
                'group_description' => 'Download Staff Report',
                'permissions' => [
                    [
                        'name' => 'downloadStaffReport.downloadStaffReport',
                        'description' => 'Download Staff Report',
                    ],
                ],
            ],
            [
                'permission_group' => 'duplictes',
                'group_description' => 'Duplicates',
                'permissions' => [
                    [
                        'name' => 'duplictes.remove',
                        'description' => 'Remove Duplicates',
                    ],
                ],
            ],
            [
                'permission_group' => 'earnLeaveConfigure',
                'group_description' => 'Earn Leave Configuration',
                'permissions' => [
                    [
                        'name' => 'earnLeaveConfigure.index',
                        'description' => 'View Earn Leave Configuration',
                    ],
                ],
            ],
            [
                'permission_group' => 'employee',
                'group_description' => 'Employees',
                'permissions' => [
                    [
                        'name' => 'employee.active',
                        'description' => 'Activate Employee',
                    ],
                    [
                        'name' => 'employee.create',
                        'description' => 'Create Employee',
                    ],
                    [
                        'name' => 'employee.delete',
                        'description' => 'Delete Employee',
                    ],
                    [
                        'name' => 'employee.disable',
                        'description' => 'Disable Employee',
                    ],
                    [
                        'name' => 'employee.downloadReport',
                        'description' => 'Download Employee Report',
                    ],
                    [
                        'name' => 'employee.edit',
                        'description' => 'Edit Employee',
                    ],
                    [
                        'name' => 'employee.enable',
                        'description' => 'Enable Employee',
                    ],
                    [
                        'name' => 'employee.importView',
                        'description' => 'View Employee Import',
                    ],
                    [
                        'name' => 'employee.index',
                        'description' => 'View Employees',
                    ],
                    [
                        'name' => 'employee.joinersReport',
                        'description' => 'View Employee Joiners Report',
                    ],
                    [
                        'name' => 'employee.leaversReport',
                        'description' => 'View Employee Leavers Report',
                    ],
                    [
                        'name' => 'employee.turnoverReport',
                        'description' => 'View Employee Turnover Report',
                    ],
                    [
                        'name' => 'employee.movementReport',
                        'description' => 'View Employee Movement Report',
                    ],
                    [
                        'name' => 'employee.show',
                        'description' => 'Show Employee Details',
                    ],
                    [
                        'name' => 'employee.store',
                        'description' => 'Create Employee',
                    ],
                    [
                        'name' => 'employee.update',
                        'description' => 'Update Employee',
                    ],
                ],
            ],
            [
                'permission_group' => 'employeeGroup',
                'group_description' => 'Employee Groups',
                'permissions' => [
                    [
                        'name' => 'employeeGroup.create',
                        'description' => 'Create Employee Group',
                    ],
                    [
                        'name' => 'employeeGroup.destroy',
                        'description' => 'Destroy Employee Group',
                    ],
                    [
                        'name' => 'employeeGroup.edit',
                        'description' => 'Edit Employee Group',
                    ],
                    [
                        'name' => 'employeeGroup.index',
                        'description' => 'View Employee Groups',
                    ],
                    [
                        'name' => 'employeeGroup.show',
                        'description' => 'Show Employee Group Details',
                    ],
                    [
                        'name' => 'employeeGroup.store',
                        'description' => 'Create Employee Group',
                    ],
                    [
                        'name' => 'employeeGroup.update',
                        'description' => 'Update Employee Group',
                    ],
                ],
            ],
            [
                'permission_group' => 'employeeMovement',
                'group_description' => 'Employee Movements',
                'permissions' => [
                    [
                        'name' => 'employeeMovement.create',
                        'description' => 'Create Employee Movement',
                    ],
                    [
                        'name' => 'employeeMovement.delete',
                        'description' => 'Delete Employee Movement',
                    ],
                    [
                        'name' => 'employeeMovement.destroy',
                        'description' => 'Destroy Employee Movement',
                    ],
                    [
                        'name' => 'employeeMovement.edit',
                        'description' => 'Edit Employee Movement',
                    ],
                    [
                        'name' => 'employeeMovement.index',
                        'description' => 'View Employee Movements',
                    ],
                    [
                        'name' => 'employeeMovement.show',
                        'description' => 'Show Employee Movement Details',
                    ],
                    [
                        'name' => 'employeeMovement.store',
                        'description' => 'Create Employee Movement',
                    ],
                    [
                        'name' => 'employeeMovement.undoChanges',
                        'description' => 'Undo Employee Movement Changes',
                    ],
                    [
                        'name' => 'employeeMovement.update',
                        'description' => 'Update Employee Movement',
                    ],
                ],
            ],
            [
                'permission_group' => 'employeeMovementImport',
                'group_description' => 'Employee Movement Import',
                'permissions' => [
                    [
                        'name' => 'employeeMovementImport',
                        'description' => 'Import Employee Movements',
                    ],
                ],
            ],
            [
                'permission_group' => 'employeeSection',
                'group_description' => 'Employee Sections',
                'permissions' => [
                    [
                        'name' => 'employeeSection.create',
                        'description' => 'Create Employee Section',
                    ],
                    [
                        'name' => 'employeeSection.destroy',
                        'description' => 'Destroy Employee Section',
                    ],
                    [
                        'name' => 'employeeSection.edit',
                        'description' => 'Edit Employee Section',
                    ],
                    [
                        'name' => 'employeeSection.index',
                        'description' => 'View Employee Sections',
                    ],
                    [
                        'name' => 'employeeSection.show',
                        'description' => 'Show Employee Section Details',
                    ],
                    [
                        'name' => 'employeeSection.store',
                        'description' => 'Create Employee Section',
                    ],
                    [
                        'name' => 'employeeSection.update',
                        'description' => 'Update Employee Section',
                    ],
                ],
            ],
            [
                'permission_group' => 'employeeTrainingReport',
                'group_description' => 'Employee Training Report',
                'permissions' => [
                    [
                        'name' => 'employeeTrainingReport.employeeTrainingReport',
                        'description' => 'View Employee Training Report',
                    ],
                ],
            ],
            [
                'permission_group' => 'export',
                'group_description' => 'Export',
                'permissions' => [
                    [
                        'name' => 'export',
                        'description' => 'Export Data',
                    ],
                ],
            ],
            [
                'permission_group' => 'front',
                'group_description' => 'Front Settings',
                'permissions' => [
                    [
                        'name' => 'front.setting',
                        'description' => 'View Front Settings',
                    ],
                    [
                        'name' => 'front.setting.submit',
                        'description' => 'Submit Front Settings',
                    ],
                ],
            ],
            [
                'permission_group' => 'geneMgtPayroll',
                'group_description' => 'Gene Management Payroll',
                'permissions' => [
                    [
                        'name' => 'geneMgtPayroll',
                        'description' => 'Manage Gene Payroll',
                    ],
                ],
            ],
            [
                'permission_group' => 'generalSettings',
                'group_description' => 'General Settings',
                'permissions' => [
                    [
                        'name' => 'generalSettings.edit',
                        'description' => 'Edit General Settings',
                    ],
                    [
                        'name' => 'generalSettings.index',
                        'description' => 'View General Settings',
                    ],
                    [
                        'name' => 'generalSettings.store',
                        'description' => 'Create General Settings',
                    ],
                    [
                        'name' => 'generalSettings.update',
                        'description' => 'Update General Settings',
                    ],
                ],
            ],
            [
                'permission_group' => 'generate_payroll_request',
                'group_description' => 'Generate Payroll Request',
                'permissions' => [
                    [
                        'name' => 'generate_payroll_request',
                        'description' => 'Create Payroll Request',
                    ],
                ],
            ],
            [
                'permission_group' => 'generate_payroll_request_mgmt',
                'group_description' => 'Manage Generate Payroll Request',
                'permissions' => [
                    [
                        'name' => 'generate_payroll_request_mgmt',
                        'description' => 'Manage Payroll Requests',
                    ],
                ],
            ],
            [
                'permission_group' => 'generateBonus',
                'group_description' => 'Generate Bonus',
                'permissions' => [
                    [
                        'name' => 'generateBonus.create',
                        'description' => 'Create Bonus',
                    ],
                    [
                        'name' => 'generateBonus.filter',
                        'description' => 'Filter Bonus',
                    ],
                    [
                        'name' => 'generateBonus.index',
                        'description' => 'View Bonuses',
                    ],
                ],
            ],
            [
                'permission_group' => 'generatePayrollExcel',
                'group_description' => 'Generate Payroll Excel',
                'permissions' => [
                    [
                        'name' => 'generatePayrollExcel',
                        'description' => 'Generate Payroll Excel Report',
                    ],
                ],
            ],
            [
                'permission_group' => 'generatePayslip',
                'group_description' => 'Generate Payslip',
                'permissions' => [
                    [
                        'name' => 'generatePayslip',
                        'description' => 'Generate Payslips',
                    ],
                    [
                        'name' => 'generatePayslip.self',
                        'description' => 'Generate Your Payslip',
                    ],
                ],
            ],
            [
                'permission_group' => 'generateReport',
                'group_description' => 'Generate Report',
                'permissions' => [
                    [
                        'name' => 'generateReport.generateReport',
                        'description' => 'Generate Reports',
                    ],
                ],
            ],
            [
                'permission_group' => 'generateSalary',
                'group_description' => 'Generate Salary',
                'permissions' => [
                    [
                        'name' => 'generateSalary.massGenerate',
                        'description' => 'Mass Generate Salaries',
                    ],
                ],
            ],
            [
                'permission_group' => 'generateSalarySheet',
                'group_description' => 'Generate Salary Sheet',
                'permissions' => [
                    [
                        'name' => 'generateSalarySheet.calculateEmployeeSalary',
                        'description' => 'Calculate Employee Salary',
                    ],
                    [
                        'name' => 'generateSalarySheet.create',
                        'description' => 'Create Salary Sheet',
                    ],
                    [
                        'name' => 'generateSalarySheet.index',
                        'description' => 'View Salary Sheets',
                    ],
                    [
                        'name' => 'generateSalarySheet.monthSalary',
                        'description' => 'Generate Monthly Salary Sheet',
                    ],
                ],
            ],
            [
                'permission_group' => 'holiday',
                'group_description' => 'Holiday Management',
                'permissions' => [
                    [
                        'name' => 'holiday.create',
                        'description' => 'Create Holiday',
                    ],
                    [
                        'name' => 'holiday.delete',
                        'description' => 'Delete Holiday',
                    ],
                    [
                        'name' => 'holiday.edit',
                        'description' => 'Edit Holiday',
                    ],
                    [
                        'name' => 'holiday.index',
                        'description' => 'View Holidays',
                    ],
                    [
                        'name' => 'holiday.store',
                        'description' => 'Create Holiday',
                    ],
                    [
                        'name' => 'holiday.update',
                        'description' => 'Update Holiday',
                    ],
                ],
            ],
            [
                'permission_group' => 'hourlyWages',
                'group_description' => 'Hourly Wages',
                'permissions' => [
                    [
                        'name' => 'hourlyWages.create',
                        'description' => 'Create Hourly Wages',
                    ],
                    [
                        'name' => 'hourlyWages.destroy',
                        'description' => 'Destroy Hourly Wages',
                    ],
                    [
                        'name' => 'hourlyWages.edit',
                        'description' => 'Edit Hourly Wages',
                    ],
                    [
                        'name' => 'hourlyWages.index',
                        'description' => 'View Hourly Wages',
                    ],
                    [
                        'name' => 'hourlyWages.show',
                        'description' => 'Show Hourly Wage Details',
                    ],
                    [
                        'name' => 'hourlyWages.store',
                        'description' => 'Create Hourly Wages',
                    ],
                    [
                        'name' => 'hourlyWages.update',
                        'description' => 'Update Hourly Wages',
                    ],
                ],
            ],
            [
                'permission_group' => 'importUsers',
                'group_description' => 'Import Users',
                'permissions' => [
                    [
                        'name' => 'importUsers',
                        'description' => 'Import Users',
                    ],
                ],
            ],
            [
                'permission_group' => 'invalidLicense',
                'group_description' => 'Invalid License',
                'permissions' => [
                    [
                        'name' => 'invalidLicense',
                        'description' => 'View Invalid License',
                    ],
                ],
            ],
            [
                'permission_group' => 'ip',
                'group_description' => 'IP Settings',
                'permissions' => [
                    [
                        'name' => 'ip.attendance',
                        'description' => 'View IP Attendance',
                    ],
                ],
            ],
            [
                'permission_group' => 'job_category',
                'group_description' => 'Job Category',
                'permissions' => [
                    [
                        'name' => 'job_category.create',
                        'description' => 'Create Job Category',
                    ],
                    [
                        'name' => 'job_category.destroy',
                        'description' => 'Destroy Job Category',
                    ],
                    [
                        'name' => 'job_category.edit',
                        'description' => 'Edit Job Category',
                    ],
                    [
                        'name' => 'job_category.index',
                        'description' => 'View Job Categories',
                    ],
                    [
                        'name' => 'job_category.show',
                        'description' => 'Show Job Category Details',
                    ],
                    [
                        'name' => 'job_category.store',
                        'description' => 'Create Job Category',
                    ],
                    [
                        'name' => 'job_category.update',
                        'description' => 'Update Job Category',
                    ],
                ],
            ],
            [
                'permission_group' => 'job',
                'group_description' => 'Job Management',
                'permissions' => [
                    [
                        'name' => 'job.application',
                        'description' => 'Manage Job Applications',
                    ],
                    [
                        'name' => 'job.details',
                        'description' => 'View Job Details',
                    ],
                    [
                        'name' => 'job.internal_details',
                        'description' => 'View Internal Job Details',
                    ],
                ],
            ],
            [
                'permission_group' => 'jobCandidate',
                'group_description' => 'Job Candidates',
                'permissions' => [
                    [
                        'name' => 'jobCandidate.applyCandidateList',
                        'description' => 'View Applied Candidates',
                    ],
                    [
                        'name' => 'jobCandidate.index',
                        'description' => 'View Job Candidates',
                    ],
                    [
                        'name' => 'jobCandidate.jobHireList',
                        'description' => 'View Job Hire List',
                    ],
                    [
                        'name' => 'jobCandidate.jobInterviewList',
                        'description' => 'View Job Interview List',
                    ],
                    [
                        'name' => 'jobCandidate.rejectedApplicant',
                        'description' => 'View Rejected Applicants',
                    ],
                    [
                        'name' => 'jobCandidate.shortListedApplicant',
                        'description' => 'View Shortlisted Applicants',
                    ],
                ],
            ],
            [
                'permission_group' => 'jobCategory',
                'group_description' => 'Job Category Management',
                'permissions' => [
                    [
                        'name' => 'jobCategory.import',
                        'description' => 'Import Job Categories',
                    ],
                    [
                        'name' => 'jobCategory.importView',
                        'description' => 'View Job Category Import',
                    ],
                ],
            ],
            [
                'permission_group' => 'jobGroups',
                'group_description' => 'Job Groups',
                'permissions' => [
                    [
                        'name' => 'jobGroups.create',
                        'description' => 'Create Job Group',
                    ],
                    [
                        'name' => 'jobGroups.destroy',
                        'description' => 'Destroy Job Group',
                    ],
                    [
                        'name' => 'jobGroups.edit',
                        'description' => 'Edit Job Group',
                    ],
                    [
                        'name' => 'jobGroups.index',
                        'description' => 'View Job Groups',
                    ],
                    [
                        'name' => 'jobGroups.show',
                        'description' => 'Show Job Group Details',
                    ],
                    [
                        'name' => 'jobGroups.store',
                        'description' => 'Create Job Group',
                    ],
                    [
                        'name' => 'jobGroups.update',
                        'description' => 'Update Job Group',
                    ],
                ],
            ],
            [
                'permission_group' => 'jobPost',
                'group_description' => 'Job Postings',
                'permissions' => [
                    [
                        'name' => 'jobPost.create',
                        'description' => 'Create Job Post',
                    ],
                    [
                        'name' => 'jobPost.delete',
                        'description' => 'Delete Job Post',
                    ],
                    [
                        'name' => 'jobPost.edit',
                        'description' => 'Edit Job Post',
                    ],
                    [
                        'name' => 'jobPost.index',
                        'description' => 'View Job Posts',
                    ],
                    [
                        'name' => 'jobPost.show',
                        'description' => 'Show Job Post Details',
                    ],
                    [
                        'name' => 'jobPost.store',
                        'description' => 'Create Job Post',
                    ],
                    [
                        'name' => 'jobPost.update',
                        'description' => 'Update Job Post',
                    ],
                ],
            ],
            [
                'permission_group' => 'leaveApplication',
                'group_description' => 'Leave Applications',
                'permissions' => [
                    [
                        'name' => 'leaveApplication.delete',
                        'description' => 'Delete Leave Application',
                    ],
                ],
            ],
            [
                'permission_group' => 'leaveManagement',
                'group_description' => 'Leave Management',
                'permissions' => [
                    [
                        'name' => 'leaveManagement.manualUpload',
                        'description' => 'Upload Leave Data Manually',
                    ],
                    [
                        'name' => 'leaveManagement.manualUploadSave',
                        'description' => 'Create Manual Leave Data Upload',
                    ],
                    [
                        'name' => 'leaveManagement.manualUploadView',
                        'description' => 'View Manual Leave Data Upload',
                    ],
                ],
            ],
            [
                'permission_group' => 'leaveReport',
                'group_description' => 'Leave Reports',
                'permissions' => [
                    [
                        'name' => 'leaveReport.fullOrganizationReport',
                        'description' => 'Generate Full Organization Leave Report',
                    ],
                    [
                        'name' => 'leaveReport.leaveReport',
                        'description' => 'Generate Leave Report',
                    ],
                ],
            ],
            [
                'permission_group' => 'leaveType',
                'group_description' => 'Leave Types',
                'permissions' => [
                    [
                        'name' => 'leaveType.create',
                        'description' => 'Create Leave Type',
                    ],
                    [
                        'name' => 'leaveType.delete',
                        'description' => 'Delete Leave Type',
                    ],
                    [
                        'name' => 'leaveType.edit',
                        'description' => 'Edit Leave Type',
                    ],
                    [
                        'name' => 'leaveType.index',
                        'description' => 'View Leave Types',
                    ],
                    [
                        'name' => 'leaveType.store',
                        'description' => 'Create Leave Type',
                    ],
                    [
                        'name' => 'leaveType.update',
                        'description' => 'Update Leave Type',
                    ],
                ],
            ],
            [
                'permission_group' => 'licenses',
                'group_description' => 'Licenses',
                'permissions' => [
                    [
                        'name' => 'licenses',
                        'description' => 'Manage Licenses',
                    ],
                ],
            ],
            [
                'permission_group' => 'login',
                'group_description' => 'Login',
                'permissions' => [
                    [
                        'name' => 'login',
                        'description' => 'Login to the System',
                    ],
                ],
            ],
            [
                'permission_group' => 'managementPay',
                'group_description' => 'Management Pay',
                'permissions' => [
                    [
                        'name' => 'managementPay.index',
                        'description' => 'View Management Pay',
                    ],
                ],
            ],
            [
                'permission_group' => 'managementPayrollDataExport',
                'group_description' => 'Management Payroll Data Export',
                'permissions' => [
                    [
                        'name' => 'managementPayrollDataExport',
                        'description' => 'Export Management Payroll Data',
                    ],
                ],
            ],
            [
                'permission_group' => 'manualAttendance',
                'group_description' => 'Manual Attendance',
                'permissions' => [
                    [
                        'name' => 'manualAttendance.filter',
                        'description' => 'Filter Manual Attendance',
                    ],
                    [
                        'name' => 'manualAttendance.manualAttendance',
                        'description' => 'Record Manual Attendance',
                    ],
                    [
                        'name' => 'manualAttendance.store',
                        'description' => 'Create Manual Attendance',
                    ],
                ],
            ],
            [
                'permission_group' => 'migrateAttendanceData',
                'group_description' => 'Migrate Attendance Data',
                'permissions' => [
                    [
                        'name' => 'migrateAttendanceData',
                        'description' => 'Migrate Attendance Data',
                    ],
                ],
            ],
            [
                'permission_group' => 'monthlyAttendance',
                'group_description' => 'Monthly Attendance',
                'permissions' => [
                    [
                        'name' => 'monthlyAttendance.monthlyAttendance',
                        'description' => 'View Monthly Attendance',
                    ],
                    [
                        'name' => 'monthlyAttendance.monthlyAttendanceFilter',
                        'description' => 'Filter Monthly Attendance',
                    ],
                ],
            ],
            [
                'permission_group' => 'myAttendanceReport',
                'group_description' => 'My Attendance Report',
                'permissions' => [
                    [
                        'name' => 'myAttendanceReport.myAttendanceReport',
                        'description' => 'View My Attendance Report',
                    ],
                    [
                        'name' => 'myAttendanceReport.myAttendanceReportFilter',
                        'description' => 'Filter My Attendance Report',
                    ],
                ],
            ],
            [
                'permission_group' => 'myLeaveReport',
                'group_description' => 'My Leave Report',
                'permissions' => [
                    [
                        'name' => 'myLeaveReport.myLeaveReport',
                        'description' => 'View My Leave Report',
                    ],
                ],
            ],
            [
                'permission_group' => 'myPayroll',
                'group_description' => 'My Payroll',
                'permissions' => [
                    [
                        'name' => 'myPayroll.myPayroll',
                        'description' => 'View My Payroll',
                    ],
                ],
            ],
            [
                'permission_group' => 'newAttendance',
                'group_description' => 'New Attendance',
                'permissions' => [
                    [
                        'name' => 'newAttendance.filter',
                        'description' => 'Filter New Attendance',
                    ],
                    [
                        'name' => 'newAttendance.store',
                        'description' => 'Create New Attendance',
                    ],
                    [
                        'name' => 'newAttendanceIndex',
                        'description' => 'View New Attendance Index',
                    ],
                ],
            ],
            [
                'permission_group' => 'newManagementSalaryCalculate',
                'group_description' => 'New Management Salary Calculation',
                'permissions' => [
                    [
                        'name' => 'newManagementSalaryCalculate',
                        'description' => 'Calculate New Management Salary',
                    ],
                ],
            ],
            [
                'permission_group' => 'newMonthlyAttendance',
                'group_description' => 'New Monthly Attendance',
                'permissions' => [
                    [
                        'name' => 'newMonthlyAttendance.monthlyAttendance',
                        'description' => 'View New Monthly Attendance',
                    ],
                ],
            ],
            [
                'permission_group' => 'newSalaryCalculate',
                'group_description' => 'New Salary Calculation',
                'permissions' => [
                    [
                        'name' => 'newSalaryCalculate',
                        'description' => 'Calculate New Salary',
                    ],
                ],
            ],
            [
                'permission_group' => 'nhif',
                'group_description' => 'NHIF',
                'permissions' => [
                    [
                        'name' => 'nhif.create',
                        'description' => 'Create NHIF Record',
                    ],
                    [
                        'name' => 'nhif.destroy',
                        'description' => 'Destroy NHIF Record',
                    ],
                    [
                        'name' => 'nhif.edit',
                        'description' => 'Edit NHIF Record',
                    ],
                    [
                        'name' => 'nhif.index',
                        'description' => 'View NHIF Records',
                    ],
                    [
                        'name' => 'nhif.show',
                        'description' => 'Show NHIF Record Details',
                    ],
                    [
                        'name' => 'nhif.store',
                        'description' => 'Create NHIF Record',
                    ],
                    [
                        'name' => 'nhif.update',
                        'description' => 'Update NHIF Record',
                    ],
                ],
            ],
            [
                'permission_group' => 'nhifReportsIndex',
                'group_description' => 'NHIF Reports',
                'permissions' => [
                    [
                        'name' => 'nhifReportsIndex',
                        'description' => 'View NHIF Reports',
                    ],
                ],
            ],
            [
                'permission_group' => 'notice',
                'group_description' => 'Notices',
                'permissions' => [
                    [
                        'name' => 'notice.create',
                        'description' => 'Create Notice',
                    ],
                    [
                        'name' => 'notice.delete',
                        'description' => 'Delete Notice',
                    ],
                    [
                        'name' => 'notice.edit',
                        'description' => 'Edit Notice',
                    ],
                    [
                        'name' => 'notice.index',
                        'description' => 'View Notices',
                    ],
                    [
                        'name' => 'notice.show',
                        'description' => 'Show Notice Details',
                    ],
                    [
                        'name' => 'notice.store',
                        'description' => 'Create Notice',
                    ],
                    [
                        'name' => 'notice.update',
                        'description' => 'Update Notice',
                    ],
                ],
            ],
            [
                'permission_group' => 'nssfReportsIndex',
                'group_description' => 'NSSF Reports',
                'permissions' => [
                    [
                        'name' => 'nssfReportsIndex',
                        'description' => 'View NSSF Reports',
                    ],
                ],
            ],
            [
                'permission_group' => 'payGrade',
                'group_description' => 'Pay Grade',
                'permissions' => [
                    [
                        'name' => 'payGrade.create',
                        'description' => 'Create Pay Grade',
                    ],
                    [
                        'name' => 'payGrade.destroy',
                        'description' => 'Destroy Pay Grade',
                    ],
                    [
                        'name' => 'payGrade.edit',
                        'description' => 'Edit Pay Grade',
                    ],
                    [
                        'name' => 'payGrade.index',
                        'description' => 'View Pay Grades',
                    ],
                    [
                        'name' => 'payGrade.show',
                        'description' => 'Show Pay Grade Details',
                    ],
                    [
                        'name' => 'payGrade.store',
                        'description' => 'Create Pay Grade',
                    ],
                    [
                        'name' => 'payGrade.update',
                        'description' => 'Update Pay Grade',
                    ],
                ],
            ],
            [
                'permission_group' => 'paygroup',
                'group_description' => 'Pay Group',
                'permissions' => [
                    [
                        'name' => 'paygroup.create',
                        'description' => 'Create Pay Group',
                    ],
                    [
                        'name' => 'paygroup.destroy',
                        'description' => 'Destroy Pay Group',
                    ],
                    [
                        'name' => 'paygroup.edit',
                        'description' => 'Edit Pay Group',
                    ],
                    [
                        'name' => 'paygroup.index',
                        'description' => 'View Pay Groups',
                    ],
                    [
                        'name' => 'paygroup.show',
                        'description' => 'Show Pay Group Details',
                    ],
                    [
                        'name' => 'paygroup.store',
                        'description' => 'Create Pay Group',
                    ],
                    [
                        'name' => 'paygroup.update',
                        'description' => 'Update Pay Group',
                    ],
                ],
            ],
            [
                'permission_group' => 'paymentHistory',
                'group_description' => 'Payment History',
                'permissions' => [
                    [
                        'name' => 'paymentHistory.paymentHistory',
                        'description' => 'View Payment History',
                    ],
                ],
            ],
            [
                'permission_group' => 'payoutChannel',
                'group_description' => 'Payout Channel',
                'permissions' => [
                    [
                        'name' => 'payoutChannel.create',
                        'description' => 'Create Payout Channel',
                    ],
                    [
                        'name' => 'payoutChannel.delete',
                        'description' => 'Delete Payout Channel',
                    ],
                    [
                        'name' => 'payoutChannel.deleteFromStaff',
                        'description' => 'Delete Payout Channel from Staff',
                    ],
                    [
                        'name' => 'payoutChannel.edit',
                        'description' => 'Edit Payout Channel',
                    ],
                    [
                        'name' => 'payoutChannel.index',
                        'description' => 'View Payout Channels',
                    ],
                    [
                        'name' => 'payoutChannel.show',
                        'description' => 'Show Payout Channel Details',
                    ],
                    [
                        'name' => 'payoutChannel.store',
                        'description' => 'Create Payout Channel',
                    ],
                    [
                        'name' => 'payoutChannel.update',
                        'description' => 'Update Payout Channel',
                    ],
                    [
                        'name' => 'payoutChannel.updateStaff',
                        'description' => 'Update Payout Channel for Staff',
                    ],
                ],
            ],
            [
                'permission_group' => 'payroll',
                'group_description' => 'Payroll',
                'permissions' => [
                    [
                        'name' => 'payroll.view',
                        'description' => 'View Payroll',
                    ],
                ],
            ],
            [
                'permission_group' => 'payroll9',
                'group_description' => 'Payroll 9',
                'permissions' => [
                    [
                        'name' => 'payroll9.create',
                        'description' => 'Create Payroll 9',
                    ],
                    [
                        'name' => 'payroll9.destroy',
                        'description' => 'Destroy Payroll 9',
                    ],
                    [
                        'name' => 'payroll9.edit',
                        'description' => 'Edit Payroll 9',
                    ],
                    [
                        'name' => 'payroll9.generate',
                        'description' => 'Generate Payroll 9',
                    ],
                    [
                        'name' => 'payroll9.index',
                        'description' => 'View Payroll 9',
                    ],
                    [
                        'name' => 'payroll9.massMail',
                        'description' => 'Mass Mail Payroll 9',
                    ],
                    [
                        'name' => 'payroll9.preview',
                        'description' => 'Preview Payroll 9',
                    ],
                    [
                        'name' => 'payroll9.preview1',
                        'description' => 'Preview Payroll 9 (1)',
                    ],
                    [
                        'name' => 'payroll9.preview2',
                        'description' => 'Preview Payroll 9 (2)',
                    ],
                    [
                        'name' => 'payroll9.show',
                        'description' => 'Show Payroll 9 Details',
                    ],
                    [
                        'name' => 'payroll9.store',
                        'description' => 'Create Payroll 9',
                    ],
                    [
                        'name' => 'payroll9.update',
                        'description' => 'Update Payroll 9',
                    ],
                ],
            ],
            [
                'permission_group' => 'payrollcaculator',
                'group_description' => 'Payroll Calculator',
                'permissions' => [
                    [
                        'name' => 'payrollcaculator_ahl',
                        'description' => 'Calculate AHL Payroll',
                    ],
                    [
                        'name' => 'payrollcaculator_gross',
                        'description' => 'Calculate Gross Payroll',
                    ],
                    [
                        'name' => 'payrollcaculator_index',
                        'description' => 'Index Payroll Calculator',
                    ],
                    [
                        'name' => 'payrollcaculator_insurance_relief',
                        'description' => 'Calculate Insurance Relief',
                    ],
                    [
                        'name' => 'payrollcaculator_net_pay',
                        'description' => 'Calculate Net Pay',
                    ],
                    [
                        'name' => 'payrollcaculator_nhif',
                        'description' => 'Calculate NHIF Payroll',
                    ],
                    [
                        'name' => 'payrollcaculator_nssf',
                        'description' => 'Calculate NSSF Payroll',
                    ],
                    [
                        'name' => 'payrollcaculator_paye',
                        'description' => 'Calculate PAYE',
                    ],
                    [
                        'name' => 'payrollcaculator_personal_relief',
                        'description' => 'Calculate Personal Relief',
                    ],
                    [
                        'name' => 'payrollcaculator_taxable_pay',
                        'description' => 'Calculate Taxable Pay',
                    ],
                    [
                        'name' => 'payrollcaculator.ahl',
                        'description' => 'Calculate AHL Payroll (Advanced)',
                    ],
                    [
                        'name' => 'payrollcaculator.gross',
                        'description' => 'Calculate Gross Payroll (Advanced)',
                    ],
                    [
                        'name' => 'payrollcaculator.index',
                        'description' => 'Index Payroll Calculator (Advanced)',
                    ],
                    [
                        'name' => 'payrollcaculator.insurance_relief',
                        'description' => 'Calculate Insurance Relief (Advanced)',
                    ],
                    [
                        'name' => 'payrollcaculator.net_pay',
                        'description' => 'Calculate Net Pay (Advanced)',
                    ],
                    [
                        'name' => 'payrollcaculator.nhif',
                        'description' => 'Calculate NHIF Payroll (Advanced)',
                    ],
                    [
                        'name' => 'payrollcaculator.nssf',
                        'description' => 'Calculate NSSF Payroll (Advanced)',
                    ],
                    [
                        'name' => 'payrollcaculator.paye',
                        'description' => 'Calculate PAYE (Advanced)',
                    ],
                    [
                        'name' => 'payrollcaculator.personal_relief',
                        'description' => 'Calculate Personal Relief (Advanced)',
                    ],
                    [
                        'name' => 'payrollcaculator.taxable_pay',
                        'description' => 'Calculate Taxable Pay (Advanced)',
                    ],
                ],
            ],
            [
                'permission_group' => 'payrollDataExport',
                'group_description' => 'Payroll Data Export',
                'permissions' => [
                    [
                        'name' => 'payrollDataExport',
                        'description' => 'Export Payroll Data',
                    ],
                ],
            ],
            [
                'permission_group' => 'payrollIndex',
                'group_description' => 'Payroll Index',
                'permissions' => [
                    [
                        'name' => 'payrollIndex',
                        'description' => 'View Payroll Index',
                    ],
                ],
            ],
            [
                'permission_group' => 'pendingLeaveRequests',
                'group_description' => 'Pending Leave Requests',
                'permissions' => [
                    [
                        'name' => 'pendingLeaveRequests.pendingLeaveRequests',
                        'description' => 'View Pending Leave Requests',
                    ],
                ],
            ],
            [
                'permission_group' => 'permanent',
                'group_description' => 'Permanent',
                'permissions' => [
                    [
                        'name' => 'permanent.index',
                        'description' => 'View Permanent Records',
                    ],
                    [
                        'name' => 'permanent.updatePermanent',
                        'description' => 'Update Permanent Record',
                    ],
                ],
            ],
            [
                'permission_group' => 'permissions',
                'group_description' => 'Permissions',
                'permissions' => [
                    [
                        'name' => 'permissions.create',
                        'description' => 'Create Permission',
                    ],
                    [
                        'name' => 'permissions.destroy',
                        'description' => 'Destroy Permission',
                    ],
                    [
                        'name' => 'permissions.edit',
                        'description' => 'Edit Permission',
                    ],
                    [
                        'name' => 'permissions.index',
                        'description' => 'View Permissions',
                    ],
                    [
                        'name' => 'permissions.show',
                        'description' => 'Show Permission Details',
                    ],
                    [
                        'name' => 'permissions.store',
                        'description' => 'Create Permission',
                    ],
                    [
                        'name' => 'permissions.update',
                        'description' => 'Update Permission',
                    ],
                ],
            ],
            [
                'permission_group' => 'printHeadSettings',
                'group_description' => 'Print Head Settings',
                'permissions' => [
                    [
                        'name' => 'printHeadSettings.store',
                        'description' => 'Store Print Head Settings',
                    ],
                    [
                        'name' => 'printHeadSettings.update',
                        'description' => 'Update Print Head Settings',
                    ],
                ],
            ],
            [
                'permission_group' => 'approvalSettings',
                'group_description' => 'Approval Settings',
                'permissions' => [
                    [
                        'name' => 'approvalSettings.edit',
                        'description' => 'Edit Approval Settings',
                    ],
                    [
                        'name' => 'approvalSettings.index',
                        'description' => 'View Approval Settings',
                    ],
                    [
                        'name' => 'approvalSettings.store',
                        'description' => 'Create Approval Settings',
                    ],
                    [
                        'name' => 'approvalSettings.update',
                        'description' => 'Update Approval Settings',
                    ],
                ],
            ],
            [
                'permission_group' => 'promotion',
                'group_description' => 'Promotion',
                'permissions' => [
                    [
                        'name' => 'promotion.create',
                        'description' => 'Create Promotion',
                    ],
                    [
                        'name' => 'promotion.delete',
                        'description' => 'Delete Promotion',
                    ],
                    [
                        'name' => 'promotion.edit',
                        'description' => 'Edit Promotion',
                    ],
                    [
                        'name' => 'promotion.index',
                        'description' => 'View Promotions',
                    ],
                    [
                        'name' => 'promotion.store',
                        'description' => 'Create Promotion',
                    ],
                    [
                        'name' => 'promotion.update',
                        'description' => 'Update Promotion',
                    ],
                ],
            ],
            [
                'permission_group' => 'publicHoliday',
                'group_description' => 'Public Holiday',
                'permissions' => [
                    [
                        'name' => 'publicHoliday.create',
                        'description' => 'Create Public Holiday',
                    ],
                    [
                        'name' => 'publicHoliday.delete',
                        'description' => 'Delete Public Holiday',
                    ],
                    [
                        'name' => 'publicHoliday.edit',
                        'description' => 'Edit Public Holiday',
                    ],
                    [
                        'name' => 'publicHoliday.index',
                        'description' => 'View Public Holidays',
                    ],
                    [
                        'name' => 'publicHoliday.store',
                        'description' => 'Create Public Holiday',
                    ],
                    [
                        'name' => 'publicHoliday.update',
                        'description' => 'Update Public Holiday',
                    ],
                ],
            ],
            [
                'permission_group' => 'reports',
                'group_description' => 'Reports',
                'permissions' => [
                    [
                        'name' => 'reports.activity_logs',
                        'description' => 'View Activity Logs',
                    ],
                    [
                        'name' => 'reports.activity_logs.view',
                        'description' => 'View Detailed Activity Logs',
                    ],
                    [
                        'name' => 'reports.errorLog',
                        'description' => 'View Error Logs',
                    ],
                    [
                        'name' => 'reports.test',
                        'description' => 'View Test Reports',
                    ],
                ],
            ],
            [
                'permission_group' => 'requestedApplication',
                'group_description' => 'Requested Applications',
                'permissions' => [
                    [
                        'name' => 'requestedApplication.index',
                        'description' => 'View Requested Applications',
                    ],
                    [
                        'name' => 'requestedApplication.update',
                        'description' => 'Update Requested Application',
                    ],
                    [
                        'name' => 'requestedApplication.viewDetails',
                        'description' => 'View Requested Application Details',
                    ],
                ],
            ],
            [
                'permission_group' => 'resetPassword',
                'group_description' => 'Reset Password',
                'permissions' => [
                    [
                        'name' => 'reset_password_with_token',
                        'description' => 'Reset Password with Token',
                    ],
                    [
                        'name' => 'reset_password_without_token',
                        'description' => 'Reset Password without Token',
                    ],
                    [
                        'name' => 'resetPassword',
                        'description' => 'General Password Reset',
                    ],
                ],
            ],
            [
                'permission_group' => 'rolePermission',
                'group_description' => 'Role Permissions',
                'permissions' => [
                    [
                        'name' => 'rolePermission.create',
                        'description' => 'Create Role Permission',
                    ],
                    [
                        'name' => 'rolePermission.destroy',
                        'description' => 'Destroy Role Permission',
                    ],
                    [
                        'name' => 'rolePermission.edit',
                        'description' => 'Edit Role Permission',
                    ],
                    [
                        'name' => 'rolePermission.index',
                        'description' => 'View Role Permissions',
                    ],
                    [
                        'name' => 'rolePermission.show',
                        'description' => 'Show Role Permission Details',
                    ],
                    [
                        'name' => 'rolePermission.store',
                        'description' => 'Create Role Permission',
                    ],
                    [
                        'name' => 'rolePermission.update',
                        'description' => 'Update Role Permission',
                    ],
                ],
            ],
            [
                'permission_group' => 'roles',
                'group_description' => 'Roles',
                'permissions' => [
                    [
                        'name' => 'roles.create',
                        'description' => 'Create Role',
                    ],
                    [
                        'name' => 'roles.destroy',
                        'description' => 'Destroy Role',
                    ],
                    [
                        'name' => 'roles.edit',
                        'description' => 'Edit Role',
                    ],
                    [
                        'name' => 'roles.index',
                        'description' => 'View Roles',
                    ],
                    [
                        'name' => 'roles.show',
                        'description' => 'Show Role Details',
                    ],
                    [
                        'name' => 'roles.store',
                        'description' => 'Create Role',
                    ],
                    [
                        'name' => 'roles.update',
                        'description' => 'Update Role',
                    ],
                ],
            ],
            [
                'permission_group' => 'rolloverLeave',
                'group_description' => 'Rollover Leave',
                'permissions' => [
                    [
                        'name' => 'rolloverLeave.delete',
                        'description' => 'Delete Rollover Leave',
                    ],
                    [
                        'name' => 'rolloverLeaveEdit',
                        'description' => 'Edit Rollover Leave',
                    ],
                    [
                        'name' => 'rolloverLeaves',
                        'description' => 'View Rollover Leaves',
                    ],
                ],
            ],
            [
                'permission_group' => 'salaryDeductionRule',
                'group_description' => 'Salary Deduction Rule',
                'permissions' => [
                    [
                        'name' => 'salaryDeductionRule.index',
                        'description' => 'View Salary Deduction Rules',
                    ],
                ],
            ],
            [
                'permission_group' => 'saveEmployeeBonus',
                'group_description' => 'Create Employee Bonus',
                'permissions' => [
                    [
                        'name' => 'saveEmployeeBonus.store',
                        'description' => 'Create Employee Bonus',
                    ],
                ],
            ],
            [
                'permission_group' => 'saveEmployeeSalaryDetails',
                'group_description' => 'Create Employee Salary Details',
                'permissions' => [
                    [
                        'name' => 'saveEmployeeSalaryDetails.store',
                        'description' => 'Create Employee Salary Details',
                    ],
                ],
            ],
            [
                'permission_group' => 'saveMigrateAttendanceData',
                'group_description' => 'Create Migrate Attendance Data',
                'permissions' => [
                    [
                        'name' => 'saveMigrateAttendanceData',
                        'description' => 'Create Migrate Attendance Data',
                    ],
                ],
            ],
            [
                'permission_group' => 'service',
                'group_description' => 'Service',
                'permissions' => [
                    [
                        'name' => 'service.create',
                        'description' => 'Create Service',
                    ],
                    [
                        'name' => 'service.destroy',
                        'description' => 'Destroy Service',
                    ],
                    [
                        'name' => 'service.edit',
                        'description' => 'Edit Service',
                    ],
                    [
                        'name' => 'service.index',
                        'description' => 'View Services',
                    ],
                    [
                        'name' => 'service.show',
                        'description' => 'Show Service Details',
                    ],
                    [
                        'name' => 'service.store',
                        'description' => 'Create Service',
                    ],
                    [
                        'name' => 'service.update',
                        'description' => 'Update Service',
                    ],
                ],
            ],
            [
                'permission_group' => 'storeRolloverLeave',
                'group_description' => 'Store Rollover Leave',
                'permissions' => [
                    [
                        'name' => 'storeRolloverLeave',
                        'description' => 'Store Rollover Leave',
                    ],
                ],
            ],
            [
                'permission_group' => 'summaryReport',
                'group_description' => 'Summary Report',
                'permissions' => [
                    [
                        'name' => 'summaryReport.summaryReport',
                        'description' => 'View Summary Report',
                    ],
                ],
            ],
            [
                'permission_group' => 'taxSetup',
                'group_description' => 'Tax Setup',
                'permissions' => [
                    [
                        'name' => 'taxSetup.index',
                        'description' => 'View Tax Setup',
                    ],
                ],
            ],
            [
                'permission_group' => 'termination',
                'group_description' => 'Termination',
                'permissions' => [
                    [
                        'name' => 'termination.create',
                        'description' => 'Create Termination',
                    ],
                    [
                        'name' => 'termination.delete',
                        'description' => 'Delete Termination',
                    ],
                    [
                        'name' => 'termination.edit',
                        'description' => 'Edit Termination',
                    ],
                    [
                        'name' => 'termination.import',
                        'description' => 'Import Termination',
                    ],
                    [
                        'name' => 'termination.importSave',
                        'description' => 'Create Imported Termination',
                    ],
                    [
                        'name' => 'termination.index',
                        'description' => 'View Terminations',
                    ],
                    [
                        'name' => 'termination.show',
                        'description' => 'Show Termination Details',
                    ],
                    [
                        'name' => 'termination.store',
                        'description' => 'Create Termination',
                    ],
                    [
                        'name' => 'termination.update',
                        'description' => 'Update Termination',
                    ],
                ],
            ],
            [
                'permission_group' => 'trainingInfo',
                'group_description' => 'Training Information',
                'permissions' => [
                    [
                        'name' => 'trainingInfo.create',
                        'description' => 'Create Training Info',
                    ],
                    [
                        'name' => 'trainingInfo.delete',
                        'description' => 'Delete Training Info',
                    ],
                    [
                        'name' => 'trainingInfo.edit',
                        'description' => 'Edit Training Info',
                    ],
                    [
                        'name' => 'trainingInfo.index',
                        'description' => 'View Training Information',
                    ],
                    [
                        'name' => 'trainingInfo.show',
                        'description' => 'Show Training Info Details',
                    ],
                    [
                        'name' => 'trainingInfo.store',
                        'description' => 'Create Training Info',
                    ],
                    [
                        'name' => 'trainingInfo.update',
                        'description' => 'Update Training Info',
                    ],
                ],
            ],
            [
                'permission_group' => 'trainingType',
                'group_description' => 'Training Type',
                'permissions' => [
                    [
                        'name' => 'trainingType.create',
                        'description' => 'Create Training Type',
                    ],
                    [
                        'name' => 'trainingType.delete',
                        'description' => 'Delete Training Type',
                    ],
                    [
                        'name' => 'trainingType.edit',
                        'description' => 'Edit Training Type',
                    ],
                    [
                        'name' => 'trainingType.index',
                        'description' => 'View Training Types',
                    ],
                    [
                        'name' => 'trainingType.show',
                        'description' => 'Show Training Type Details',
                    ],
                    [
                        'name' => 'trainingType.store',
                        'description' => 'Create Training Type',
                    ],
                    [
                        'name' => 'trainingType.update',
                        'description' => 'Update Training Type',
                    ],
                ],
            ],
            [
                'permission_group' => 'updateDefaultRollovers',
                'group_description' => 'Update Default Rollovers',
                'permissions' => [
                    [
                        'name' => 'updateDefaultRollovers',
                        'description' => 'Update Default Rollovers',
                    ],
                ],
            ],
            [
                'permission_group' => 'updateStatus',
                'group_description' => 'Update Status',
                'permissions' => [
                    [
                        'name' => 'updateStatus',
                        'description' => 'Update Status',
                    ],
                ],
            ],
            [
                'permission_group' => 'user',
                'group_description' => 'User',
                'permissions' => [
                    [
                        'name' => 'user.create',
                        'description' => 'Create User',
                    ],
                    [
                        'name' => 'user.destroy',
                        'description' => 'Destroy User',
                    ],
                    [
                        'name' => 'user.edit',
                        'description' => 'Edit User',
                    ],
                    [
                        'name' => 'user.index',
                        'description' => 'View Users',
                    ],
                    [
                        'name' => 'user.show',
                        'description' => 'Show User Details',
                    ],
                    [
                        'name' => 'user.store',
                        'description' => 'Create User',
                    ],
                    [
                        'name' => 'user.update',
                        'description' => 'Update User',
                    ],
                ],
            ],
            [
                'permission_group' => 'userRole',
                'group_description' => 'User Roles',
                'permissions' => [
                    [
                        'name' => 'userRole.create',
                        'description' => 'Create User Role',
                    ],
                    [
                        'name' => 'userRole.destroy',
                        'description' => 'Destroy User Role',
                    ],
                    [
                        'name' => 'userRole.edit',
                        'description' => 'Edit User Role',
                    ],
                    [
                        'name' => 'userRole.index',
                        'description' => 'View User Roles',
                    ],
                    [
                        'name' => 'userRole.show',
                        'description' => 'Show User Role Details',
                    ],
                    [
                        'name' => 'userRole.store',
                        'description' => 'Create User Role',
                    ],
                    [
                        'name' => 'userRole.update',
                        'description' => 'Update User Role',
                    ],
                ],
            ],
            [
                'permission_group' => 'warning',
                'group_description' => 'Warnings',
                'permissions' => [
                    [
                        'name' => 'warning.create',
                        'description' => 'Create Warning',
                    ],
                    [
                        'name' => 'warning.delete',
                        'description' => 'Delete Warning',
                    ],
                    [
                        'name' => 'warning.edit',
                        'description' => 'Edit Warning',
                    ],
                    [
                        'name' => 'warning.index',
                        'description' => 'View Warnings',
                    ],
                    [
                        'name' => 'warning.show',
                        'description' => 'Show Warning Details',
                    ],
                    [
                        'name' => 'warning.store',
                        'description' => 'Create Warning',
                    ],
                    [
                        'name' => 'warning.update',
                        'description' => 'Update Warning',
                    ],
                ],
            ],
            [
                'permission_group' => 'weeklyAttendance',
                'group_description' => 'Weekly Attendance',
                'permissions' => [
                    [
                        'name' => 'weeklyAttendance.weeklyAttendance',
                        'description' => 'View Weekly Attendance',
                    ],
                    [
                        'name' => 'weeklyAttendance.weeklyAttendanceFilter',
                        'description' => 'Filter Weekly Attendance',
                    ],
                ],
            ],
            [
                'permission_group' => 'weeklyHoliday',
                'group_description' => 'Weekly Holiday',
                'permissions' => [
                    [
                        'name' => 'weeklyHoliday.create',
                        'description' => 'Create Weekly Holiday',
                    ],
                    [
                        'name' => 'weeklyHoliday.delete',
                        'description' => 'Delete Weekly Holiday',
                    ],
                    [
                        'name' => 'weeklyHoliday.edit',
                        'description' => 'Edit Weekly Holiday',
                    ],
                    [
                        'name' => 'weeklyHoliday.index',
                        'description' => 'View Weekly Holidays',
                    ],
                    [
                        'name' => 'weeklyHoliday.store',
                        'description' => 'Create Weekly Holiday',
                    ],
                    [
                        'name' => 'weeklyHoliday.update',
                        'description' => 'Update Weekly Holiday',
                    ],
                ],
            ],
            [
                'permission_group' => 'workHourApproval',
                'group_description' => 'Work Hour Approval',
                'permissions' => [
                    [
                        'name' => 'workHourApproval.create',
                        'description' => 'Create Work Hour Approval',
                    ],
                    [
                        'name' => 'workHourApproval.filter',
                        'description' => 'Filter Work Hour Approval',
                    ],
                    [
                        'name' => 'workHourApproval.store',
                        'description' => 'Create Work Hour Approval',
                    ],
                ],
            ],
            [
                'permission_group' => 'workShift',
                'group_description' => 'Work Shift',
                'permissions' => [
                    [
                        'name' => 'workShift.create',
                        'description' => 'Create Work Shift',
                    ],
                    [
                        'name' => 'workShift.delete',
                        'description' => 'Delete Work Shift',
                    ],
                    [
                        'name' => 'workShift.edit',
                        'description' => 'Edit Work Shift',
                    ],
                    [
                        'name' => 'workShift.index',
                        'description' => 'View Work Shifts',
                    ],
                    [
                        'name' => 'workShift.store',
                        'description' => 'Create Work Shift',
                    ],
                    [
                        'name' => 'workShift.update',
                        'description' => 'Update Work Shift',
                    ],
                ],
            ],

        ];
    }
    public function groupedMenuPermissions()
    {

        return [
            'Administration' => [
                'changePassword',
                'permissions',
                'roles',
                'rolePermission',
                'user',
                'userRole',
                'licenses',
                'invalidLicense',
                'azure',
                'resetPassword',
                'reports',
                'storeRolloverLeave',
                'updateStatus',
                'biometricDevices',
                'importUsers',
                'export',
                'duplictes',
                'service',
                'requestedApplication',
                'printHeadSettings',
                'login'
            ],
            'Employee Management' => [
                'contract',
                'branch',
                'department',
                'designation',
                'job_category',
                'jobCategory',
                'jobGroups',
                'employee',
                'employeeGroup',
                'employeeMovement',
                'employeeMovementImport',
                'employeeSection',
                'saveEmployeeBonus',
                'saveEmployeeSalaryDetails',
                'promotion',
                'termination',
                'managementPay',
                'managementPayrollDataExport',
                'myAttendanceReport',
                'myPayroll',
                'newManagementSalaryCalculate',
                'newMonthlyAttendance',
                'newSalaryCalculate',
                'permanent',
                'downloadStaffReport'
            ],
            'Leave Management' => [
                'leaveApplication',
                'applyForLeave',
                'leaveManagement',
                'leaveType',
                'earnLeaveConfigure',
                'leaveReport',
                'allLeaveApplications',
                'ceoPendingLeaveRequests',
                'pendingLeaveRequests',
                'hlReportIndex',
                'myLeaveReport',
                'holiday',
                'publicHoliday',
                'addRolloverLeave1',
                'rolloverLeave',
            ],
            'Attendance' => [
                'ip',
                'attendance',
                'manualAttendance',
                'dailyAttendance',
                'newAttendance',
                'weeklyAttendance',
                'monthlyAttendance',
                'attendanceSummaryReport',
                'saveMigrateAttendanceData',
                'updateDefaultRollovers',
                'migrateAttendanceData',
                'daily_pay',
                'DailyPay',
                'hourlyWages',
                'weeklyHoliday',
            ],
            'Payroll' => [
                'payroll',
                'payrollIndex',
                'payroll9',
                'generatePayrollExcel',
                'generatePayslip',
                'generateSalary',
                'generateSalarySheet',
                'generatePayrollRequest',
                'generateBonus',
                'salaryDeductionRule',
                'payoutChannel',
                'payGrade',
                'paygroup',
                'paymentHistory',
                'bonusSetting',
                'taxSetup',
                'payrollDataExport',
                'nhif',
                'nssfReportsIndex',
                'geneMgtPayroll',
                'delete_salary_entry',
                'calculatePaye',
                'calculateManagementPay',
                'bonus_types',
                'advances',
                'advance_types',
                'payrollcaculator',
                'generate_payroll_request',
                'generate_payroll_request_mgmt',
                'deduction',
                'nhifReportsIndex',
                'downloadPayslip'
            ],
            'Recruitment' => [
                'applicant',
                'jobCategory',
                'jobCandidate',
                'jobPost',
                'job',
                'allowance',
                'approvals',
            ],
            'Training' => [
                'trainingInfo',
                'trainingType',
                'employeeTrainingReport',
            ],
            'Award' => [
                'award',
                'saveEmployeeBonus',
                'bonuses',
                'bonus_types',
            ],
            'Notice Board' => [
                'notice',
                'zkbiometricGet',
            ],
            'Settings' => [
                'generalSettings',
                'front',
                'company',
                'generateReport',
                'ahlReportIndex',
            ]
        ];
    }
}
