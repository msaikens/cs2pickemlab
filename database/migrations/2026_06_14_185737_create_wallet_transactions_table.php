<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('wallet_transactions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('wallet_id')->constrained()->cascadeOnDelete();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();

        $table->string('type');
        $table->integer('amount_cents');
        $table->string('currency', 3)->default('usd');

        $table->string('stripe_checkout_session_id')->nullable()->index();
        $table->string('stripe_payment_intent_id')->nullable()->index();

        $table->string('status')->default('completed');
        $table->json('metadata')->nullable();

        $table->timestamps();

        $table->unique('stripe_checkout_session_id');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
