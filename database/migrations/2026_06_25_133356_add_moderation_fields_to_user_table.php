<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('site_banned_at')->nullable()->after('remember_token');
            $table->foreignId('site_banned_by_user_id')
                ->nullable()
                ->after('site_banned_at')
                ->constrained('users')
                ->nullOnDelete();

            $table->string('site_ban_incident_number', 32)
                ->nullable()
                ->after('site_banned_by_user_id');

            $table->timestamp('site_suspended_until')
                ->nullable()
                ->after('site_ban_incident_number');

            $table->foreignId('site_suspended_by_user_id')
                ->nullable()
                ->after('site_suspended_until')
                ->constrained('users')
                ->nullOnDelete();

            $table->string('site_suspension_incident_number', 32)
                ->nullable()
                ->after('site_suspended_by_user_id');

            $table->index('site_banned_at');
            $table->index('site_suspended_until');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['site_banned_by_user_id']);
            $table->dropForeign(['site_suspended_by_user_id']);

            $table->dropIndex(['site_banned_at']);
            $table->dropIndex(['site_suspended_until']);

            $table->dropColumn([
                'site_banned_at',
                'site_banned_by_user_id',
                'site_ban_incident_number',
                'site_suspended_until',
                'site_suspended_by_user_id',
                'site_suspension_incident_number',
            ]);
        });
    }
};