<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permission = null, $guard = null)
    {

        $authGuard = app('auth')->guard($guard);

        if ($authGuard->guest()) {
            throw UnauthorizedException::notLoggedIn();
        }

        $user = Auth::user();

        if ($user && is_null($user->password_changed_at) && is_null($user->google_id)) {
                       Redirect::to(route('changePassword.index'))->send();
                   }

        if (! is_null($permission)) {
            $permissions = is_array($permission)
                ? $permission
                : explode('|', $permission);
        }

        if (is_null($permission)) {
            $permission = $request->route()->getName();
            if ($permission == 'home.dashboard' || $permission == 'home.logout' || $permission == 'home.profile') {
                return $next($request);
            }

            if (in_array($permission, ['moduleSettings.index', 'moduleSettings.update'], true)
                && $authGuard->user()->can('systemSettings.index')) {
                return $next($request);
            }

            $permissions = array($permission);
        }


        foreach ($permissions as $permission) {

            if ($authGuard->user()->can($permission)) {
                return $next($request);
            }
        }

        throw UnauthorizedException::forPermissions($permissions);
    }
}
