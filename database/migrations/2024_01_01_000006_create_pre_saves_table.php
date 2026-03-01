<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pre_saves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('track_id')->constrained('tracks')->cascadeOnDelete();
            $table->string('platform')->default('spotify'); // spotify, apple_music, youtube_music, deezer
            $table->string('spotify_user_id')->nullable();
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->string('status')->default('pending'); // pending, processed, failed
            $table->string('spotify_track_id')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->string('user_display_name')->nullable();
            $table->string('user_email')->nullable();
            $table->string('track_title')->nullable();
            $table->string('artist_name')->nullable();
            $table->date('release_date')->nullable();
            $table->boolean('is_public_pre_save')->default(false);
            $table->timestamps();

            $table->unique(['spotify_user_id', 'track_id', 'platform'], 'unique_public_presave');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pre_saves');
    }
};
