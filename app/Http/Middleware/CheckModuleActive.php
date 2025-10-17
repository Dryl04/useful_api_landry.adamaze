<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $moduleName  Le nom du module à vérifier
     */
    public function handle(Request $request, Closure $next, string $moduleName): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                "error" => "Unauthenticated"
            ], 401);
        }

        $module = $user->modules()
            ->where("name", $moduleName)
            ->wherePivot("active", true)
            ->first();

        if (!$module) {
            return response()->json([
                "error" => "Module inactive. Please activate this module to use it."
            ], 403);
        }

        return $next($request);
    }
}
