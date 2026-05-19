<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
class CreateRoutePermissionsCommand extends Command
{
    
    protected $signature = 'permission:create-permission-routes';

    protected $description = 'Create a permission routes.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /** 
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $routes = Route::getRoutes()->getRoutes();

        foreach ($routes as $route) {
            
            $routeName = $route->getName();

if (!$routeName) {
    continue;
}

$middleware = $route->gatherMiddleware();

if (!in_array('web', $middleware)) {
    continue;
}

Permission::firstOrCreate([
    'name' => $routeName,
    'guard_name' => 'web',
]);

        }

        $this->info('Permission routes added successfully.');


        $this->info('Categorise All Routes');

       Artisan::call('route:create_route_menu_sections');
        
        $output = Artisan::output();

        $this->info( $output); 
    }
}