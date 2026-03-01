<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Free, Premium, Pro
            $table->text('description')->nullable();
            $table->json('features')->nullable();
            $table->decimal('price_monthly', 10, 2)->default(0);
            $table->decimal('price_yearly', 10, 2)->default(0);
            $table->string('stripe_monthly_price_id')->nullable();
            $table->string('stripe_yearly_price_id')->nullable();
            $table->string('stripe_product_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained('pricing_plans')->nullOnDelete();
            $table->string('plan_name');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->string('status')->default('active'); // active, cancelled, expired, past_due
            $table->string('payment_method')->nullable(); // stripe, paypal
            $table->string('stripe_subscription_id')->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->string('paypal_subscription_id')->nullable();
            $table->string('billing_cycle')->nullable(); // monthly, yearly
            $table->decimal('amount', 10, 2)->default(0);
            $table->integer('renewal_attempts')->default(0);
            $table->timestamp('last_renewal_attempt')->nullable();
            $table->timestamp('next_renewal_date')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // stripe, paypal
            $table->string('payment_method_id')->nullable();
            $table->boolean('is_default')->default(false);
            $table->string('last4')->nullable();
            $table->string('brand')->nullable();
            $table->string('expiry_date')->nullable();
            $table->string('paypal_email')->nullable();
            $table->timestamps();
        });

        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('paypal_email');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid'])->default('pending');
            $table->timestamp('request_date')->nullable();
            $table->timestamp('paid_date')->nullable();
            $table->string('user_full_name')->nullable();
            $table->string('user_email')->nullable();
            $table->json('payout_stats')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('discount_amount', 10, 2);
            $table->integer('max_uses')->nullable();
            $table->integer('used_count')->default(0);
            $table->timestamp('expiration_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('specific_user')->nullable()->constrained('users')->nullOnDelete();
            $table->string('subscription_plan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('payouts');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('pricing_plans');
    }
};
