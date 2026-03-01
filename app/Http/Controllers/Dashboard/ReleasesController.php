<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReleasesController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Track::where('user_id', $user->id);

        $tab = $request->get('tab', 'all');

        if ($tab === 'single') {
            $query->where('release_type', 'single');
        } elseif ($tab === 'album') {
            $query->where('release_type', 'album');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tracks = $query->orderByDesc('created_at')->paginate(10);

        return view('dashboard.releases.index', compact('tracks', 'tab', 'user'));
    }

    public function show(int $id)
    {
        $user = auth()->user();
        $track = Track::where('user_id', $user->id)->findOrFail($id);
        $preSaves = $track->preSaves()->count();
        return view('dashboard.releases.show', compact('track', 'preSaves', 'user'));
    }

    public function create()
    {
        $clearDraftIfSubmittedAfter = session()->pull('last_release_submitted_at');
        return view('dashboard.releases.create', compact('clearDraftIfSubmittedAfter'));
    }

    /** Generate filename in old system format: audioFile-{ts}-{rand}.ext or albumTrack_N-{ts}-{rand}.ext */
    private function audioFilename(string $ext, ?string $type = 'single', ?int $index = null): string
    {
        $ts = (string) (round(microtime(true) * 1000));
        $rand = (string) random_int(100000000, 999999999);
        if ($type === 'album' && $index !== null) {
            return "albumTrack_{$index}-{$ts}-{$rand}.{$ext}";
        }
        return "audioFile-{$ts}-{$rand}.{$ext}";
    }

    /** Generate filename in old system format: coverArt-{ts}-{rand}.ext */
    private function coverFilename(string $ext): string
    {
        $ts = (string) (round(microtime(true) * 1000));
        $rand = (string) random_int(100000000, 999999999);
        return "coverArt-{$ts}-{$rand}.{$ext}";
    }

    public function uploadAudio(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:mp3,wav,flac,aac,ogg|max:204800',
        ]);

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension() ?: 'mp3');
        $type = $request->input('type', 'single');
        $index = $request->has('index') ? (int) $request->input('index') : null;
        $name = $this->audioFilename($ext, $type, ($type === 'album' ? $index : null));

        $path = $file->storeAs('tracks', $name, 'public');

        return response()->json(['path' => $path]);
    }

    public function uploadCover(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,jpg,png|max:5120',
        ]);

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $name = $this->coverFilename($ext);
        $path = $file->storeAs('covers', $name, 'public');

        return response()->json(['path' => $path]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'release_type' => 'required|in:single,album',
            'title' => 'required|string|max:255',
            'artists' => 'required|string|max:255',
            'primary_genre' => 'required|string|in:Pop,Hip-Hop,R&B,Electronic,Rock,Alternative,Jazz,Classical,Country,Latin,Folk,Reggae',
            'release_date' => 'required|date',
            'cover_art' => 'required_without:cover_art_path|nullable|image|mimes:jpeg,jpg,png|max:5120',
            'cover_art_path' => 'required_without:cover_art|nullable|string|max:500',
            'lyrics' => 'required|string',
        ];

        if ($request->release_type === 'single') {
            $rules['audio_file'] = 'required_without:audio_file_path|nullable|file|mimes:mp3,wav,flac,aac,ogg|max:204800';
            $rules['audio_file_path'] = 'required_without:audio_file|nullable|string|max:500';
        }

        if ($request->release_type === 'album') {
            $rules['track_titles'] = 'required|array|min:2';
            $rules['track_titles.*'] = 'nullable|string|max:255';
            $rules['album_tracks'] = 'required_without:album_track_paths|nullable|array';
            $rules['album_tracks.*'] = 'nullable|file|mimes:mp3,wav,flac,aac,ogg|max:204800';
            $rules['album_track_paths'] = 'required_without:album_tracks|nullable|array|min:2';
            $rules['album_track_paths.*'] = 'nullable|string|max:500';
        }

        if ($request->input('has_spotify_apple') === 'YES') {
            $rules['spotify_link'] = 'required|url';
            $rules['apple_music_link'] = 'required|url';
        }

        $request->validate($rules);

        if ($request->release_type === 'album' && $request->filled('album_track_paths')) {
            $paths = array_filter((array) $request->album_track_paths);
            if (count($paths) < 2) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'At least 2 tracks are required.', 'errors' => ['album_tracks' => ['At least 2 tracks are required.']]], 422);
                }
                return back()->withErrors(['album_tracks' => ['At least 2 tracks are required.']])->withInput();
            }
        }

        $data = $request->only([
            'release_type', 'title', 'artists', 'primary_genre', 'secondary_genre',
            'release_date', 'description', 'is_explicit', 'isrc', 'upc',
            'first_name', 'last_name', 'stage_name', 'featuring_artists',
            'authors', 'composers', 'producer', 'is_youtube_beat', 'has_license',
            'tik_tok_start_time', 'short_bio', 'track_description', 'song_duration',
            'cm_society', 'siae_position', 'distribution_details', 'has_spotify_apple',
            'spotify_link', 'apple_music_link', 'tik_tok_link', 'youtube_link',
            'lyrics', 'album_title', 'main_track_title',
        ]);

        $data['user_id'] = $user->id;
        $data['status'] = 'On Request';
        $data['is_explicit'] = $request->boolean('is_explicit');
        $data['is_youtube_beat'] = $request->boolean('is_youtube_beat');
        $data['has_license'] = $request->boolean('has_license');

        // Handle cover art: pre-uploaded path or file (coverArt-{ts}-{rand}.ext)
        if ($request->filled('cover_art_path')) {
            $data['cover_art'] = $request->cover_art_path;
        } elseif ($request->hasFile('cover_art')) {
            $file = $request->file('cover_art');
            $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
            $data['cover_art'] = $file->storeAs('covers', $this->coverFilename($ext), 'public');
        }

        // Handle single audio: pre-uploaded path or file (audioFile-{ts}-{rand}.ext)
        if ($request->release_type === 'single') {
            if ($request->filled('audio_file_path')) {
                $data['audio_file'] = $request->audio_file_path;
            } elseif ($request->hasFile('audio_file')) {
                $file = $request->file('audio_file');
                $ext = strtolower($file->getClientOriginalExtension() ?: 'mp3');
                $data['audio_file'] = $file->storeAs('tracks', $this->audioFilename($ext, 'single'), 'public');
            }
        }

        // Handle album tracks: pre-uploaded paths or files (albumTrack_N-{ts}-{rand}.ext)
        if ($request->release_type === 'album') {
            $trackTitles = $request->input('track_titles', []);
            $data['track_titles'] = $trackTitles;
            $albumTracks = [];

            if ($request->filled('album_track_paths') && is_array($request->album_track_paths)) {
                foreach ($request->album_track_paths as $index => $path) {
                    if ($path) {
                        $albumTracks[] = [
                            'title' => $trackTitles[$index] ?? 'Track ' . ($index + 1),
                            'audio_file' => $path,
                            'duration' => null,
                            'order' => $index,
                        ];
                    }
                }
            } elseif ($request->hasFile('album_tracks')) {
                foreach ($request->file('album_tracks') as $index => $audioFile) {
                    if ($audioFile && $audioFile->isValid()) {
                        $ext = strtolower($audioFile->getClientOriginalExtension() ?: 'mp3');
                        $audioPath = $audioFile->storeAs('tracks', $this->audioFilename($ext, 'album', $index), 'public');
                        $albumTracks[] = [
                            'title' => $trackTitles[$index] ?? 'Track ' . ($index + 1),
                            'audio_file' => $audioPath,
                            'duration' => null,
                            'order' => $index,
                        ];
                    }
                }
            }
            $data['album_tracks'] = $albumTracks;
        }

        $track = Track::create($data);

        // Update user stats
        $user->increment('stats_track_count');
        $user->increment('stats_on_request_tracks');

        // So the create page can clear any draft from before this submit (don't restore after successful upload)
        session(['last_release_submitted_at' => round(microtime(true) * 1000)]);

        $redirectUrl = route('dashboard.releases.index');
        if ($request->expectsJson()) {
            return response()->json(['redirect' => $redirectUrl, 'message' => 'Release submitted successfully! We will review it shortly.']);
        }
        return redirect()->route('dashboard.releases.show', $track->id)
            ->with('success', 'Release submitted successfully! We will review it shortly.');
    }

    public function edit(int $id)
    {
        $user = auth()->user();
        $track = Track::where('user_id', $user->id)->findOrFail($id);
        if ($track->status === 'Modify Pending') {
            return redirect()->route('dashboard.releases.show', $track->id)
                ->with('error', 'This release is pending review and cannot be edited. Please wait until it has been processed.');
        }
        return view('dashboard.releases.edit', compact('track'));
    }

    public function update(Request $request, int $id)
    {
        $user = auth()->user();
        $track = Track::where('user_id', $user->id)->findOrFail($id);

        $data = $request->only([
            'title', 'artists', 'primary_genre', 'secondary_genre',
            'release_date', 'description', 'is_explicit', 'isrc', 'upc',
            'first_name', 'last_name', 'stage_name', 'featuring_artists',
            'authors', 'composers', 'producer', 'is_youtube_beat', 'has_license',
            'tik_tok_start_time', 'short_bio', 'track_description', 'song_duration',
            'cm_society', 'siae_position', 'distribution_details', 'has_spotify_apple',
            'spotify_link', 'apple_music_link', 'tik_tok_link', 'youtube_link',
            'lyrics', 'album_title', 'main_track_title',
        ]);

        $data['is_explicit'] = $request->boolean('is_explicit');
        $data['is_youtube_beat'] = $request->boolean('is_youtube_beat');
        $data['has_license'] = $request->boolean('is_youtube_beat') && $request->boolean('has_license');

        // Collecting societies: clear SIAE position when SOUNDREEF or NONE is selected
        $data['siae_position'] = $request->input('cm_society') === 'SIAE' ? $request->input('siae_position') : null;

        // Any edit sets status to Modify Pending for review
        $data['status'] = 'Modify Pending';

        // Cover: pre-uploaded path or new file (coverArt-{ts}-{rand}.ext)
        if ($request->filled('cover_art_path')) {
            if ($track->cover_art) Storage::disk('public')->delete($track->cover_art);
            $data['cover_art'] = $request->cover_art_path;
        } elseif ($request->hasFile('cover_art')) {
            $request->validate(['cover_art' => 'image|mimes:jpeg,jpg,png|max:5120']);
            if ($track->cover_art) Storage::disk('public')->delete($track->cover_art);
            $file = $request->file('cover_art');
            $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
            $data['cover_art'] = $file->storeAs('covers', $this->coverFilename($ext), 'public');
        }

        // Audio (single): pre-uploaded path or new file (audioFile-{ts}-{rand}.ext)
        if ($track->release_type === 'single') {
            if ($request->filled('audio_file_path')) {
                if ($track->audio_file) Storage::disk('public')->delete($track->audio_file);
                $data['audio_file'] = $request->audio_file_path;
            } elseif ($request->hasFile('audio_file')) {
                $request->validate(['audio_file' => 'file|mimes:mp3,wav,flac,aac,ogg|max:204800']);
                if ($track->audio_file) Storage::disk('public')->delete($track->audio_file);
                $file = $request->file('audio_file');
                $ext = strtolower($file->getClientOriginalExtension() ?: 'mp3');
                $data['audio_file'] = $file->storeAs('tracks', $this->audioFilename($ext, 'single'), 'public');
            }
        }

        $track->update($data);

        return redirect()->route('dashboard.releases.show', $track->id)
            ->with('success', 'Release updated successfully!');
    }
}
