<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureBusAssigned
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // Only check for PIC Bus role
        if ($user->role === 'pic_bus' && !$user->bus_id) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'No bus assigned. Please contact administrator.'
                ], 403);
            }
            
            return redirect()->route('login')->with('error', 'Anda belum memiliki bus yang ditugaskan. Silakan hubungi administrator.');
        }

        return $next($request);
    }
}