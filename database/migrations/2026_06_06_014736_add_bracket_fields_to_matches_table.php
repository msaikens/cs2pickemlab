<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->string('bracket_group')->nullable()->after('format');
            $table->string('round_label')->nullable()->after('bracket_group');
            $table->unsignedInteger('bracket_position')->default(0)->after('round_label');
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn([
                'bracket_group',
                'round_label',
                'bracket_position',
            ]);
        });
    }
};
