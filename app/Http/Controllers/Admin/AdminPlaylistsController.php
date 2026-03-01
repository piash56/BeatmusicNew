<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EditorialPlaylist;
use App\Models\PlaylistSubmission;
use Illuminate\Http\Request;

class AdminPlaylistsController extends Controller
{
    public function index(Request $request)
    {
        $query = PlaylistSubmission::with(['user', 'track']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('playlist_name', 'like', "%{$s}%")
                    ->orWhereHas('track', fn ($t) => $t->where('title', 'like', "%{$s}%")->orWhere('artists', 'like', "%{$s}%"))
                    ->orWhereHas('user', fn ($u) => $u->where('full_name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
            });
        }

        $submissions = $query->orderByDesc('created_at')->paginate(20);
        return view('admin.editorial-playlists.index', compact('submissions'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:Waiting,Processing,Published,Rejected',
        ]);

        $submission = PlaylistSubmission::findOrFail($id);
        $submission->update([
            'status' => $request->status,
            'review_note' => $request->review_note,
            'review_date' => now(),
        ]);

        return back()->with('success', 'Playlist submission status updated!');
    }

    public function updateStreams(Request $request, int $id)
    {
        $request->validate([
            'listeners' => 'nullable|integer|min:0',
            'streams' => 'nullable|integer|min:0',
        ]);

        $submission = PlaylistSubmission::findOrFail($id);
        if ($submission->status !== 'Published') {
            return back()->with('error', 'Streams can only be updated for Published submissions.');
        }
        $incrementStreams = (int) $request->input('streams', 0);
        $incrementListeners = (int) $request->input('listeners', 0);

        $submission->streams = (int) ($submission->streams ?? 0) + $incrementStreams;
        $submission->listeners = (int) ($submission->listeners ?? 0) + $incrementListeners;
        $submission->save();
        return back()->with('success', 'Streams/Listeners updated!');
    }

    /** Playlist catalog: list editorial playlists */
    public function catalog(Request $request)
    {
        $query = EditorialPlaylist::query();
        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }
        $playlists = $query->orderBy('platform')->orderBy('sort_order')->orderBy('name')->paginate(30);
        return view('admin.editorial-playlists.catalog', compact('playlists'));
    }

    /** Store new editorial playlist */
    public function storePlaylist(Request $request)
    {
        $request->validate([
            'platform' => 'required|in:Spotify,Apple Music,Amazon Music',
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable',
        ]);

        $exists = EditorialPlaylist::where('platform', $request->platform)->where('name', $request->name)->exists();
        if ($exists) {
            return back()->with('error', 'A playlist with this name already exists for this platform.');
        }

        EditorialPlaylist::create([
            'platform' => $request->platform,
            'name' => $request->name,
            'url' => $request->url,
            'sort_order' => (int) $request->get('sort_order', 0),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Playlist added.');
    }

    /** Update editorial playlist */
    public function updatePlaylist(Request $request, int $id)
    {
        $playlist = EditorialPlaylist::findOrFail($id);
        $request->validate([
            'platform' => 'required|in:Spotify,Apple Music,Amazon Music',
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable',
        ]);

        $exists = EditorialPlaylist::where('platform', $request->platform)
            ->where('name', $request->name)
            ->where('id', '!=', $id)
            ->exists();
        if ($exists) {
            return back()->with('error', 'A playlist with this name already exists for this platform.');
        }

        $playlist->update([
            'platform' => $request->platform,
            'name' => $request->name,
            'url' => $request->url,
            'sort_order' => (int) $request->get('sort_order', 0),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Playlist updated.');
    }

    /** Edit form for editorial playlist */
    public function editPlaylist(int $id)
    {
        $playlist = EditorialPlaylist::findOrFail($id);
        return view('admin.editorial-playlists.catalog-edit', compact('playlist'));
    }

    /** Delete editorial playlist */
    public function destroyPlaylist(int $id)
    {
        $playlist = EditorialPlaylist::findOrFail($id);
        $playlist->delete();
        return back()->with('success', 'Playlist deleted.');
    }
}
