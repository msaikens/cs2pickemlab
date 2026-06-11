<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('steam_accounts', function (Blueprint $table) {
            if (! Schema::hasColumn('steam_accounts', 'verification_status')) {
                $table->string('verification_status')->default('verified')->after('profile_visibility');
            }

            if (! Schema::hasColumn('steam_accounts', 'verification_failed_reason')) {
                $table->text('verification_failed_reason')->nullable()->after('verification_status');
            }

            if (! Schema::hasColumn('steam_accounts', 'last_marketplace_sweep_at')) {
                $table->timestamp('last_marketplace_sweep_at')->nullable()->after('last_verified_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('steam_accounts', function (Blueprint $table) {
            if (Schema::hasColumn('steam_accounts', 'last_marketplace_sweep_at')) {
                $table->dropColumn('last_marketplace_sweep_at');
            }

            if (Schema::hasColumn('steam_accounts', 'verification_failed_reason')) {
                $table->dropColumn('verification_failed_reason');
            }

            if (Schema::hasColumn('steam_accounts', 'verification_status')) {
                $table->dropColumn('verification_status');
            }
        });
    }
};