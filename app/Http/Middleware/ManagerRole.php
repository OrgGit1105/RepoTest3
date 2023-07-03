<?php

namespace App\Http\Middleware;

use Closure;
use Helper\ResponseService;
use Illuminate\Http\Request;

class ManagerRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
      $user = Auth()->user();
      if ($user->role->name !== "Manager"){
        return ResponseService::responseJsonError(CODE_NO_ACCESS, 'user not permission', 'user not permission');
      }
      return $next($request);
    }
}
