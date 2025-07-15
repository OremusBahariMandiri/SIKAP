<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Skip check for first login if there are no admins
        $adminsCount = \App\Models\User::where('is_admin', true)->count();
        if ($adminsCount == 0) {
            return $next($request);
        }

        if (!$user->isAdmin()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Hanya administrator yang dapat mengakses halaman ini.'], 403);
            }

            session()->flash('error', 'Hanya administrator yang dapat mengakses halaman ini.');

            if ($request->header('Referer')) {
                return redirect($request->header('Referer'));
            }

            return redirect()->route('home');
        }

        return $next($request);
    }
}