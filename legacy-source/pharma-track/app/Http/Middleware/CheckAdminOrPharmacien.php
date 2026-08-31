<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdminOrPharmacien
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        if ($user->role === 'admin' || $user->role === 'pharmacien') {
            return $next($request);
        }

        return redirect()->route('medicaments.index')
            ->with('error', 'Vous n\'avez pas les permissions nécessaires.');
    }
}