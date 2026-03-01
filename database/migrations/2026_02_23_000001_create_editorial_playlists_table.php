<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('editorial_playlists', function (Blueprint $table) {
            $table->id();
            $table->string('platform'); // Spotify, Apple Music, Amazon Music
            $table->string('name');
            $table->string('url');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('platform');
            $table->unique(['platform', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('editorial_playlists');
    }
};
