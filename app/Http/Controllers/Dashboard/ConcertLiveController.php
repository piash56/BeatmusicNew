<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ConcertLive;
use App\Models\ConcertLiveRequest;
use Illuminate\Http\Request;

class ConcertLiveController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (!$user->is_company && $user->subscription === 'Free') {
            return redirect()->route('dashboard.not-eligible');
        }

        $concerts = ConcertLive::where('is_active', true)
            ->where('concert_date', '>=', now())
            ->orderBy('concert_date')
            ->get();
        $myRequests = ConcertLiveRequest::where('user_id', $user->id)
            ->with('concertLive')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('dashboard.concert-live.index', compact('user', 'concerts', 'myRequests'));
    }

    public function request(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'concert_live_id' => 'required|exists:concert_lives,id',
            'artist_name' => 'required|string|max:100',
        ]);

        $concert = ConcertLive::findOrFail($request->concert_live_id);

        if ($concert->slots_remaining <= 0) {
            return back()->with('error', 'No slots available for this concert.');
        }

        if (!$concert->is_active || $concert->concert_date < now()->startOfDay()) {
            return back()->with('error', 'This concert is not available for requests.');
        }
        if ($concert->slots_remaining <= 0) {
            return back()->with('error', 'No slots available for this concert.');
        }

        $existing = ConcertLiveRequest::where('user_id', $user->id)
            ->where('concert_live_id', $concert->id)
            ->where('is_active', true)
            ->first();

        if ($existing) {
            return back()->with('error', 'You already have a request for this concert.');
        }

        ConcertLiveRequest::create([
            'user_id' => $user->id,
            'concert_live_id' => $concert->id,
            'artist_name' => $request->artist_name,
            'status' => 'pending',
            'request_date' => now(),
        ]);

        return back()->with('success', 'Concert live slot request submitted!');
    }
}
