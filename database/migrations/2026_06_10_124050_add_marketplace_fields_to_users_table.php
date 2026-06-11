<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('active')->after('password');
            }

            if (! Schema::hasColumn('users', 'marketplace_terms_accepted_at')) {
                $table->timestamp('marketplace_terms_accepted_at')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'marketplace_terms_accepted_at')) {
                $table->dropColumn('marketplace_terms_accepted_at');
            }

            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};