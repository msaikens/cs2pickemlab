<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();

            $table->string('short_name')->nullable();
            $table->string('region')->nullable();
            $table->string('country')->nullable();

            $table->string('logo_path')->nullable();

            $table->integer('picklab_rating')->default(1500);
            $table->string('status')->default('active');
            // active, inactive, archived

            $table->text('summary')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['status', 'region']);
            $table->index('picklab_rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
