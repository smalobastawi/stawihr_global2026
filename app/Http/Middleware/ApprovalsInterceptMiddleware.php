<?php

namespace App\Http\Middleware;

use App\Models\ApprovalRequest;
use App\Models\ApprovalRequestDbQueries;
use App\Models\GroupedMenuRoutePermission;
use App\Models\Module;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToArray;

class ApprovalsInterceptMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->hasHeader('artisan-source')) {
            $request_method = $request->method();
            if (in_array($request_method, ['POST', 'DELETE', 'PUT', 'PATCH'])) {

                try {
                    $routeName = $request->route()->getName();
                    $moduleName = GroupedMenuRoutePermission::where('permission', $routeName)->value('menu_name');
                    $module = Module::where('name', $moduleName)->first();
                    //Log::channel('approvals')->info("Module Found   ===" . $moduleName);
                } catch (\Exception $e) {
                    Log::channel('approvals')->info($e->getMessage());
                    return $next($request);

                }

                if ($module && $module->approvalSetting) {
                    DB::beginTransaction();
                    DB::enableQueryLog();

                    $response = $next($request);
                    $queries = DB::getQueryLog();
                    DB::disableQueryLog();
                    DB::rollBack();

                    // Log approval queries
                    $message = $this->logApproval($request, $queries);

                    // Add a flash message
                    if (session()->has('success')) {

                        $existingMessage = session('success', '');
                        $newMessage = $message;
                        if($message=='d'){
                            session()->flash('success', 'No Effect. A similar Request is logged For approval');
                        }
                        elseif ($message == null) {
                            session()->flash('success', 'No effect Action. Duplicate or No Changes Detected');
                        } else {
                            $updatedMessage = trim($existingMessage . '. ' . $newMessage);
                            session()->flash('approval', $updatedMessage);
                        }
                    }

                    return $response;
                }
            }
        }

        return $next($request);
    }

    protected function logApproval($request, $queries = [])
    {

        $routeName = $request->route()->getName();
        $request_method = $request->method();
        $uri = $request->getRequestUri(); 
        $uriWithoutDomain = preg_replace('/^https?:\/\/[^\/]+/i', '', $uri);

        $groupMenu = GroupedMenuRoutePermission::where('permission', $routeName)->first();
        $module = Module::where('name', $groupMenu->menu_name)->first();
        $module_id = $module->id;

        // Exclude _token, _method, and all file inputs
        $request_data = $request->input();
        unset($request_data['_token']); 
       

        if(ApprovalRequest::where('uri',$uriWithoutDomain)
                    ->where('route_name',$routeName)
                    ->where('request_method',$request_method)
                    ->where('effected',false)
                    ->whereNot('status','declined')
                    ->where('request_data',json_encode($request_data))
                    ->exists()){
                        return 'd';
                    }
        $approval_request = new ApprovalRequest();
        $approval_request->request_data = json_encode($request_data);
        $approval_request->module_id = $module_id;
        $approval_request->request_by = Auth::id();
        $approval_request->route_name = $routeName;
        $approval_request->uri = $uriWithoutDomain;
        $approval_request->action_type = $groupMenu->permission_description;
        $approval_request->request_method = $request_method;
        $approval_request->save();

        if (!$this->logQueries($queries, $approval_request)) {
            return null;
        }
        return "Request Action Has been Logged for Approval";
    }

    protected function logQueries(array $queries, ApprovalRequest $approval_request)
    {
        $savedQueries = 0; // Counter to track saved queries

        foreach ($queries as $query) {
            // Only process non-SELECT queries
            if (strpos(strtolower($query['query']), 'select') !== 0) {
                // Exclude INSERT queries into the `activity_log` table
                if (preg_match('/insert\s+into\s+`activity_log`/i', $query['query'])) {
                    continue; // Skip this query
                }

                // Handle UPDATE queries
                if (preg_match('/update\s+`(\w+)`\s+set\s+(.*)\s+where\s+(.*)/i', $query['query'], $matches)) {
                    $tableName = $matches[1];
                    $setClause = $matches[2];
                    $whereClause = $matches[3];

                    // Extract column names from SET clause
                    preg_match_all('/`(\w+)`\s*=\s*\?/i', $setClause, $columnMatches);
                    $columns = $columnMatches[1];

                    // Map bindings to updated columns
                    $newValues = array_combine($columns, array_slice($query['bindings'], 0, count($columns)));

                    // Fetch the current (old) values using the WHERE clause
                    $whereBindings = array_slice($query['bindings'], count($columns));
                    $oldValues = DB::table($tableName)
                        ->whereRaw($whereClause, $whereBindings)
                        ->first();

                    if ($oldValues) {
                        $changes = [];

                        foreach ($newValues as $column => $newValue) {
                            $oldValue = $oldValues->$column ?? null;

                            // Only log changes if the value is different
                            if ($oldValue != $newValue) {
                                if ($column != 'updated_at') {
                                    //$changes[$column] = ['oldValue' => $oldValue, 'newValue' => $newValue];
                                    $changes['oldValues'][$column] = $oldValue;
                                    $changes['newValues'][$column] = $newValue;
                                }
                            }
                        }

                        // Check for duplicates
                        $duplicate = ApprovalRequestDbQueries::where('query', $query['query'])
                            ->whereIn('approval_request_id', ApprovalRequest::whereNot('status', 'declined')
                                    ->where('effected', false)
                                    ->pluck('id')
                                    ->toArray())
                            ->where('changes', json_encode($changes))
                            ->exists();

                        // Log only if there are changes and no duplicate exists
                        if (!empty($changes) && !$duplicate) {
                            ApprovalRequestDbQueries::create([
                                'approval_request_id' => $approval_request->id,
                                'query' => $query['query'],
                                'bindings' => json_encode($query['bindings']),
                                'execution_time' => $query['time'],
                                'changes' => json_encode($changes),
                            ]);
                            $savedQueries++;
                        }
                    }
                }
                // Handle DELETE queries
                elseif (preg_match('/delete\s+from\s+`(\w+)`\s+where\s+(.*)/i', $query['query'], $matches)) {
                    $tableName = $matches[1];
                    $whereClause = $matches[2];

                    // Fetch the current (old) values being deleted
                    $oldValues = DB::table($tableName)->whereRaw($whereClause, $query['bindings'])->get();

                    // Check for duplicates
                    $duplicate = ApprovalRequestDbQueries::where('query', $query['query'])
                        ->whereIn('approval_request_id', ApprovalRequest::whereNot('status', 'declined')
                                ->where('effected', false)
                                ->pluck('id')
                                ->toArray())
                        ->where('bindings', json_encode($query['bindings']))
                        ->exists();

                    // Log only if no duplicate exists
                    if (!$duplicate && $oldValues->count() > 0) {

                        $oldValues = json_decode(json_encode($oldValues[0]), true);
                        ApprovalRequestDbQueries::create([
                            'approval_request_id' => $approval_request->id,
                            'query' => $query['query'],
                            'bindings' => json_encode($query['bindings']),
                            'execution_time' => $query['time'],
                            'changes' => json_encode([
                                //'oldValues' => $oldValues->toArray(),
                                'oldValues' => $oldValues,
                                'newValues' => array_fill_keys(array_keys($oldValues), ''),
                            ]),
                        ]);
                        $savedQueries++;
                    }
                }
                // Handle INSERT queries
                elseif (preg_match('/insert\s+into\s+`(\w+)`\s*\((.+)\)\s*values\s*\((.+)\)/i', $query['query'], $matches)) {
                    $tableName = $matches[1];
                    $columns = explode(',', str_replace('`', '', $matches[2])); // Extract column names
                    $columns = array_map('trim', $columns); // Remove extra spaces from column names

                    $bindingValues = array_map('strval', $query['bindings']);
                    $newValues = ['__0' => '__0'];
                    try {
                        $newValues = $this->columnBindingsCombine($columns, $bindingValues);

                    } catch (\Throwable $e) {
                        //dd($columns,$bindingValues,$e);
                    }

                    // $newValues = array_combine($columns, $query['bindings']); // Map bindings to columns
                    //dd($columns,$query['bindings']);
                    // Check for duplicates
                    $duplicate = ApprovalRequestDbQueries::where('query', $query['query'])
                        ->whereIn('approval_request_id', ApprovalRequest::whereNot('status', 'declined')
                                ->where('effected', false)
                                ->pluck('id')
                                ->toArray())
                        ->where('bindings', json_encode($query['bindings']))
                        ->exists();

                    // Log only if no duplicate exists
                    if (!$duplicate) {
                        ApprovalRequestDbQueries::create([
                            'approval_request_id' => $approval_request->id,
                            'query' => $query['query'],
                            'bindings' => json_encode($query['bindings']),
                            'execution_time' => $query['time'],
                            'changes' => json_encode([
                                'oldValues' => array_fill_keys($columns, ''), // Empty strings for old values
                                'newValues' => $newValues,
                            ]),
                        ]);
                        $savedQueries++;
                    }

                }
                // Handle other types of queries
                else {
                    $duplicate = ApprovalRequestDbQueries::where('query', $query['query'])
                        ->where('bindings', json_encode($query['bindings']))
                        ->whereIn('approval_request_id', ApprovalRequest::whereNot('status', 'declined')
                                ->where('effected', false)
                                ->pluck('id')
                                ->toArray())
                        ->exists();

                    if (!$duplicate) {
                        ApprovalRequestDbQueries::create([
                            'approval_request_id' => $approval_request->id,
                            'query' => $query['query'],
                            'bindings' => json_encode($query['bindings']),
                            'execution_time' => $query['time'],
                        ]);
                        $savedQueries++;
                    }
                }
            }
        }

        // If no queries were saved, delete the approval request
        if ($savedQueries === 0) {
            $approval_request->delete();
            return false;
        }
        return true;
    }

    protected function columnBindingsCombine(Array $arr1,Array $arr2)
    {
       // $arr1 = ['a', 'b', 'c']; // Assume this is the smaller array
       // $arr2 = [1, 2, 3, 4, 5, 6, 7, 8, 9]; // Larger array

// Combine for elements where both arrays have entries
        $combined = array_combine($arr1, array_slice($arr2, 0, count($arr1)));

// Handle the rest of the elements in arr2
        $remainder = array_slice($arr2, count($arr1)); // Get the remaining elements from arr2
        $patternLength = count($arr1); // Length of our pattern (arr1)

// Group remainder by the pattern of arr1
        $result = [];
        foreach ($remainder as $index => $value) {
            $key = $arr1[$index % $patternLength]; // Cycle through arr1 keys
            if (!isset($result[$key])) {
                $result[$key] = [];
            }
            $result[$key][] = $value;
        }

// Merge initial combined array with the grouped remainder
        foreach ($result as $key => $values) {
            $combined[$key] = isset($combined[$key]) ? $combined[$key] . ',' . implode(',', $values) : implode(',', $values);
        }
        return $combined;
    }
  

}
