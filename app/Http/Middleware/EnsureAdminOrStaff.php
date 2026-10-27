<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminOrStaff
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login', ['tab' => 'admin'])
                ->with('error', 'Silakan masuk terlebih dahulu dengan akun Admin / Kasir.');
        }

        $user = auth()->user();

        if (! $user->isAdminOrStaff()) {
            return redirect()->route('customer.dashboard')
                ->with('error', 'Akses Ditolak: Akun Anda adalah akun Pelanggan dan tidak memiliki hak akses ke panel Admin.');
        }

        return $next($request);
    }
}
