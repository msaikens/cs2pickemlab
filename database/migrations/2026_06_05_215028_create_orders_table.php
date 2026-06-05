<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_number', 191)->unique();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('customer_name', 100);
            $table->string('customer_email', 100);
            $table->string('customer_phone', 100)->nullable();

            $table->string('status', 100)->default('draft');
            // draft, pending_payment, paid, design_needed, design_ready,
            // printing, quality_check, shipped, completed, cancelled, refunded

            $table->string('payment_status', 100)->default('unpaid');
            // unpaid, pending, paid, failed, refunded

            $table->unsignedInteger('subtotal')->default(0);
            $table->unsignedInteger('shipping_amount')->default(0);
            $table->unsignedInteger('tax_amount')->default(0);
            $table->unsignedInteger('discount_amount')->default(0);
            $table->unsignedInteger('total')->default(0);

            $table->string('currency', 3)->default('USD');

            $table->string('stripe_checkout_session_id')->nullable()->unique();
            $table->string('stripe_payment_intent_id')->nullable()->index();

            $table->timestamp('paid_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['status', 'payment_status']);
            $table->index('customer_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
