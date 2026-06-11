<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trade_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('skin_listing_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('buyer_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('seller_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->text('message')->nullable();
            $table->string('status')->default('pending');

            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('declined_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->index('skin_listing_id');
            $table->index('buyer_user_id');
            $table->index('seller_user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trade_requests');
    }
};