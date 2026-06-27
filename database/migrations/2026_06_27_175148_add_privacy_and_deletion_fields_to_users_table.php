<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'show_real_name_publicly')) {
                $table->boolean('show_real_name_publicly')
                    ->default(false)
                    ->after('remember_token');
            }

            if (! Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes()->after('show_real_name_publicly');
            }

            if (! Schema::hasColumn('users', 'deleted_by_user_id')) {
                $table->foreignId('deleted_by_user_id')
                    ->nullable()
                    ->after('deleted_at')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('users', 'deleted_reason')) {
                $table->text('deleted_reason')
                    ->nullable()
                    ->after('deleted_by_user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'deleted_by_user_id')) {
                $table->dropForeign(['deleted_by_user_id']);
            }

            $columns = [
                'show_real_name_publicly',
                'deleted_at',
                'deleted_by_user_id',
                'deleted_reason',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};