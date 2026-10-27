<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\ProgramLoyalitas;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the unified login page with role tabs.
     */
    public function showLogin(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isAdminOrStaff()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('customer.dashboard');
        }

        $activeTab = $request->query('tab', 'user');
        if (! in_array($activeTab, ['user', 'admin'], true)) {
            $activeTab = 'user';
        }

        return view('auth.login', compact('activeTab'));
    }

    /**
     * Process authentication request with strict role separation.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'login_type' => ['required', 'in:user,admin'],
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
            'remember' => ['nullable'],
        ], [
            'identifier.required' => 'Alamat email wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
            'login_type.required' => 'Tipe login harus dipilih.',
        ]);

        $identifier = trim((string) $request->input('identifier'));
        $loginType = $request->input('login_type');
        $remember = $request->boolean('remember');

        // Look up user strictly by email (if identifier contains @) or phone number
        $user = str_contains($identifier, '@')
            ? User::where('email', $identifier)->first()
            : User::where('nomor_telepon', $identifier)->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            return back()
                ->withInput($request->only('identifier', 'login_type', 'remember'))
                ->withErrors([
                    'identifier' => 'Email atau Kata Sandi yang Anda masukkan salah. Silakan periksa kembali.',
                ]);
        }

        // Strict Role Separation Check
        if ($loginType === 'admin') {
            if (! $user->isAdminOrStaff()) {
                return back()
                    ->withInput($request->only('identifier', 'login_type', 'remember'))
                    ->withErrors([
                        'role_error' => 'Akses Ditolak: Akun Anda adalah akun Pelanggan dan tidak memiliki hak akses ke panel Admin / Kasir. Silakan beralih ke tab "Login User / Pelanggan".',
                    ]);
            }

            Auth::login($user, $remember);
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Selamat datang di Dashboard Admin & Kasir, '.$user->nama.'!');
        }

        // Customer login branch
        if ($loginType === 'user') {
            if (! $user->isCustomer()) {
                return back()
                    ->withInput($request->only('identifier', 'login_type', 'remember'))
                    ->withErrors([
                        'role_error' => 'Akun Anda terdaftar sebagai Admin / Kasir. Silakan beralih ke tab "Login Admin / Kasir" untuk mengakses Dashboard Admin.',
                    ]);
            }

            Auth::login($user, $remember);
            $request->session()->regenerate();

            return redirect()->intended(route('customer.dashboard'))
                ->with('success', 'Selamat datang di Portal Pelanggan Matahari FotoCopy, '.$user->nama.'!');
        }

        return redirect()->route('login');
    }

    /**
     * Handle Customer Registration.
     * Guaranteed role: 'user' / Pelanggan. Cannot register as admin.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'max:100', 'unique:users,email'],
            'nomor_telepon' => ['required', 'string', 'max:20', 'unique:users,nomor_telepon'],
            'password' => ['required', 'string', 'min:5', 'confirmed'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar sebelumnya.',
            'nomor_telepon.required' => 'Nomor WhatsApp wajib diisi.',
            'nomor_telepon.unique' => 'Nomor WhatsApp ini sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 5 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        // Explicitly hardcode 'user' role
        $user = User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'nomor_telepon' => $validated['nomor_telepon'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
        ]);

        // Create linked customer record with 50 loyalty points welcome reward
        $pelanggan = Pelanggan::create([
            'user_id' => $user->user_id,
            'saldo_poin' => 50,
        ]);

        ProgramLoyalitas::create([
            'pelanggan_id' => $pelanggan->pelanggan_id,
            'jumlah_poin_masuk' => 50,
            'jumlah_poin_keluar' => 0,
            'tanggal_transaksi' => now(),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('customer.dashboard')
            ->with('success', 'Pendaftaran Akun Berhasil! Selamat datang, '.$user->nama.'. Anda mendapatkan bonus 50 Poin Loyalitas!');
    }

    /**
     * Terminate user session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil keluar dari sistem Matahari FotoCopy.');
    }
}
