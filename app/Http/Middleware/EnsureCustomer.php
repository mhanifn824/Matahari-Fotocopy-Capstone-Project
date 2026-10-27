<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomer
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login', ['tab' => 'user'])
                ->with('error', 'Silakan masuk terlebih dahulu ke akun Pelanggan Anda.');
        }

        $user = auth()->user();

        if (! $user->isCustomer()) {
            return redirect()->route('admin.dashboard')
                ->with('warning', 'Akun Anda adalah Admin / Kasir Matahari Fotocopy. Anda telah dialihkan ke Dashboard Admin.');
        }

        return $next($request);
    }
}
