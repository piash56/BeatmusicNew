<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('dashboard.profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'full_name' => 'required|string|max:255',
            'country' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'social_facebook' => 'nullable|url',
            'social_twitter' => 'nullable|url',
            'social_instagram' => 'nullable|url',
            'social_website' => 'nullable|url',
        ]);

        $user->update($request->only([
            'full_name',
            'country',
            'phone',
            'address',
            'city',
            'state',
            'zip',
            'bio',
            'social_facebook',
            'social_twitter',
            'social_instagram',
            'social_website',
        ]));

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updatePicture(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,jpg,png,gif|max:5120',
        ]);

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $path = $request->file('profile_picture')->store('profiles', 'public');
        $user->update(['profile_picture' => $path]);

        return back()->with('success', 'Profile picture updated!');
    }

    public function settings()
    {
        $user = Auth::user();
        return view('dashboard.profile.settings', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        if (Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'New password cannot be the same as your current password.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password updated successfully!');
    }

    public function billing()
    {
        $user = Auth::user();
        $paymentMethods = $user->paymentMethods()->get();
        return view('dashboard.profile.billing', compact('user', 'paymentMethods'));
    }

    public function updatePayout(Request $request)
    {
        $user = Auth::user();

        if ($user->paypal_email) {
            return back()->with('info', 'Your PayPal email is already set. Please contact support if you need to change it.');
        }

        $request->validate([
            'paypal_email' => 'required|email|max:255',
        ]);

        $user->update([
            'payout_method' => 'paypal',
            'paypal_email' => $request->paypal_email,
        ]);

        return back()->with('success', 'Payout settings updated successfully!');
    }
}
