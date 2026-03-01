<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vevo_accounts', function (Blueprint $table) {
            $table->string('telephone')->nullable()->change();
            $table->string('release_name')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('vevo_accounts', function (Blueprint $table) {
            $table->string('telephone')->nullable(false)->change();
            $table->string('release_name')->nullable(false)->change();
        });
    }
};
