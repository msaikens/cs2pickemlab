<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_ratings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rater_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('rated_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('rateable_type');
            $table->unsignedBigInteger('rateable_id');

            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();

            $table->timestamps();

            $table->unique([
                'rater_user_id',
                'rated_user_id',
                'rateable_type',
                'rateable_id',
            ], 'marketplace_rating_once_per_user_context');

            $table->index(['rated_user_id', 'rating']);
            $table->index(['rateable_type', 'rateable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_ratings');
    }
};