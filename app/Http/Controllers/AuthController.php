<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    protected EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function showLogin()
    {
        if (auth()->check() && auth()->user()->is_verified) {
            return redirect()->route('dashboard.home');
        }
        return view('auth.login');
    }

    public function showRegister()
    {
        if (auth()->check() && auth()->user()->is_verified) {
            return redirect()->route('dashboard.home');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'country' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'country' => $request->country,
            'otp' => $otp,
            'otp_expires' => now()->addMinutes(5),
            'is_verified' => false,
        ]);

        $this->emailService->sendOtp($user, $otp);

        return redirect()->route('verify-otp', ['userId' => $user->id])
            ->with('success', 'Registration successful! Please check your email for the OTP.');
    }

    public function showVerifyOtp($userId)
    {
        $user = User::findOrFail($userId);
        if ($user->is_verified) {
            return redirect()->route('dashboard.home');
        }
        return view('auth.verify-otp', compact('user'));
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'otp' => 'required|string|size:6',
        ]);

        $user = User::findOrFail($request->user_id);

        if ($user->is_verified) {
            return redirect()->route('dashboard.home');
        }

        if (!$user->otp || $user->otp !== $request->otp) {
            return back()->withErrors(['otp' => 'Invalid OTP code.']);
        }

        if ($user->otp_expires && $user->otp_expires->isPast()) {
            return back()->withErrors(['otp' => 'OTP has expired. Please request a new one.']);
        }

        $user->update([
            'is_verified' => true,
            'otp' => null,
            'otp_expires' => null,
            'last_login_time' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        Auth::login($user);

        return redirect()->route('dashboard.home')->with('success', 'Email verified successfully! Welcome to Beat Music.');
    }

    public function resendOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);

        if ($user->is_verified) {
            return back()->with('info', 'Your account is already verified.');
        }

        // Rate limiting: max 5 resends
        if ($user->otp_resend_count >= 5) {
            if ($user->otp_resend_reset_time && $user->otp_resend_reset_time->isFuture()) {
                return back()->withErrors(['otp' => 'Too many OTP requests. Please wait before trying again.']);
            }
            $user->update(['otp_resend_count' => 0, 'otp_resend_reset_time' => null]);
        }

        // Throttle: 1 minute between resends
        if ($user->last_otp_resend_time && $user->last_otp_resend_time->diffInSeconds(now()) < 60) {
            return back()->withErrors(['otp' => 'Please wait 1 minute before requesting a new OTP.']);
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->update([
            'otp' => $otp,
            'otp_expires' => now()->addMinutes(5),
            'otp_resend_count' => $user->otp_resend_count + 1,
            'last_otp_resend_time' => now(),
            'otp_resend_reset_time' => $user->otp_resend_count + 1 >= 5 ? now()->addHours(1) : null,
        ]);

        $this->emailService->sendOtp($user, $otp);

        return back()->with('success', 'OTP resent to your email.');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No account found with this email.'])->withInput();
        }

        if (!$user->is_verified) {
            return back()->withErrors(['email' => 'Please verify your account. Check your email for the verification or set-password link.'])->withInput();
        }

        if (!$user->isActive()) {
            return back()->withErrors(['email' => 'Your account has been suspended. Please contact support.'])->withInput();
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
        }

        Auth::login($user, $request->boolean('remember'));

        $user->update([
            'last_login_time' => now(),
            'last_login_ip' => $request->ip(),
            'last_active' => now(),
        ]);

        return redirect()->intended(route('dashboard.home'))->with('success', 'Welcome back, ' . $user->full_name . '!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('success', 'You have been logged out.');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Don't reveal if user exists
            return back()->with('success', 'If an account exists with this email, a reset link has been sent.');
        }

        $token = Str::random(64);
        $user->update([
            'reset_password_token' => hash('sha256', $token),
            'reset_password_expiry' => now()->addHours(1),
        ]);

        $this->emailService->sendPasswordReset($user, $token);

        return back()->with('success', 'Password reset link has been sent to your email.');
    }

    public function showResetPassword(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');
        return view('auth.reset-password', compact('token', 'email'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)
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

        return redirect()->route('login')->with('success', 'Password reset successfully. Please log in.');
    }

    public function showSetPassword(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');

        if (!$token || !$email) {
            return redirect()->route('login')->withErrors(['email' => 'Invalid set password link.']);
        }

        $user = User::where('email', $email)->first();
        if (!$user || !$user->reset_password_token || hash('sha256', $token) !== $user->reset_password_token) {
            return redirect()->route('login')->withErrors(['email' => 'Invalid set password link.']);
        }
        if (!$user->reset_password_expiry || $user->reset_password_expiry->isPast()) {
            return redirect()->route('login')->withErrors(['email' => 'This set password link has expired. Please ask the admin to resend it.']);
        }

        return view('auth.set-password', compact('token', 'email'));
    }

    public function setPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)
            ->where('reset_password_token', hash('sha256', $request->token))
            ->first();

        if (!$user || !$user->reset_password_expiry || $user->reset_password_expiry->isPast()) {
            return back()->withErrors(['email' => 'Invalid or expired set password link.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'reset_password_token' => null,
            'reset_password_expiry' => null,
            'is_verified' => true,
        ]);

        return redirect()->route('login')->with('success', 'Password set successfully. You can now log in.');
    }
}
