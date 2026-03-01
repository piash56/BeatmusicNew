<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('playlist_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('track_id')->constrained('tracks')->cascadeOnDelete();
            $table->string('platform'); // Spotify, Apple Music, Amazon Music
            $table->string('playlist_name');
            $table->string('playlist_url')->nullable();
            $table->enum('status', ['Waiting', 'Processing', 'Published', 'Rejected'])->default('Waiting');
            $table->timestamp('submission_date')->nullable();
            $table->timestamp('review_date')->nullable();
            $table->text('review_note')->nullable();
            $table->bigInteger('listeners')->default(0);
            $table->bigInteger('streams')->default(0);
            $table->timestamps();
        });

        Schema::create('user_playlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('platform')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(true);
            $table->string('playlist_url')->nullable();
            $table->bigInteger('followers')->default(0);
            $table->integer('total_tracks')->default(0);
            $table->string('cover_image')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_playlists');
        Schema::dropIfExists('playlist_submissions');
    }
};
