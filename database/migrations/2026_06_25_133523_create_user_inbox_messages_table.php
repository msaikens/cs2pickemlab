<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_inbox_messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('moderation_incident_id')
                ->nullable()
                ->constrained('moderation_incidents')
                ->nullOnDelete();

            $table->string('type', 64)->default('moderation');
            $table->string('title');
            $table->text('body');

            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'read_at']);
            $table->index(['user_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_inbox_messages');
    }
};