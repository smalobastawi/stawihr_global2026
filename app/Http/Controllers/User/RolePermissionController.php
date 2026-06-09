<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\User;

use App\Http\Requests\RolePermissionRequest;
use App\Models\GroupedMenuRoutePermission;
use App\Repositories\CommonRepository;
use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Services\ModuleActivationService;
use App\Models\Role as ModelsRole;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use function MongoDB\BSON\toJSON;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RolePermissionController extends Controller
{

    protected $commonRepository;
    protected ModuleActivationService $moduleActivation;

    public function __construct(CommonRepository $commonRepository, ModuleActivationService $moduleActivation)
    {
        $this->commonRepository = $commonRepository;
        $this->moduleActivation = $moduleActivation;
    }

    public function index()
    {
        $roleList = $this->commonRepository->roleList();
        return view('admin.user.role.add_user_permission', ['data' => $roleList]);
    }

    public function getAllMenu2(Request $request)
    {
        $role_id     = $request->role_id;
        $permissions =  json_decode(DB::table('menus')
                        ->select(DB::raw('`menus`.`id`, `menus`.`name`, `menus`.`menu_url`, `menus`.`parent_id`, `menus`.`module_id`,menu_permission.menu_id'))
                        ->join('menu_permission', 'menu_permission.menu_id', '=', 'menus.id')
                        ->where('menu_permission.role_id', '=', $role_id)
                        ->get()->toJson(),true);


        $allMenus = json_decode(DB::table('menus')
                    ->select(DB::raw('menus.*,modules.name as moduleName,modules.icon_class'))
                    ->join('modules', 'modules.id', '=', 'menus.module_id')
                    ->where('menus.status', '=', 1)
                    ->whereNotNull('menu_url')
                    ->orderBy('module_id')
                    ->get()->toJSON(),true);

        $subMenu = [];

        $arrayFormat = [];
        foreach ($allMenus as $allMenu)
        {
            $hasPermission = array_search($allMenu['id'], array_column($permissions, 'menu_id'));

            if(gettype($hasPermission) == 'integer'){
                $allMenu['hasPermission'] ='yes';
            }else{
                $allMenu['hasPermission'] ='no';
            }

            if(!empty($allMenu['action'])){
                $subMenu[$allMenu['parent_id']][] = $allMenu;
            }

            if($allMenu['action']==''){
                $arrayFormat[$allMenu['moduleName']][$allMenu['name']] = $allMenu;
            }
        }

       // dd($arrayFormat,$subMenu);

        return ['arrayFormat'=>$arrayFormat,'subMenu'=>$subMenu];
    }


    public function getAllMenu(Request $request)
    {
        $enabledModuleIds = $this->moduleActivation->enabledModules()->pluck('id');

        $groupPms = GroupedMenuRoutePermission::with('module')
            ->whereIn('module_id', $enabledModuleIds)
            ->select('menu_name', 'module_id')
            ->groupBy('menu_name')
            ->groupBy('module_id')
            ->get();
        $roleId=$request->role_id;
        $role = Role::where('id', $roleId)->first();
        $rolePermissions=$role->permissions()->orderBy('name','asc')->pluck('name')->toArray();
        //dd($rolePermissions);
        $modules = $this->moduleActivation->enabledModules();
        $actionTypeColors=[
            'CREATE' => 'text-success', // Green
            'READ' => 'text-primary',  // Blue
            'UPDATE' => 'text-warning', // Orange
            'DELETE' => 'text-danger',  // Gray for unknown action types
        ];
        $pmGroups=GroupedMenuRoutePermission::select('permission_group','sub_section','module_id');
        return view('admin.user.role.grouped_permission_routes')->with([
            'menus'=>$groupPms,
            'modules'=>$modules,
            'ppmGroups'=>$pmGroups,
            'role_permissions'=>$rolePermissions,
            'actionTypeColors'=>$actionTypeColors
        ]);
    }


    public function store(RolePermissionRequest $request)
    {
        $role = Role::where('id', $request->role_id)->first();

        if (Auth::user()->hasRole([$role->name])) {
            return redirect()->back()->with('error', 'Editing own role not allowed.');
        }

        try {
            DB::beginTransaction();
           
            $menus = $request->get('menu') ?? [];
            $permission_groups = $request->get('permission_group') ?? [];
            $perms = $request->get('permission') ?? [];
            $sub_sections = $request->get('sub_section') ?? [];
            $moreMenus = GroupedMenuRoutePermission::whereIn('permission', $perms)
                            ->distinct()
                            ->pluck('menu_name')
                            ->map(function($menu) {
                                return 'pmMenu__' . $menu;
                            })->toArray() ?? [];
            // $morePermGroups = GroupedMenuRoutePermission::whereIn('permission', $perms)
            //                 ->distinct()->pluck('permission_group')->toArray()?? [];
            // $moreSubSections=GroupedMenuRoutePermission::whereIn('permission', $perms)
            // ->distinct()->pluck('sub_section')->toArray()?? [];
       $permissions=array_merge($permission_groups,$menus,$sub_sections,$perms,$moreMenus/*,$morePermGroups,$moreSubSections*/);
            $permissions = $this->moduleActivation->filterPermissionsForEnabledModules($permissions);
            foreach($permissions as $permission){
                Permission::updateOrCreate(['name'=>$permission],['name'=>$permission,'guard'=>'web']);
                
            } 
            $role->syncPermissions($permissions);
            DB::commit();
            return redirect()->back()->with('success', $role->name.' Role Permissions Update Successfully');
            }catch(Exception $e){
                 return redirect()->back()->with('error', 'Some Error Found !, Please try again.'.$e->getMessage());
           }
             

    }



}
