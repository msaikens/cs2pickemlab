<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_fees', function (Blueprint $table) {
            $table->id();

            $table->foreignId('skin_listing_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('trade_request_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('fee_type')->default('seller_percentage');

            $table->unsignedInteger('rate_basis_points')->default(700);
            $table->unsignedInteger('fixed_fee_cents')->default(0);
            $table->unsignedInteger('calculated_fee_cents')->default(0);

            $table->string('currency', 3)->default('USD');

            $table->timestamps();

            $table->index('skin_listing_id');
            $table->index('trade_request_id');
            $table->index('fee_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_fees');
    }
};