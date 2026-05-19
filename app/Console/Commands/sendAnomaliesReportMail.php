<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Console\Commands;

use App\Exports\AnomaliesExport;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\WorkShift;
use Illuminate\Console\Command;
use PDF;
use Mail;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class sendAnomaliesReportMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendAnomaliesReportMail:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filename = Carbon::now()->subDays(1)->format('Y-m-d ms').'-attendance anomalies.xlsx';
         Excel::store(new AnomaliesExport, $filename);

         $attendanceDate = Carbon::now()->subDays(1)->format('Y-m-d');
        $excelFile =storage_path('app/').$filename;
        $data["email"] = "smaloba3@gmail.com";
        $data["title"] = "Attendance Anomalies";
        $data["body"] = "This is test mail with attachment";

        Mail::send('emails.Test_mail', $data, function($message)use($data, $excelFile, $attendanceDate) {
            $message->to($data["email"])
                ->subject($data["title"].'-'.$attendanceDate)
                ->attach($excelFile);
        });

        dd('Mail sent successfully');
    }
}
