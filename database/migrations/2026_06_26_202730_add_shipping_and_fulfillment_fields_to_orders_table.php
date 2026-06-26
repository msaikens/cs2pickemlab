<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_name', 100)->nullable()->after('customer_phone');
            $table->string('shipping_address_line_1', 191)->nullable()->after('shipping_name');
            $table->string('shipping_address_line_2', 191)->nullable()->after('shipping_address_line_1');
            $table->string('shipping_city', 100)->nullable()->after('shipping_address_line_2');
            $table->string('shipping_state', 100)->nullable()->after('shipping_city');
            $table->string('shipping_postal_code', 40)->nullable()->after('shipping_state');
            $table->string('shipping_country', 2)->default('US')->after('shipping_postal_code');
            $table->text('shipping_instructions')->nullable()->after('shipping_country');

            $table->string('shipping_carrier', 100)->nullable()->after('shipping_instructions');
            $table->string('tracking_number', 191)->nullable()->after('shipping_carrier');
            $table->timestamp('shipped_at')->nullable()->after('tracking_number');
            $table->timestamp('completed_at')->nullable()->after('shipped_at');
            $table->timestamp('cancelled_at')->nullable()->after('completed_at');

            $table->index(['shipping_country', 'shipping_state']);
            $table->index('tracking_number');
            $table->index('shipped_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['shipping_country', 'shipping_state']);
            $table->dropIndex(['tracking_number']);
            $table->dropIndex(['shipped_at']);

            $table->dropColumn([
                'shipping_name',
                'shipping_address_line_1',
                'shipping_address_line_2',
                'shipping_city',
                'shipping_state',
                'shipping_postal_code',
                'shipping_country',
                'shipping_instructions',
                'shipping_carrier',
                'tracking_number',
                'shipped_at',
                'completed_at',
                'cancelled_at',
            ]);
        });
    }
};