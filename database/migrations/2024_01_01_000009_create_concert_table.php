<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('concert_lives', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('city');
            $table->date('concert_date');
            $table->integer('slots_available')->default(0);
            $table->integer('slots_booked')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('concert_live_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('concert_live_id')->constrained('concert_lives')->cascadeOnDelete();
            $table->string('artist_name');
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'finished'])->default('pending');
            $table->timestamp('request_date')->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('concert_live_requests');
        Schema::dropIfExists('concert_lives');
    }
};
