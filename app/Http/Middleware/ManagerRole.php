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
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth()->user();
        $policies = $user->viam_user->policies;
        foreach ($policies as $policy) {
            if ($policy->type == POLICY_TYPE['V_FACE'] && $policy->name == POLICY_V_FACE_NAME['Admin']) {
                return $next($request);
            }
        }
        return ResponseService::responseJsonError(CODE_NO_ACCESS, 'user not permission', 'user not permission');
    }
}
