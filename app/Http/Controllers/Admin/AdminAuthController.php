<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminAuthController extends Controller
{
    protected EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function showLogin()
    {
        if (auth()->check() && auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->where('is_admin', true)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Invalid admin credentials.'])->withInput();
        }

        Auth::login($user);

        $user->update([
            'last_login_time' => now(),
            'last_login_ip' => $request->ip(),
            'last_active' => now(),
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Welcome back, ' . $user->full_name . '!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

    public function showForgotPassword()
    {
        return view('admin.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->where('is_admin', true)->first();

        if ($user) {
            $token = Str::random(64);
            $user->update([
                'reset_password_token' => hash('sha256', $token),
                'reset_password_expiry' => now()->addHours(1),
            ]);
            $this->emailService->sendPasswordReset($user, $token, true);
        }

        return back()->with('success', 'If an admin account exists with this email, a reset link has been sent.');
    }

    public function showResetPassword(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');
        return view('admin.reset-password', compact('token', 'email'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)
            ->where('is_admin', true)
            ->where('reset_password_token', hash('sha256', $request->token))
            ->first();

        if (!$user || !$user->reset_password_expiry || $user->reset_password_expiry->isPast()) {
            return back()->withErrors(['email' => 'Invalid or expired reset link.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'reset_password_token' => null,
            'reset_password_expiry' => null,
        ]);

        return redirect()->route('admin.login')->with('success', 'Password reset successfully.');
    }
}
