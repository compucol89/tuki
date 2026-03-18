<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HasPermission
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle(Request $request, Closure $next, $menuName)
  {
    $authAdmin = Auth::guard('admin')->user();

    if (is_null($authAdmin)) {
      abort(403);
    }

    if (is_null($authAdmin->role_id)) {
      if ((int) $authAdmin->id === 1) {
        return $next($request);
      }

      abort(403);
    }

    $role = $authAdmin->role()->first();
    $rolePermissions = [];

    if (!is_null($role) && !empty($role->permissions)) {
      $rolePermissions = json_decode($role->permissions, true) ?: [];
    }

    if (!empty($rolePermissions) && in_array($menuName, $rolePermissions, true)) {
      return $next($request);
    }

    abort(403);
  }
}
