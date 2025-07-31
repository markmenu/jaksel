<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Ambil peran pengguna yang baru saja login
        $role = Auth::user()->role;

        // Terapkan logika pengalihan berdasarkan peran
        if ($role === 'kepala') {
            return redirect()->route('kepala.dashboard');
        }

        if ($role === 'superadmin') {
            return redirect()->route('superadmin.dashboard');
        }

        if ($role === 'ketua_tim' || $role === 'anggota_tim') {
            return redirect()->route('tim.dashboard');
        }

        // Sebagai cadangan, jika tidak ada peran yang cocok
        return redirect('/');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
