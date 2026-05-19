<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Console\Commands;

use App\Models\Module;
use App\Models\Menu;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncspatiePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:sync-to-menu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Spatie Permissions to Existing Menu';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    { 
        DB::beginTransaction();
        try{

        
        $moreMenuModule=Module::firstOrNew([
            'name' => 'More'
        ]);
        $moreMenuModule->save();
        $this->info($moreMenuModule->id);

        $more_menu_names=DB::select(DB::raw("SELECT * FROM `permissions` WHERE name not in (select menu_url from menus where menu_url is not null)"));
        //$this->info($more_menu_names);
        foreach($more_menu_names as $more_menu_name){
          $menu_name=  $more_menu_name->name;
          $menu= Menu::firstOrNew([
            'menu_url' => $menu_name
        ]);
          $menu->name=$menu_name;
          $menu->menu_url=$menu_name;
          $menu->action=null;
          $menu->module_id=$moreMenuModule->id;
          $menu->status=1;
          $menu->parent_id=0;
          $menu->save();
        }
        DB::commit();
        $this->info("Menus Added Successfull");
        return Command::SUCCESS;
    }catch(\Exception $e){
        //Roll Back Icase Of Exceptions
        DB::rollBack();
        $this->error($e->getMessage());
        return Command::FAILURE;
        }
        
        
    }
}
