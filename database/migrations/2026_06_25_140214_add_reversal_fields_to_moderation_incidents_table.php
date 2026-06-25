<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('moderation_incidents', function (Blueprint $table) {
            $table->timestamp('reversed_at')->nullable()->after('resolved_at');

            $table->foreignId('reversed_by_user_id')
                ->nullable()
                ->after('reversed_at')
                ->constrained('users')
                ->nullOnDelete();

            $table->text('reversal_reason')->nullable()->after('reversed_by_user_id');

            $table->index('reversed_at');
            $table->index('reversed_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('moderation_incidents', function (Blueprint $table) {
            $table->dropForeign(['reversed_by_user_id']);
            $table->dropIndex(['reversed_at']);
            $table->dropIndex(['reversed_by_user_id']);

            $table->dropColumn([
                'reversed_at',
                'reversed_by_user_id',
                'reversal_reason',
            ]);
        });
    }
};