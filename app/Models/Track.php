<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'artists',
        'release_type',
        'primary_genre',
        'secondary_genre',
        'audio_file',
        'cover_art',
        'release_date',
        'description',
        'is_explicit',
        'isrc',
        'upc',
        'status',
        'new_streams',
        'total_streams',
        'pre_save_count',
        'platforms',
        'first_name',
        'last_name',
        'stage_name',
        'featuring_artists',
        'authors',
        'composers',
        'producer',
        'is_youtube_beat',
        'has_license',
        'tik_tok_start_time',
        'short_bio',
        'track_description',
        'song_duration',
        'cm_society',
        'siae_position',
        'distribution_details',
        'has_spotify_apple',
        'spotify_link',
        'apple_music_link',
        'tik_tok_link',
        'youtube_link',
        'lyrics',
        'album_title',
        'main_track_title',
        'track_titles',
        'album_tracks',
    ];

    protected $casts = [
        'is_explicit' => 'boolean',
        'is_youtube_beat' => 'boolean',
        'has_license' => 'boolean',
        'platforms' => 'array',
        'track_titles' => 'array',
        'album_tracks' => 'array',
        'release_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function preSaves()
    {
        return $this->hasMany(PreSave::class);
    }

    public function playlistSubmissions()
    {
        return $this->hasMany(PlaylistSubmission::class);
    }

    public function radioPromotions()
    {
        return $this->hasMany(RadioPromotion::class);
    }

    public function isReleased(): bool
    {
        return $this->status === 'Released';
    }

    public function isAlbum(): bool
    {
        return $this->release_type === 'album';
    }

    /**
     * Normalize storage paths from MongoDB format (/uploads/covers/X) to Laravel format (covers/X).
     * Ensures cover art, audio files, and other uploads resolve correctly regardless of source.
     */
    public static function normalizeStoragePath(?string $path): ?string
    {
        if (! $path || ! is_string($path)) {
            return null;
        }
        $path = str_replace('\\', '/', trim($path));
        $path = ltrim($path, '/');
        $path = preg_replace('#^uploads/covers/#', 'covers/', $path);
        $path = preg_replace('#^uploads/tracks/#', 'tracks/', $path);
        return $path ?: null;
    }

    /** Storage path for cover art (normalized for Laravel storage disk). */
    public function getCoverArtStoragePathAttribute(): ?string
    {
        return self::normalizeStoragePath($this->cover_art);
    }

    public function getCoverArtUrlAttribute(): string
    {
        $path = $this->cover_art_storage_path;
        if ($path) {
            return asset('storage/' . $path);
        }
        return asset('images/default-cover.png');
    }

    /** Storage path for audio file (normalized for Laravel storage disk). */
    public function getAudioFileStoragePathAttribute(): ?string
    {
        return self::normalizeStoragePath($this->audio_file);
    }

    public function getAudioUrlAttribute(): ?string
    {
        if ($this->audio_file) {
            return route('files.audio', $this->id);
        }
        return null;
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'Released' => 'green',
            'On Request', 'Modify Pending' => 'blue',
            'On Process', 'Modify Process' => 'yellow',
            'Rejected', 'Modify Rejected' => 'red',
            'Modify Released' => 'emerald',
            default => 'gray',
        };
    }
}
