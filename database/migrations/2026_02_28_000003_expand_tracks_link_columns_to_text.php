<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Expand URL/path columns to TEXT for MongoDB migration.
     * (spotify_link, apple_music_link can be long Instagram redirect URLs.)
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver !== 'mysql') {
            return;
        }

        $columns = [
            'spotify_link',
            'apple_music_link',
            'tik_tok_link',
            'youtube_link',
            'audio_file',
            'cover_art',
            'siae_position',
        ];

        foreach ($columns as $col) {
            DB::statement("ALTER TABLE tracks MODIFY COLUMN {$col} TEXT NULL");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver !== 'mysql') {
            return;
        }

        foreach (['spotify_link', 'apple_music_link', 'tik_tok_link', 'youtube_link', 'audio_file', 'cover_art', 'siae_position'] as $col) {
            DB::statement("ALTER TABLE tracks MODIFY COLUMN {$col} VARCHAR(255) NULL");
        }
    }
};
