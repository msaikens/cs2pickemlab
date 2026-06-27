<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['trade_requests', 'skin_listings'] as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (! Schema::hasColumn($tableName, 'supervisor_user_id')) {
                    $table->foreignId('supervisor_user_id')
                        ->nullable()
                        ->constrained('users')
                        ->nullOnDelete();
                }

                if (! Schema::hasColumn($tableName, 'supervisor_assigned_at')) {
                    $table->timestamp('supervisor_assigned_at')->nullable();
                }

                if (! Schema::hasColumn($tableName, 'supervisor_note')) {
                    $table->text('supervisor_note')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['trade_requests', 'skin_listings'] as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'supervisor_user_id')) {
                    $table->dropForeign(['supervisor_user_id']);
                }

                foreach (['supervisor_user_id', 'supervisor_assigned_at', 'supervisor_note'] as $column) {
                    if (Schema::hasColumn($tableName, $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};