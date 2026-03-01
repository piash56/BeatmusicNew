<?php

namespace App\Http\Controllers;

use App\Models\PreSave;
use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PreSaveController extends Controller
{
    public function show(int $trackId)
    {
        $track = Track::where('status', 'Released')
            ->orWhere(function ($q) {
                $q->where('status', 'On Process')
                    ->where('release_date', '>', now());
            })
            ->findOrFail($trackId);

        $alreadySaved = false;
        if (auth()->check()) {
            $alreadySaved = PreSave::where('user_id', auth()->id())
                ->where('track_id', $trackId)
                ->exists();
        }

        return view('presave.show', compact('track', 'alreadySaved'));
    }

    public function spotifyAuthRedirect(int $trackId)
    {
        $track = Track::findOrFail($trackId);

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
        $trackId = $request->get('state');
        $error = $request->get('error');

        if ($error || !$code) {
            return redirect()->route('presave.show', $trackId)
                ->with('error', 'Spotify authorization was cancelled.');
        }

        try {
            $tokenResponse = Http::asForm()->post('https://accounts.spotify.com/api/token', [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => config('services.spotify.redirect'),
                'client_id' => config('services.spotify.client_id'),
                'client_secret' => config('services.spotify.client_secret'),
            ]);

            $tokenData = $tokenResponse->json();

            if (isset($tokenData['error'])) {
                return redirect()->route('presave.show', $trackId)
                    ->with('error', 'Failed to connect with Spotify.');
            }

            // Get user profile
            $profileResponse = Http::withToken($tokenData['access_token'])
                ->get('https://api.spotify.com/v1/me');
            $profile = $profileResponse->json();

            $track = Track::findOrFail($trackId);

            PreSave::updateOrCreate(
                [
                    'spotify_user_id' => $profile['id'],
                    'track_id' => $trackId,
                    'platform' => 'spotify',
                ],
                [
                    'user_id' => auth()->id(),
                    'access_token' => $tokenData['access_token'],
                    'refresh_token' => $tokenData['refresh_token'] ?? null,
                    'token_expires_at' => now()->addSeconds($tokenData['expires_in'] ?? 3600),
                    'status' => 'pending',
                    'user_display_name' => $profile['display_name'] ?? null,
                    'user_email' => $profile['email'] ?? null,
                    'track_title' => $track->title,
                    'artist_name' => $track->artists,
                    'release_date' => $track->release_date,
                    'is_public_pre_save' => !auth()->check(),
                ]
            );

            $track->increment('pre_save_count');

            return redirect()->route('presave.success')
                ->with('success', 'Pre-save successful! We\'ll add this track to your Spotify library on release day.');
        } catch (\Exception $e) {
            return redirect()->route('presave.show', $trackId)
                ->with('error', 'An error occurred. Please try again.');
        }
    }

    public function success()
    {
        return view('presave.success');
    }
}
