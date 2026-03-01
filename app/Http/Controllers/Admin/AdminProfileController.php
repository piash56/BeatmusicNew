<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminProfileController extends Controller
{
    public function index()
    {
        $admin = auth()->user();
        return view('admin.profile.index', compact('admin'));
    }

    public function update(Request $request)
    {
        $admin = auth()->user();

        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin->id,
            'phone' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'social_facebook' => 'nullable|url',
            'social_twitter' => 'nullable|url',
            'social_instagram' => 'nullable|url',
            'social_website' => 'nullable|url',
        ]);

        $data = $request->only([
            'full_name',
            'email',
            'phone',
            'country',
            'city',
            'state',
            'zip',
            'address',
            'bio',
            'social_facebook',
            'social_twitter',
            'social_instagram',
            'social_website',
        ]);

        if ($request->hasFile('profile_picture')) {
            $request->validate(['profile_picture' => 'image|mimes:jpeg,jpg,png|max:5120']);
            if ($admin->profile_picture) Storage::disk('public')->delete($admin->profile_picture);
            $data['profile_picture'] = $request->file('profile_picture')->store('profiles', 'public');
        }

        $admin->update($data);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        $admin = auth()->user();

        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        if (Hash::check($request->password, $admin->password)) {
            return back()->withErrors(['password' => 'New password cannot be the same as your current password.']);
        }

        $admin->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password updated successfully!');
    }
}
