<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('grid_id')->nullable()->after('id');
            $table->string('hltv_id')->nullable()->after('grid_id');
            $table->unsignedInteger('world_rank')->nullable()->after('picklab_rating');
            $table->decimal('ranking_points', 8, 2)->nullable()->after('world_rank');

            $table->index('grid_id');
            $table->index('hltv_id');
            $table->index('world_rank');
        });

        Schema::table('players', function (Blueprint $table) {
            $table->string('grid_id')->nullable()->after('id');
            $table->string('hltv_id')->nullable()->after('grid_id');
            $table->unsignedInteger('age')->nullable()->after('country');

            $table->index('grid_id');
            $table->index('hltv_id');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->string('grid_id')->nullable()->after('id');
            $table->string('hltv_id')->nullable()->after('grid_id');
            $table->string('liquipedia_slug')->nullable()->after('hltv_id');

            $table->index('grid_id');
            $table->index('hltv_id');
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->string('grid_id')->nullable()->after('id');
            $table->string('hltv_id')->nullable()->after('grid_id');

            $table->index('grid_id');
            $table->index('hltv_id');
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropIndex(['grid_id']);
            $table->dropIndex(['hltv_id']);
            $table->dropColumn(['grid_id', 'hltv_id']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex(['grid_id']);
            $table->dropIndex(['hltv_id']);
            $table->dropColumn(['grid_id', 'hltv_id', 'liquipedia_slug']);
        });

        Schema::table('players', function (Blueprint $table) {
            $table->dropIndex(['grid_id']);
            $table->dropIndex(['hltv_id']);
            $table->dropColumn(['grid_id', 'hltv_id', 'age']);
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->dropIndex(['grid_id']);
            $table->dropIndex(['hltv_id']);
            $table->dropIndex(['world_rank']);
            $table->dropColumn(['grid_id', 'hltv_id', 'world_rank', 'ranking_points']);
        });
    }
};