<?php

namespace App\Console\Commands;

use App\Models\GroupedMenuRoutePermission;
use App\Models\Module;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Artisan;
use Exception;
use Illuminate\Support\Str;

class RouteMenuSectionCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'route:create_route_menu_sections';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Capture routes metadata and save it to the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Create Already Existing Permission Groups...');

        Artisan::call('permission:create-grouped-permissions');

        $output = Artisan::output();
        $this->info($output);
        $this->info('Done Proceedign to Route Module and sections');
        $routes = Route::getRoutes();
        $routeData = [];
        $i = 0;
        $modules = [];
        $routeNames = [];
        $httpMethods = [
            'GET'    => 'READ',    // Retrieve data
            'POST'   => 'CREATE',   // Create new resource
            'PUT'    => 'UPDATE',   // Update existing resource (replace)
            'PATCH'  => 'UPDATE',   // Update existing resource (partial update)
            'DELETE' => 'DELETE',   // Delete resource
        ];

        foreach ($routes as $route) {
            $action = $route->action;
            $routeName = $route->getName();
            $route_uri = $route->uri();

            $subSection = $action['sub_section'] ?? 'default';
            $section = $action['section'] ?? 'default';

            if (isset($action['module']) && $section && $subSection) {


                $i++;
                $moduleName = $action['module'];
                $module = Module::where('name', $moduleName)->first();
                if (!$module) {

                    $this->info('  Module==' . $moduleName);
                    $module = new Module();
                    $module->name = $moduleName;
                    $module->icon_class = 'mdi mdi-format-line-weight';
                    $module->save();
                }
                $routeNames[] = $route->getName();
                $routeData[] = [
                    'module_id' => $module->id,
                    'module' => $moduleName,
                    'section' => $section,
                    'sub_section' => $subSection,
                    'route_name' =>  $routeName,
                    'actiontype' => $action['actiontype'] ?? $httpMethods[$route->methods()[0]],
                    'description' => $action['description'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (!$routeName) {
                    continue;
                }

                $grpP = GroupedMenuRoutePermission::where('permission', $routeName)->first();
                if (!$grpP) {

                    $grpP = new GroupedMenuRoutePermission();
                }
                $grpP->module_id = $module->id;
                $grpP->menu_name = $moduleName;
                $grpP->permission_group = $section;
                $grpP->permission = $routeName;

                $method = collect($route->methods())->first(fn($m) => isset($httpMethods[$m]));

                $grpP->actiontype = $action['actiontype'] ?? ($httpMethods[$method] ?? 'READ');
                $grpP->sub_section = $subSection;

                if (isset($action['description'])) {

                    $grpP->permission_description = $action['description'];
                }
                if (!$grpP->permission_description) {
                    $grpP->permission_description = $routeName;
                }
                $grpP->group_description = Str::ucfirst(Str::replace('_', ' ', $section));
                try {
                    $grpP->sub_section_description = Str::ucfirst(Str::replace('_', ' ', $subSection));
                } catch (\Throwable $e) {
                    dd($subSection);
                }



                try {
                    $test11 = $grpP->save();
                } catch (Exception $e) {
                    dd($grpP);
                }
            }
        }

        // Insert data into the database
        //DB::table('route_menu_section_groupings')->truncate();
        //DB::table('route_menu_section_groupings')->insert($routeData);

        $this->info('Routes metadata captured successfully!=== Categorised ' . $i . ' Routes');
        return 0;
    }
}
