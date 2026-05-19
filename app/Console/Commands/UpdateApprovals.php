<?php

namespace App\Console\Commands;

use App\Models\ApprovalRecord;
use App\Models\ApprovalRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UpdateApprovals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'approvals:update-approvals-requests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to update approval requests to the respective models';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try{
            $appprovalRecords = ApprovalRequest::where('effected', false,)->where('status','approved')->get();
            foreach ($appprovalRecords as $appprovalRecord) {
                 
                $this->approve($appprovalRecord);
                $appprovalRecord->effected=true;
                $appprovalRecord->save(); 
            }
            return 0;
        }catch(\Exception $e){
            $this->error($e->getMessage());
            return 1;
        }
       
        
    }

    protected function approve(ApprovalRequest $approvalRequest){

        $approvalQueries=$approvalRequest->queries;
        foreach ($approvalQueries as $query) {  
            $bindings = json_decode($query->bindings, true);

            // Execute the query
            DB::statement($query->query, $bindings);
 
            $this->info("Executed: " . $query->query);
        }

      /*  $routeName = $approvalRequest->route_name;
        $data = json_decode($approvalRequest->request_data);
        $method=$approvalRequest->request_method;

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON format in the data option.');
            return 1;
        }

        // Resolve the route URL
        $url = route($routeName);

        if (!$url) {
            $this->error("The route '{$routeName}' does not exist.");
            return 1;
        }

        try {
            // Step 1: Fetch the CSRF token
            $csrfResponse = Http::get($url);
            preg_match('/<meta name="csrf-token" content="([^"]+)"/', $csrfResponse->body(), $matches);

            if (empty($matches[1])) {
                $this->error('Unable to retrieve CSRF token.');
                return 1;
            }

            $csrfToken = $matches[1];

            // Step 2: Submit data with the CSRF token
            $response = Http::withHeaders([
                'X-CSRF-TOKEN' => $csrfToken,
                'Accept' => 'application/json',
                'artisan-source'=>'replay'
            ])->send($method, $url, [
                'form_params' => $data,
            ]);

            if ($response->successful()) {
                $this->info('Data successfully submitted!');
                $this->info('Response: ' . $response->body());
                return 0;
            } else {
                $this->error('Failed to submit data. Response: ' . $response->body());
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }

        */
    

    }
}

