<?php

namespace App\Mail\Payroll;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Payroll\PayrollRecord;
use Barryvdh\DomPDF\Facade\Pdf;

class SendPayslipEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $payrollRecord;
    public $customMessage;

    /**
     * Create a new message instance.
     *
     * @param PayrollRecord $payrollRecord
     * @param string|null $customMessage
     */
    public function __construct(PayrollRecord $payrollRecord, $customMessage = null)
    {
        $this->payrollRecord = $payrollRecord;
        $this->customMessage = $customMessage;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $employee = $this->payrollRecord->employeePayroll->employee;
        $period = $this->payrollRecord->payrollPeriod;

        $subject = "Payslip for {$period->name} - {$employee->fullName()}";

        // Generate PDF
        $pdf = $this->generatePayslipPdf();

        $filename = "Payslip_{$employee->fullName()}_{$period->name}.pdf";

        return $this->view('emails.payroll.payslip_email')
            ->subject($subject)
            ->with([
                'payrollRecord' => $this->payrollRecord,
                'employee' => $employee,
                'period' => $period,
                'customMessage' => $this->customMessage
            ])
            ->attachData($pdf->output(), $filename, [
                'mime' => 'application/pdf'
            ]);
    }

    /**
     * Generate payslip PDF
     */
    private function generatePayslipPdf()
    {
        $this->payrollRecord->load([
            'employeePayroll.employee',
            'payrollPeriod',
            'details'
        ]);
        $userPassword = $this->payrollRecord->employeePayroll->employee->national_id ?? $this->payrollRecord->employeePayroll->employee->payroll_number;
        $pdf = Pdf::loadView('admin.payroll.payslip', [
            'payrollRecord' => $this->payrollRecord
        ])->setPaper('a4');
        $pdf->getDomPDF()->getCanvas()->get_cpdf()->setEncryption($userPassword, env('APP_KEY'));

        return $pdf;
    }
}
