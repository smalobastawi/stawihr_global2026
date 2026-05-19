<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Log;
use App\Models\GroupedMenuRoutePermission;
use Illuminate\Console\Command;

class CreateGroupedPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:create-grouped-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Group Route Permissions';
    public function handle()
    {
          $permissionGroups=new GroupedRoutePermissions();
          $menuGroups=$permissionGroups->groupedMenuPermissions();
          $permGrpPms=$permissionGroups->groupedPermissions();
          
          GroupedMenuRoutePermission::where('id','>',0)->delete();

           foreach($permGrpPms as $group) {
             
             $permission_group=$group['permission_group'];
             $group_description=$group['group_description'];
             foreach($group['permissions'] as $pm) {
                $permission_name=$pm['name'];
                $permission_description=$pm['description'];
                $grpPerm=new GroupedMenuRoutePermission();
                /*
                 $table->string('menu_name')->nullable(true);
                  $table->string('permission_group');
                  $table->string('group_description');
                  $table->string('permission');
                  $table->string('permission_description')
                */
                $grpPerm->permission_group=(string)$permission_group;
                $grpPerm->group_description=(string)$group_description;
                $grpPerm->permission=(string)$permission_name;
                                $grpPerm->permission_description=(string)$permission_description;
                $grpPerm->module_id = '';
                $grpPerm->menu_name = '';
                $grpPerm->actiontype = '';
                $grpPerm->sub_section = '';
                $grpPerm->sub_section_description = '';
                Log::info('Attempting to save permission:', $grpPerm->toArray());
                $grpPerm->save();

             }
           }

           foreach($menuGroups as $mGroup=>$menus) {
            $menuGroupP=$mGroup;
            foreach($menus as $menu) {
                $menuP=$menu; 
                GroupedMenuRoutePermission::where('permission_group', (string)$menuP)
                ->update(['menu_name' => $menuGroupP]);
            }
            
          }
          $this->info('Permission Groups Successfuly Defined');
  

        return Command::SUCCESS;
    }
}