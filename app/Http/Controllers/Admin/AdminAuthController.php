<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    public function showLogin(): View
    {
        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $login = trim($credentials['login']);
        $authCredentials = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? ['email' => $login, 'password' => $credentials['password']]
            : ['no_telp' => $login, 'password' => $credentials['password']];

        if (Auth::attempt($authCredentials)) {
            $request->session()->regenerate();

            if (Auth::user()->role !== 'admin') {
                Auth::logout();
                return back()->withErrors(['login' => 'Only admin can access this dashboard.'])->onlyInput('login');
            }

            return redirect()->route('admin.tickets.index');
        }

        return back()->withErrors(['login' => 'Invalid credentials.'])->onlyInput('login');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login.form');
    }
}
