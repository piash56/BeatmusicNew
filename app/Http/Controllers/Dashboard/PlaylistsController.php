<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\EditorialPlaylist;
use App\Models\PlaylistSubmission;
use App\Models\Track;
use Illuminate\Http\Request;

class PlaylistsController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user->is_company && $user->subscription === 'Free') {
            return redirect()->route('dashboard.not-eligible');
        }

        $statusFilter = $request->get('status');
        $submissionsQuery = PlaylistSubmission::where('user_id', $user->id)->with('track');
        if ($statusFilter && in_array($statusFilter, ['Waiting', 'Processing', 'Published', 'Rejected'])) {
            $submissionsQuery->where('status', $statusFilter);
        }
        $submissions = $submissionsQuery->latest()->paginate(10);

        $waiting = PlaylistSubmission::where('user_id', $user->id)->where('status', 'Waiting')->with('track')->latest()->get();
        $processing = PlaylistSubmission::where('user_id', $user->id)->where('status', 'Processing')->with('track')->latest()->get();
        $published = PlaylistSubmission::where('user_id', $user->id)->where('status', 'Published')->with('track')->latest()->get();
        $rejected = PlaylistSubmission::where('user_id', $user->id)->where('status', 'Rejected')->with('track')->latest()->get();

        $releasedTracks = Track::where('user_id', $user->id)
            ->whereIn('status', ['Released', 'Modify Released'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where(function ($q2) use ($s) {
                    $q2->where('title', 'like', "%{$s}%")
                        ->orWhere('artists', 'like', "%{$s}%")
                        ->orWhere('primary_genre', 'like', "%{$s}%");
                });
            })
            ->orderByDesc('created_at')
            ->limit(500)
            ->get();

        $platforms = EditorialPlaylist::PLATFORMS;
        $playlistsByPlatform = [];
        foreach ($platforms as $platform) {
            $playlistsByPlatform[$platform] = EditorialPlaylist::where('platform', $platform)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
                ->map(fn ($p) => ['name' => $p->name, 'url' => $p->url])
                ->toArray();
        }

        return view('dashboard.playlists.index', compact(
            'user', 'waiting', 'processing', 'published', 'rejected', 'releasedTracks', 'platforms', 'playlistsByPlatform', 'submissions'
        ));
    }

    public function submit(Request $request)
    {
        $user = auth()->user();

        if (!$user->is_company && $user->subscription === 'Free') {
            return back()->with('error', 'This feature requires a Premium or Pro subscription.');
        }

        $request->validate([
            'track_id' => 'required|exists:tracks,id',
            'platform' => 'required|in:Spotify,Apple Music,Amazon Music',
            'playlist_name' => 'required|string|max:255',
        ]);

        $track = Track::where('user_id', $user->id)
            ->whereIn('status', ['Released', 'Modify Released'])
            ->findOrFail($request->track_id);

        $editorial = EditorialPlaylist::where('platform', $request->platform)
            ->where('name', $request->playlist_name)
            ->where('is_active', true)
            ->first();

        if (!$editorial) {
            return back()->with('error', 'Selected playlist is not available. Please choose from the list.');
        }

        $exists = PlaylistSubmission::where('user_id', $user->id)
            ->where('track_id', $track->id)
            ->where('platform', $request->platform)
            ->where('playlist_name', $request->playlist_name)
            ->exists();

        if ($exists) {
            return back()->with('error', 'You have already submitted this track to this playlist.');
        }

        PlaylistSubmission::create([
            'user_id' => $user->id,
            'track_id' => $track->id,
            'platform' => $request->platform,
            'playlist_name' => $editorial->name,
            'playlist_url' => $editorial->url,
            'status' => 'Waiting',
            'submission_date' => now(),
        ]);

        $user->increment('stats_playlist_count');

        return back()->with('success', 'Inviato! La tua richiesta è in attesa di revisione.');
    }
}
