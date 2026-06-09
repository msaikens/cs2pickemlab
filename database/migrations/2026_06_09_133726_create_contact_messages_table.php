<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();

            $table->string('name', 120);
            $table->string('email', 255);
            $table->string('subject', 160)->nullable();
            $table->text('message');

            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();

            $table->boolean('is_spam')->default(false);
            $table->string('spam_reason', 255)->nullable();

            $table->timestamp('sent_at')->nullable();

            $table->timestamps();

            $table->index('email');
            $table->index('created_at');
            $table->index('is_spam');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};