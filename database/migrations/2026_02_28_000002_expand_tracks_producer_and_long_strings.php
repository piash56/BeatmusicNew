<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Expand more VARCHAR(255) columns to TEXT for MongoDB migration.
     * (producer caused truncation; title, artists, etc. can be long in albums.)
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver !== 'mysql') {
            return;
        }

        $columns = [
            'producer',
            'title',
            'artists',
            'secondary_genre',
            'album_title',
            'main_track_title',
        ];

        foreach ($columns as $col) {
            DB::statement("ALTER TABLE tracks MODIFY COLUMN {$col} TEXT NULL");
        }
        // title and artists are NOT NULL in schema; keep them TEXT, allow NULL for safety during import
        // If your app requires NOT NULL, run a separate migration after import to revert.
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver !== 'mysql') {
            return;
        }

        DB::statement('ALTER TABLE tracks MODIFY COLUMN producer VARCHAR(255) NULL');
        DB::statement('ALTER TABLE tracks MODIFY COLUMN title VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE tracks MODIFY COLUMN artists VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE tracks MODIFY COLUMN secondary_genre VARCHAR(255) NULL');
        DB::statement('ALTER TABLE tracks MODIFY COLUMN album_title VARCHAR(255) NULL');
        DB::statement('ALTER TABLE tracks MODIFY COLUMN main_track_title VARCHAR(255) NULL');
    }
};
