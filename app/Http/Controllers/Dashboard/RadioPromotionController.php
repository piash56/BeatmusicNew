<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\RadioNetwork;
use App\Models\RadioPromotion;
use App\Models\Track;
use Illuminate\Http\Request;

class RadioPromotionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user->is_company && $user->subscription === 'Free') {
            return redirect()->route('dashboard.not-eligible');
        }

        $radioNetworks = RadioNetwork::where('is_active', true)->get();
        $promotions = RadioPromotion::where('user_id', $user->id)
            ->with(['track', 'radioNetwork'])
            ->orderByDesc('created_at')
            ->paginate(10);

        $releasedSingles = Track::where('user_id', $user->id)
            ->where('release_type', 'single')
            ->whereIn('status', ['Released', 'Modify Released'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where(function ($q2) use ($s) {
                    $q2->where('title', 'like', "%{$s}%")->orWhere('artists', 'like', "%{$s}%")->orWhere('primary_genre', 'like', "%{$s}%");
                });
            })
            ->orderByDesc('created_at')
            ->get();

        $releasedAlbums = Track::where('user_id', $user->id)
            ->where('release_type', 'album')
            ->whereIn('status', ['Released', 'Modify Released'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where(function ($q2) use ($s) {
                    $q2->where('title', 'like', "%{$s}%")->orWhere('artists', 'like', "%{$s}%")->orWhere('album_title', 'like', "%{$s}%");
                });
            })
            ->orderByDesc('created_at')
            ->get();

        return view('dashboard.radio-promotion.index', compact('user', 'radioNetworks', 'promotions', 'releasedSingles', 'releasedAlbums'));
    }

    public function albumTracks(int $id)
    {
        $user = auth()->user();
        $track = Track::where('user_id', $user->id)
            ->where('release_type', 'album')
            ->whereIn('status', ['Released', 'Modify Released'])
            ->findOrFail($id);

        $albumTracks = $track->album_tracks ?? [];
        $list = [];
        foreach ($albumTracks as $index => $at) {
            $list[] = [
                'track_index' => $index,
                'title' => $at['title'] ?? 'Track ' . ($index + 1),
                'duration' => $at['duration'] ?? null,
                'cover' => $track->cover_art,
                'artists' => $track->artists,
                'album_title' => $track->album_title ?? $track->title,
            ];
        }

        return response()->json(['tracks' => $list]);
    }

    public function submit(Request $request)
    {
        $user = auth()->user();

        if (!$user->is_company && $user->subscription === 'Free') {
            return back()->with('error', 'This feature requires a Premium or Pro subscription.');
        }

        $request->validate([
            'track_id' => 'required|exists:tracks,id',
            'track_index' => 'nullable|integer|min:0',
            'radio_network_id' => 'nullable|exists:radio_networks,id',
        ]);

        $track = Track::where('user_id', $user->id)->findOrFail($request->track_id);

        if (!in_array($track->status, ['Released', 'Modify Released'])) {
            return back()->with('error', 'Only released tracks can be submitted for radio promotion.');
        }

        $trackIndex = $request->has('track_index') ? (int) $request->track_index : null;
        if ($track->release_type === 'album') {
            $albumTracks = $track->album_tracks ?? [];
            if ($trackIndex === null || $trackIndex < 0 || $trackIndex >= count($albumTracks)) {
                return back()->with('error', 'Please select a track from the album.');
            }
        } else {
            $trackIndex = null;
        }

        $exists = RadioPromotion::where('user_id', $user->id)
            ->where('track_id', $track->id)
            ->where(function ($q) use ($trackIndex) {
                if ($trackIndex === null) {
                    $q->whereNull('track_index');
                } else {
                    $q->where('track_index', $trackIndex);
                }
            })
            ->where(function ($q) use ($request) {
                if ($request->filled('radio_network_id')) {
                    $q->where('radio_network_id', $request->radio_network_id);
                } else {
                    $q->whereNull('radio_network_id');
                }
            })
            ->whereIn('status', ['pending', 'published'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'You already have an active promotion request for this track on this radio network.');
        }

        RadioPromotion::create([
            'user_id' => $user->id,
            'track_id' => $track->id,
            'track_index' => $trackIndex,
            'radio_network_id' => $request->radio_network_id,
            'status' => 'pending',
            'request_date' => now(),
        ]);

        return back()->with('success', 'Richiesta di promozione radiofonica inviata.');
    }
}
