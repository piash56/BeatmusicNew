<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\VevoAccount;
use Illuminate\Http\Request;

class VevoController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (!$user->is_company && $user->subscription === 'Free') {
            return redirect()->route('dashboard.not-eligible');
        }

        $accounts = VevoAccount::where('user_id', $user->id)->latest()->get();

        return view('dashboard.vevo.index', compact('user', 'accounts'));
    }

    public function submit(Request $request)
    {
        $user = auth()->user();

        if (!$user->is_company && $user->subscription === 'Free') {
            return back()->with('error', 'This feature requires a Premium or Pro subscription.');
        }

        $request->validate([
            'artist_name' => 'required|string|max:255',
            'contact_email' => 'required|email',
            'telephone' => 'nullable|string|max:50',
            'release_name' => 'nullable|string|max:255',
            'biography' => 'required|string|min:50',
        ]);

        if (!$user->is_company) {
            $exists = VevoAccount::where('user_id', $user->id)->exists();
            if ($exists) {
                return back()->with('error', "You can't request more than one Vevo account. You already have a request.");
            }
        }

        VevoAccount::create([
            'user_id' => $user->id,
            'artist_name' => $request->artist_name,
            'contact_email' => $request->contact_email,
            'telephone' => $request->telephone,
            'release_name' => $request->release_name,
            'biography' => $request->biography,
            'status' => 'Pending',
        ]);

        return back()->with('success', 'Richiedi Account VEVO inviata. We will review it and get back to you.');
    }
}
