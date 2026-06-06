<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'avatar_url')) {
                $table->string('avatar_url')->nullable()->after('password');
            }

            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('user')->after('avatar_url');
            }

            if (! Schema::hasColumn('users', 'subscription_status')) {
                $table->string('subscription_status')->default('none')->after('role');
            }

            if (! Schema::hasColumn('users', 'subscription_ends_at')) {
                $table->timestamp('subscription_ends_at')->nullable()->after('subscription_status');
            }
        });

        DB::statement('ALTER TABLE users MODIFY name VARCHAR(255) NULL');
        DB::statement('ALTER TABLE users MODIFY password VARCHAR(255) NULL');
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'subscription_ends_at')) {
                $table->dropColumn('subscription_ends_at');
            }

            if (Schema::hasColumn('users', 'subscription_status')) {
                $table->dropColumn('subscription_status');
            }

            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }

            if (Schema::hasColumn('users', 'avatar_url')) {
                $table->dropColumn('avatar_url');
            }
        });

        DB::statement('ALTER TABLE users MODIFY name VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE users MODIFY password VARCHAR(255) NOT NULL');
    }
};
