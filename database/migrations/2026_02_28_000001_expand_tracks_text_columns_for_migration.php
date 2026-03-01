<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Expand VARCHAR(255) columns to TEXT so MongoDB migration does not truncate
     * long values (featuring_artists, authors, composers, isrc, upc).
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        $columns = ['featuring_artists', 'authors', 'composers', 'isrc', 'upc'];

        if ($driver === 'mysql') {
            foreach ($columns as $col) {
                DB::statement("ALTER TABLE tracks MODIFY COLUMN {$col} TEXT NULL");
            }
        } elseif ($driver === 'sqlite') {
            // SQLite does not support MODIFY; VARCHAR(255) already allows long text in practice
            // so we skip. For MySQL/MariaDB the above applies.
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE tracks MODIFY COLUMN featuring_artists VARCHAR(255) NULL');
            DB::statement('ALTER TABLE tracks MODIFY COLUMN authors VARCHAR(255) NULL');
            DB::statement('ALTER TABLE tracks MODIFY COLUMN composers VARCHAR(255) NULL');
            DB::statement('ALTER TABLE tracks MODIFY COLUMN isrc VARCHAR(255) NULL');
            DB::statement('ALTER TABLE tracks MODIFY COLUMN upc VARCHAR(255) NULL');
        }
    }
};
