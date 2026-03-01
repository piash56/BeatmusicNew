<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('radio_networks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cover_image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('radio_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('track_id')->constrained('tracks')->cascadeOnDelete();
            $table->integer('track_index')->nullable();
            $table->foreignId('radio_network_id')->nullable()->constrained('radio_networks')->nullOnDelete();
            $table->enum('status', ['pending', 'published', 'rejected', 'finished'])->default('pending');
            $table->timestamp('request_date')->nullable();
            $table->timestamp('published_date')->nullable();
            $table->timestamp('finish_date')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('likes')->default(0);
            $table->json('liked_by')->nullable();
            $table->json('liked_by_guests')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radio_promotions');
        Schema::dropIfExists('radio_networks');
    }
};
