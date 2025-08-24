<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Check if user has the required role
        if ($user->role !== $role) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized. You do not have permission to access this resource.'], 403);
            }
            
            // Redirect based on user's actual role
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            } elseif ($user->role === 'pic_bus') {
                return redirect()->route('pic.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            }
            
            return redirect()->route('login');
        }

        return $next($request);
    }
}