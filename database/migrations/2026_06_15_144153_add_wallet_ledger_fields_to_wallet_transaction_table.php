<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->string('direction')->default('credit')->after('type');
            $table->string('balance_bucket')->default('available')->after('currency');

            $table->integer('available_balance_after_cents')->nullable()->after('balance_bucket');
            $table->integer('reserved_balance_after_cents')->nullable()->after('available_balance_after_cents');
            $table->integer('pending_balance_after_cents')->nullable()->after('reserved_balance_after_cents');

            $table->string('reference_type')->nullable()->after('stripe_payment_intent_id');
            $table->unsignedBigInteger('reference_id')->nullable()->after('reference_type');

            $table->text('description')->nullable()->after('status');

            $table->index(['reference_type', 'reference_id']);
            $table->index(['type', 'status']);
            $table->index(['wallet_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropIndex(['reference_type', 'reference_id']);
            $table->dropIndex(['type', 'status']);
            $table->dropIndex(['wallet_id', 'created_at']);

            $table->dropColumn([
                'direction',
                'balance_bucket',
                'available_balance_after_cents',
                'reserved_balance_after_cents',
                'pending_balance_after_cents',
                'reference_type',
                'reference_id',
                'description',
            ]);
        });
    }
};