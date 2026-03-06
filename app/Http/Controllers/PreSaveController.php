<?php

namespace App\Http\Controllers;

use App\Models\PreSave;
use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PreSaveController extends Controller
{
    private function spotifyHttp()
    {
        return app()->environment('local')
            ? Http::withoutVerifying()
            : Http::baseUrl('https://accounts.spotify.com');
    }

    private function findPublicTrack(int $trackId): Track
    {
        return Track::whereKey($trackId)
            ->where(function ($query) {
                $query->where('status', 'Released')
                    ->orWhere(function ($query) {
                        $query->where('status', 'On Process')
                            ->where('release_date', '>', now());
                    });
            })
            ->firstOrFail();
    }

    public function show(int $trackId)
    {
        $track = $this->findPublicTrack($trackId);

        $alreadySaved = false;
        if (Auth::check()) {
            $alreadySaved = PreSave::where('user_id', Auth::id())
                ->where('track_id', $trackId)
                ->exists();
        }

        return view('presave.show', [
            'track' => $track,
            'alreadySaved' => $alreadySaved,
            'preSaves' => $track->preSaves()->count(),
        ]);
    }

    public function spotifyAuthRedirect(int $trackId)
    {
        $this->findPublicTrack($trackId);

        if (!config('services.spotify.client_id') || !config('services.spotify.redirect')) {
            return redirect()->route('presave.show', $trackId)
                ->with('error', 'Spotify pre-save is not configured yet.');
        }

        $params = http_build_query([
            'client_id' => config('services.spotify.client_id'),
            'response_type' => 'code',
            'redirect_uri' => config('services.spotify.redirect'),
            'scope' => 'user-library-modify user-read-private user-read-email',
            'state' => $trackId,
        ]);

        return redirect('https://accounts.spotify.com/authorize?' . $params);
    }

    public function spotifyCallback(Request $request)
    {
        $code = $request->get('code');
        $trackId = (int) $request->get('state');
        $error = $request->get('error');

        if ($error || !$code) {
            return redirect()->route('presave.show', $trackId)
                ->with('error', 'Spotify authorization was cancelled.');
        }

        try {
            $tokenResponse = $this->spotifyHttp()->asForm()->post('https://accounts.spotify.com/api/token', [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => config('services.spotify.redirect'),
                'client_id' => config('services.spotify.client_id'),
                'client_secret' => config('services.spotify.client_secret'),
            ]);

            $tokenData = $tokenResponse->json();

            if ($tokenResponse->failed() || !isset($tokenData['access_token'])) {
                Log::warning('Spotify token exchange failed.', [
                    'track_id' => $trackId,
                    'status' => $tokenResponse->status(),
                    'body' => $tokenData,
                ]);

                return redirect()->route('presave.show', $trackId)
                    ->with('error', 'Failed to connect with Spotify.');
            }

            $profileResponse = $this->spotifyHttp()->withToken($tokenData['access_token'])
                ->get('https://api.spotify.com/v1/me');
            $profile = $profileResponse->json();

            if ($profileResponse->failed() || !isset($profile['id'])) {
                Log::warning('Spotify profile fetch failed.', [
                    'track_id' => $trackId,
                    'status' => $profileResponse->status(),
                    'body' => $profile,
                ]);

                return redirect()->route('presave.show', $trackId)
                    ->with('error', 'Spotify account data could not be read.');
            }

            $track = $this->findPublicTrack($trackId);

            $existingPreSave = PreSave::where('spotify_user_id', $profile['id'])
                ->where('track_id', $trackId)
                ->where('platform', 'spotify')
                ->first();

            if ($existingPreSave) {
                return redirect()->route('presave.show', $trackId)
                    ->with('info', 'This Spotify account already pre-saved this track. Try another release.');
            }

            $preSave = PreSave::create(
                [
                    'spotify_user_id' => $profile['id'],
                    'track_id' => $trackId,
                    'platform' => 'spotify',
                    'user_id' => Auth::id(),
                    'access_token' => $tokenData['access_token'],
                    'refresh_token' => $tokenData['refresh_token'] ?? null,
                    'token_expires_at' => now()->addSeconds($tokenData['expires_in'] ?? 3600),
                    'status' => 'pending',
                    'user_display_name' => $profile['display_name'] ?? null,
                    'user_email' => $profile['email'] ?? null,
                    'track_title' => $track->title,
                    'artist_name' => $track->artists,
                    'release_date' => $track->release_date,
                    'is_public_pre_save' => !Auth::check(),
                ]
            );

            $track->increment('pre_save_count');

            return redirect()->route('presave.success')
                ->with('presave_success_track_id', $trackId)
                ->with('success', 'Pre-save successful! We\'ll add this track to your Spotify library on release day.');
        } catch (\Exception $e) {
            Log::error('Spotify pre-save callback failed.', [
                'track_id' => $trackId,
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('presave.show', $trackId)
                ->with('error', 'An error occurred. Please try again.');
        }
    }

    public function success()
    {
        $trackId = session('presave_success_track_id');
        $track = $trackId ? Track::find($trackId) : null;

        return view('presave.success', compact('track'));
    }
}
