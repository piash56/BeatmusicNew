<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_company')->default(false);
            $table->enum('status', ['active', 'suspended'])->default('active');
            $table->enum('subscription', ['Free', 'Premium', 'Pro'])->default('Free');
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->string('subscription_status')->nullable();
            $table->timestamp('subscription_start_date')->nullable();
            $table->timestamp('subscription_end_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payout_method')->nullable();
            $table->string('paypal_email')->nullable();
            $table->decimal('balance', 10, 2)->default(0);
            $table->boolean('can_upload_tracks')->default(true);
            $table->timestamp('last_active')->nullable();
            $table->timestamp('last_login_time')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->string('current_session_ip')->nullable();
            $table->timestamp('current_session_updated')->nullable();
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->string('otp')->nullable();
            $table->timestamp('otp_expires')->nullable();
            $table->integer('otp_resend_count')->default(0);
            $table->timestamp('last_otp_resend_time')->nullable();
            $table->timestamp('otp_resend_reset_time')->nullable();
            $table->string('reset_password_token')->nullable();
            $table->timestamp('reset_password_expiry')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('preferences_theme')->default('dark');
            $table->json('preferences_favorite_genres')->nullable();
            $table->string('preferences_language')->default('en');
            $table->bigInteger('stats_total_streams')->default(0);
            $table->bigInteger('stats_last_24h')->default(0);
            $table->bigInteger('stats_last_week')->default(0);
            $table->bigInteger('stats_last_month')->default(0);
            $table->bigInteger('stats_last_year')->default(0);
            $table->json('stats_stream_updates')->nullable();
            $table->integer('stats_on_request_tracks')->default(0);
            $table->integer('stats_on_process_tracks')->default(0);
            $table->integer('stats_released_tracks')->default(0);
            $table->integer('stats_rejected_tracks')->default(0);
            $table->integer('stats_playlist_count')->default(0);
            $table->integer('stats_track_count')->default(0);
            $table->integer('stats_follower_count')->default(0);
            $table->integer('stats_following_count')->default(0);
            $table->text('bio')->nullable();
            $table->string('social_facebook')->nullable();
            $table->string('social_twitter')->nullable();
            $table->string('social_instagram')->nullable();
            $table->string('social_website')->nullable();
            // Billing info
            $table->string('billing_full_name')->nullable();
            $table->string('billing_email')->nullable();
            $table->string('billing_address')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_zip_code')->nullable();
            $table->string('billing_country')->nullable();
            $table->timestamp('billing_updated_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
