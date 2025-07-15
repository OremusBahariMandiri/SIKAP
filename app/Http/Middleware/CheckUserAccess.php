<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class CheckUserAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $menu, string $action): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$user->hasAccess($menu, $action)) {
            // Instead of aborting with 403, redirect back with an alert
            Alert::error('Akses Ditolak', 'Anda tidak memiliki akses untuk melakukan tindakan ini.');
            
            // If it's an AJAX request, return a JSON response
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk melakukan tindakan ini.'
                ], 403);
            }
            
            // For regular requests, redirect back to the previous page
            return redirect()->back();
        }

        return $next($request);
    }
}