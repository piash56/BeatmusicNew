<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add indexes for admin list pages that filter/sort by release_type, status, created_at.
     * Speeds up: Track submissions, Album submissions, Radio Requests, Streams management.
     */
    public function up(): void
    {
        Schema::table('tracks', function (Blueprint $table) {
            $table->index(['release_type', 'status', 'created_at'], 'tracks_release_status_created_index');
            $table->index(['status', 'created_at'], 'tracks_status_created_index');
        });

        Schema::table('radio_promotions', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'radio_promotions_status_created_index');
        });
    }

    public function down(): void
    {
        Schema::table('tracks', function (Blueprint $table) {
            $table->dropIndex('tracks_release_status_created_index');
            $table->dropIndex('tracks_status_created_index');
        });

        Schema::table('radio_promotions', function (Blueprint $table) {
            $table->dropIndex('radio_promotions_status_created_index');
        });
    }
};
