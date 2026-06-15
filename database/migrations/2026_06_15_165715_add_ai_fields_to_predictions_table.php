<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('predictions', function (Blueprint $table) {
            $table->string('source')->default('manual')->after('match_id'); // manual, model, ai
            $table->string('model_name')->nullable()->after('source');
            $table->string('model_version')->nullable()->after('model_name');

            $table->unsignedTinyInteger('team_one_win_probability')
                ->nullable()
                ->after('confidence_score');

            $table->unsignedTinyInteger('team_two_win_probability')
                ->nullable()
                ->after('team_one_win_probability');

            $table->string('prediction_label')
                ->default('toss_up')
                ->after('upset_risk');
            // team_one_likely, team_two_likely, toss_up

            $table->json('factors')->nullable()->after('reasoning');
            $table->json('input_snapshot')->nullable()->after('factors');

            $table->timestamp('generated_at')->nullable()->after('published_at');
            $table->timestamp('stale_at')->nullable()->after('generated_at');

            $table->index(['source', 'status']);
            $table->index('stale_at');
        });
    }

    public function down(): void
    {
        Schema::table('predictions', function (Blueprint $table) {
            $table->dropIndex(['source', 'status']);
            $table->dropIndex(['stale_at']);

            $table->dropColumn([
                'source',
                'model_name',
                'model_version',
                'team_one_win_probability',
                'team_two_win_probability',
                'prediction_label',
                'factors',
                'input_snapshot',
                'generated_at',
                'stale_at',
            ]);
        });
    }
};