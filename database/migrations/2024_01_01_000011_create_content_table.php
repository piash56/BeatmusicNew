<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->text('answer');
            $table->string('category')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('title')->nullable();
            $table->text('feedback');
            $table->tinyInteger('rating')->default(5);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->json('display_on')->nullable(); // ['home', 'about']
            $table->string('profile_picture')->nullable();
            $table->timestamps();
        });

        Schema::create('knowledge_bases', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('content');
            $table->text('excerpt')->nullable();
            $table->string('category')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->json('tags')->nullable();
            $table->bigInteger('views')->default(0);
            $table->integer('likes')->default(0);
            $table->integer('dislikes')->default(0);
            $table->boolean('featured')->default(false);
            $table->timestamp('last_updated')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_title')->default('Beat Music');
            $table->string('logo_url')->nullable();
            $table->string('logo_alt')->default('Beat Music');
            $table->string('favicon')->nullable();
            $table->text('footer_text')->nullable();
            $table->string('copyright_text')->nullable();
            $table->json('social_links')->nullable();
            $table->json('footer_links_1')->nullable();
            $table->json('footer_links_2')->nullable();
            $table->json('copyright_links')->nullable();
            $table->foreignId('last_updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('knowledge_bases');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('faqs');
    }
};
