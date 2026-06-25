<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moderation_incidents', function (Blueprint $table) {
            $table->id();

            $table->string('incident_number', 32)->unique();

            $table->foreignId('subject_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('admin_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('action_type', 64);
            $table->string('status', 64)->default('active');

            $table->string('title');
            $table->text('user_message');
            $table->text('admin_note')->nullable();

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('resolved_at')->nullable();

            $table->unsignedInteger('listings_removed_count')->default(0);

            $table->timestamps();

            $table->index(['subject_user_id', 'status']);
            $table->index(['action_type', 'status']);
            $table->index('admin_user_id');
            $table->index('ends_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moderation_incidents');
    }
};