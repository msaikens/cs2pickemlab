<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();

            $table->foreignId('team_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('handle');
            $table->string('slug', 100)->unique();

            $table->string('real_name')->nullable();
            $table->string('country')->nullable();
            $table->string('role', 100)->nullable();
            // awper, rifler, igl, support, lurker, entry, coach, substitute

            $table->string('photo_path')->nullable();

            $table->decimal('rating', 5, 2)->nullable();
            $table->decimal('kd_ratio', 5, 2)->nullable();
            $table->decimal('impact_rating', 5, 2)->nullable();

            $table->string('status', 100)->default('active');
            // active, benched, inactive, retired

            $table->text('summary')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('team_id');
            $table->index(['status', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
