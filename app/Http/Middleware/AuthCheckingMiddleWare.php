<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Middleware;

use Closure;

use DB;

class AuthCheckingMiddleWare
{

    public function handle($request, Closure $next)
    {
        $role_id = session('logged_session_data.role_id');
        if(isset($role_id))
        {
            $namedRoute 		= \Route::currentRouteName();
            $current_url_check  = DB::table('menus')->select('menu_url')->where('menu_url', $namedRoute)->where('status', 1)->get()->toArray();
            if ($namedRoute)
            {
                if ($current_url_check)
                {
                    $permissionCheck = DB::table('menus')
                        ->join('menu_permission', 'menu_permission.menu_id', '=', 'menus.id')
                        ->where('role_id', $role_id)
                        ->where('menu_url', $namedRoute)
                        ->get()->toArray();
                    if (empty($permissionCheck) || count($permissionCheck) < 0)
                    {
                        return response()->view('errors.permissions_denied', [], 550);
                    }
                }
            }
        }

        
        return $next($request);
    }
}
