<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('artists');
            $table->enum('release_type', ['single', 'album'])->default('single');
            $table->enum('primary_genre', ['Pop', 'Hip-Hop', 'R&B', 'Electronic', 'Rock', 'Alternative', 'Jazz', 'Classical', 'Country', 'Latin', 'Folk', 'Reggae'])->nullable();
            $table->string('secondary_genre')->nullable();
            $table->string('audio_file')->nullable();
            $table->string('cover_art')->nullable();
            $table->date('release_date')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_explicit')->default(false);
            $table->string('isrc')->nullable();
            $table->string('upc')->nullable();
            $table->enum('status', ['Draft', 'On Request', 'On Process', 'Released', 'Rejected', 'Modify Pending', 'Modify Process', 'Modify Released', 'Modify Rejected'])->default('Draft');
            $table->bigInteger('new_streams')->default(0);
            $table->bigInteger('total_streams')->default(0);
            $table->integer('pre_save_count')->default(0);
            $table->json('platforms')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('stage_name')->nullable();
            $table->string('featuring_artists')->nullable();
            $table->string('authors')->nullable();
            $table->string('composers')->nullable();
            $table->string('producer')->nullable();
            $table->boolean('is_youtube_beat')->default(false);
            $table->boolean('has_license')->default(false);
            $table->string('tik_tok_start_time')->nullable();
            $table->text('short_bio')->nullable();
            $table->text('track_description')->nullable();
            $table->string('song_duration')->nullable();
            $table->enum('cm_society', ['SIAE', 'SOUNDREEF', 'NONE'])->default('NONE');
            $table->string('siae_position')->nullable();
            $table->text('distribution_details')->nullable();
            $table->enum('has_spotify_apple', ['YES', 'NO'])->default('NO');
            $table->string('spotify_link')->nullable();
            $table->string('apple_music_link')->nullable();
            $table->string('tik_tok_link')->nullable();
            $table->string('youtube_link')->nullable();
            $table->text('lyrics')->nullable();
            $table->string('album_title')->nullable();
            $table->string('main_track_title')->nullable();
            $table->json('track_titles')->nullable();
            $table->json('album_tracks')->nullable(); // [{title, audio_file, duration, order}]
            $table->timestamps();

            $table->index(['user_id', 'release_type']);
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracks');
    }
};
