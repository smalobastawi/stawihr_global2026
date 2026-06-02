<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('menus')->truncate();
        DB::table('menus')->insert(
            [

                /**
                 *
                 * @user management
                 *
                 */
                ['parent_id' => 0, 'action' => NULL, 'name' => 'User', 'menu_url' => 'user.index', 'module_id' => '1', 'status' => 1],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Manage Role', 'menu_url' => NULL, 'module_id' => '1', 'status' => '1'],
                ['parent_id' => 2, 'action' => NULL, 'name' => 'Add Role', 'menu_url' => 'userRole.index', 'module_id' => '1', 'status' => '1'],
                ['parent_id' => 2, 'action' => NULL, 'name' => 'Add Role Permission', 'menu_url' => 'rolePermission.index', 'module_id' => '1', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Change Password', 'menu_url' => 'changePassword.index', 'module_id' => '1', 'status' => '1'],

                /**
                 *
                 * @employee management
                 *
                 */

                ['parent_id' => 0, 'action' => NULL, 'name' => 'Department', 'menu_url' => 'department.index', 'module_id' => '2', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Designation', 'menu_url' => 'designation.index', 'module_id' => '2', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Location', 'menu_url' => 'branch.index', 'module_id' => '2', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Manage Employee', 'menu_url' => 'employee.index', 'module_id' => '2', 'status' => '1'],

                /**
                 *
                 * @leave management
                 *
                 */

                ['parent_id' => 0, 'action' => NULL, 'name' => 'Setup', 'menu_url' => null, 'module_id' => '3', 'status' => '1'],
                ['parent_id' => 10, 'action' => NULL, 'name' => 'Manage Holiday', 'menu_url' => 'holiday.index', 'module_id' => '3', 'status' => '1'],
                ['parent_id' => 10, 'action' => NULL, 'name' => 'Public Holiday', 'menu_url' => 'publicHoliday.index', 'module_id' => '3', 'status' => '1'],
                ['parent_id' => 10, 'action' => NULL, 'name' => 'Weekly Holiday', 'menu_url' => 'weeklyHoliday.index', 'module_id' => '3', 'status' => '1'],
                ['parent_id' => 10, 'action' => NULL, 'name' => 'Leave Type', 'menu_url' => 'leaveType.index', 'module_id' => '3', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Leave Application', 'menu_url' => null, 'module_id' => '3', 'status' => '1'],
                ['parent_id' => 15, 'action' => NULL, 'name' => 'Apply for Leave', 'menu_url' => 'applyForLeave.index', 'module_id' => '3', 'status' => '1'],
                ['parent_id' => 15, 'action' => NULL, 'name' => 'Requested Application', 'menu_url' => 'requestedApplication.index', 'module_id' => '3', 'status' => '1'],

                /**
                 *
                 * @attendance management
                 *
                 */

                ['parent_id' => 0, 'action' => NULL, 'name' => 'Setup', 'menu_url' => null, 'module_id' => '4', 'status' => '1'],
                ['parent_id' => 18, 'action' => NULL, 'name' => 'Manage Work Shift', 'menu_url' => 'workShift.index', 'module_id' => '4', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Report', 'menu_url' => null, 'module_id' => '4', 'status' => '1'],
                ['parent_id' => 20, 'action' => NULL, 'name' => 'Daily Attendance', 'menu_url' => 'dailyAttendance.dailyAttendance', 'module_id' => '4', 'status' => '1'],

                ['parent_id' => 0, 'action' => NULL, 'name' => 'Report', 'menu_url' => null, 'module_id' => '3', 'status' => '1'],
                ['parent_id' => 22, 'action' => NULL, 'name' => 'Leave Report', 'menu_url' => 'leaveReport.leaveReport', 'module_id' => '3', 'status' => '1'],
                ['parent_id' => 20, 'action' => NULL, 'name' => 'Monthly Attendance', 'menu_url' => 'monthlyAttendance.monthlyAttendance', 'module_id' => '4', 'status' => '1'],

                /**
                 *
                 * @Payroll management
                 *
                 */

                ['parent_id' => 0, 'action' => NULL, 'name' => 'Setup', 'menu_url' => null, 'module_id' => '5', 'status' => '1'],
                ['parent_id' => 25, 'action' => NULL, 'name' => 'Tax Rule Setup', 'menu_url' => 'taxSetup.index', 'module_id' => '5', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Allowance', 'menu_url' => 'allowance.index', 'module_id' => '5', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Deduction', 'menu_url' => 'deduction.index', 'module_id' => '5', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Monthly Pay Grade', 'menu_url' => 'payGrade.index', 'module_id' => '5', 'status' => '0'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Hourly Pay Grade', 'menu_url' => 'hourlyWages.index', 'module_id' => '5', 'status' => '0'],

                ['parent_id' => 0, 'action' => NULL, 'name' => 'Payroll Home', 'menu_url' => 'payrollIndex', 'module_id' => '5', 'status' => '1'],
                ['parent_id' => 25, 'action' => NULL, 'name' => 'Late Configuration', 'menu_url' => 'salaryDeductionRule.index', 'module_id' => '5', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Report', 'menu_url' => null, 'module_id' => '5', 'status' => '1'],
                ['parent_id' => 33, 'action' => NULL, 'name' => 'Payment History', 'menu_url' => 'paymentHistory.paymentHistory', 'module_id' => '5', 'status' => '1'],
                ['parent_id' => 33, 'action' => NULL, 'name' => 'My Payroll', 'menu_url' => 'myPayroll.myPayroll', 'module_id' => '5', 'status' => '1'],

                /**
                 *
                 * @Recruitment
                 *
                 */

                ['parent_id' => 0, 'action' => NULL, 'name' => 'Job Post', 'menu_url' => 'jobPost.index', 'module_id' => '7', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Job Candidate', 'menu_url' => 'jobCandidate.index', 'module_id' => '7', 'status' => '1'],


                /**
                 *
                 * @leave and attendance management
                 *
                 */

                ['parent_id' => 20, 'action' => NULL, 'name' => 'My Attendance Report', 'menu_url' => 'myAttendanceReport.myAttendanceReport', 'module_id' => '4', 'status' => '1'],
                ['parent_id' => 10, 'action' => NULL, 'name' => 'Earn Leave Configure', 'menu_url' => 'earnLeaveConfigure.index', 'module_id' => '3', 'status' => '1'],

                /**
                 *
                 * @Training
                 *
                 */
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Training Type', 'menu_url' => 'trainingType.index', 'module_id' => '8', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Training List', 'menu_url' => 'trainingInfo.index', 'module_id' => '8', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Training Report', 'menu_url' => 'employeeTrainingReport.employeeTrainingReport', 'module_id' => '8', 'status' => '1'],


                /**
                 *
                 * @Award And Notice Board
                 *
                 */
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Award', 'menu_url' => 'award.index', 'module_id' => '9', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Notice', 'menu_url' => 'notice.index', 'module_id' => '10', 'status' => '1'],
                /**
                 *
                 * @Settings
                 *
                 */
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Settings', 'menu_url' => 'generalSettings.index', 'module_id' => '11', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Manual Attendance', 'menu_url' => 'newAttendanceIndex', 'module_id' => '4', 'status' => '1'],
                ['parent_id' => 22, 'action' => NULL, 'name' => 'Summary Report', 'menu_url' => 'summaryReport.summaryReport', 'module_id' => '3', 'status' => '1'],
                ['parent_id' => 22, 'action' => NULL, 'name' => 'My Leave Report', 'menu_url' => 'myLeaveReport.myLeaveReport', 'module_id' => '3', 'status' => '1'],

                ['parent_id' => 0, 'action' => NULL, 'name' => 'Warning', 'menu_url' => 'warning.index', 'module_id' => '2', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Termination', 'menu_url' => 'termination.index', 'module_id' => '2', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Promotion', 'menu_url' => 'promotion.index', 'module_id' => '2', 'status' => '1'],

                /**
                 *
                 * @attendance
                 *
                 */
                ['parent_id' => 20, 'action' => NULL, 'name' => 'Summary Report', 'menu_url' => 'attendanceSummaryReport.attendanceSummaryReport', 'module_id' => '4', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Manage Work Hour', 'menu_url' => null, 'module_id' => '5', 'status' => '1'],
                ['parent_id' => 58, 'action' => NULL, 'name' => 'Approve Work Hour', 'menu_url' => 'workHourApproval.create', 'module_id' => '5', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Employee Permanent', 'menu_url' => 'permanent.index', 'module_id' => '2', 'status' => '1'],

                ['parent_id' => 0, 'action' => NULL, 'name' => 'Manage Bonus', 'menu_url' => null, 'module_id' => '5', 'status' => '1'],
                ['parent_id' => 61, 'action' => NULL, 'name' => 'Bonus Setting', 'menu_url' => 'bonusSetting.index', 'module_id' => '5', 'status' => '1'],
                ['parent_id' => 61, 'action' => NULL, 'name' => 'Generate Bonus', 'menu_url' => 'generateBonus.index', 'module_id' => '5', 'status' => '1'],

                //new stuff here
                ['parent_id' => 22, 'action' => NULL, 'name' => 'Full Organization Report', 'menu_url' => 'leaveReport.fullOrganizationReport', 'module_id' => '3', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Rollover Leaves', 'menu_url' => 'rolloverLeaves', 'module_id' => '3', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Pending Approvals', 'menu_url' => 'pendingLeaveRequests.pendingLeaveRequests', 'module_id' => '3', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Front Setting', 'menu_url' => null, 'module_id' => '11', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'General Setting', 'menu_url' => 'front.setting', 'module_id' => '11', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Front Service	service.index',  'menu_url' => null, 'module_id' => '11', 'status' => '1'],

                ['parent_id' => 18, 'action' => NULL, 'name' => 'Dashboard Attendance', 'menu_url' => 'attendance.dashboard', 'module_id' => '4', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Pay Group', 'menu_url' => 'paygroup.index', 'module_id' => '5', 'status' => '0'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Job Category', 'menu_url' => 'job_category.index', 'module_id' => '5', 'status' => '0'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Daily Pay', 'menu_url' => 'daily_pay.index', 'module_id' => '5', 'status' => '0'],

                ['parent_id' => 25, 'action' => NULL, 'name' => 'NHIF Setup', 'menu_url' => 'nhif.index', 'module_id' => '5', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Salary Advances', 'menu_url' => 'advances.index', 'module_id' => '5', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Salary Bonuses', 'menu_url' => 'bonuses.index', 'module_id' => '5', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'All Applications', 'menu_url' => 'allLeaveApplications.allLeaveApplications', 'module_id' => '3', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Management Pay', 'menu_url' => 'managementPay.index', 'module_id' => '5', 'status' => '0'],
                ['parent_id' => 20, 'action' => NULL, 'name' => 'Weekly Attendance', 'menu_url' => 'weeklyAttendance.weeklyAttendance', 'module_id' => '4', 'status' => '1'],
                ['parent_id' => 33, 'action' => NULL, 'name' => 'P9 Home', 'menu_url' => 'payroll9.index', 'module_id' => '5', 'status' => '1'],
                // [ 'parent_id' => 0, 'action' => NULL, 'name' => 'Biometric Attendance', 'menu_url' => 'zkbiometricGet.index', 'module_id' => '4', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Biometric Devices', 'menu_url' => 'biometricDevices', 'module_id' => '4', 'status' => '1'],

                ['parent_id' => 25, 'action' => NULL, 'name' => 'Job Group', 'menu_url' => 'jobGroups.index', 'module_id' => '5', 'status' => '1'],
                // employee reports
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Employee Sections', 'menu_url' => 'employeeSection.index', 'module_id' => '2', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Employee Movement', 'menu_url' => 'employeeMovement.index', 'module_id' => '2', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Reports', 'menu_url' => null, 'module_id' => '2', 'status' => '1'],
                ['parent_id' => 85, 'action' => NULL, 'name' => 'Leavers Report', 'menu_url' => 'employee.leaversReport', 'module_id' => '2', 'status' => '1'],
                ['parent_id' => 85, 'action' => NULL, 'name' => 'Joiners Report', 'menu_url' => 'employee.joinersReport', 'module_id' => '2', 'status' => '1'],
                ['parent_id' => 85, 'action' => NULL, 'name' => 'Movement Report', 'menu_url' => 'employee.movementReport', 'module_id' => '2', 'status' => '1'],
                ['parent_id' => 85, 'action' => NULL, 'name' => 'Employee Turnover Report', 'menu_url' => 'employee.turnoverReport', 'module_id' => '2', 'status' => '1'],

                //manual leave upload
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Manual Upload', 'menu_url' => 'leaveManagement.manualUpload', 'module_id' => '3', 'status' => '1'],
                //meal report
                ['parent_id' => 20, 'action' => NULL, 'name' => 'Meal Report', 'menu_url' => 'attendance.mealReport', 'module_id' => '4', 'status' => '1'],
                ['parent_id' => 20, 'action' => NULL, 'name' => 'Anomaly Report', 'menu_url' => 'attendance.anomalyReport', 'module_id' => '4', 'status' => '1'],
                ['parent_id' => 20, 'action' => NULL, 'name' => 'Raw Report', 'menu_url' => 'attendance.view_raw_logs', 'module_id' => '4', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Anomalies', 'menu_url' => 'attendance.anomalies', 'module_id' => '4', 'status' => '1'],
                ['parent_id' => 0, 'action' => NULL, 'name' => 'Overtime Approval', 'menu_url' => 'attendance.approveOvertimes', 'module_id' => '4', 'status' => '1'],


            ]
        );
    }
}
