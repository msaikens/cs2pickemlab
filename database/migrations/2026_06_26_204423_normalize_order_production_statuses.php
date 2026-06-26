<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('orders')
            ->where('status', 'pending_payment')
            ->update(['status' => 'received']);
    }

    public function down(): void
    {
        DB::table('orders')
            ->where('status', 'received')
            ->whereIn('payment_status', ['unpaid', 'pending'])
            ->update(['status' => 'pending_payment']);
    }
};