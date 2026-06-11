<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trade_request_events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('trade_request_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('actor_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('event_type');
            $table->string('old_status')->nullable();
            $table->string('new_status')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index('trade_request_id');
            $table->index('actor_user_id');
            $table->index('event_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trade_request_events');
    }
};