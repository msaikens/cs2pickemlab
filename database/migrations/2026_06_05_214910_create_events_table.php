<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();

            $table->string('organizer')->nullable();
            $table->string('location')->nullable();

            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();

            $table->string('status')->default('upcoming');
            // upcoming, live, completed, cancelled

            $table->boolean('has_pickem')->default(false);
            $table->boolean('is_featured')->default(false);

            $table->text('summary')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['status', 'is_featured']);
            $table->index(['starts_on', 'ends_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
