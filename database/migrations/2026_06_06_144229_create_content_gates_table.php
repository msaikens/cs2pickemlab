<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_gates', function (Blueprint $table) {
            $table->id();

            $table->string('gate_key')->unique();
            $table->string('label');
            $table->text('description')->nullable();

            $table->boolean('is_enabled')->default(true);
            $table->boolean('requires_login')->default(false);
            $table->boolean('requires_subscription')->default(false);

            $table->string('locked_message')->nullable();

            $table->timestamps();
        });

        DB::table('content_gates')->insert([
            [
                'gate_key' => 'premium_pickem_recommendations',
                'label' => 'Premium Pick’em Recommendations',
                'description' => 'Premium reasoning and advanced Pick’em advice.',
                'is_enabled' => true,
                'requires_login' => true,
                'requires_subscription' => true,
                'locked_message' => 'Subscribe to view premium Pick’em recommendations.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'gate_key' => 'advanced_match_predictions',
                'label' => 'Advanced Match Predictions',
                'description' => 'Detailed match prediction models, confidence notes, and risk explanations.',
                'is_enabled' => true,
                'requires_login' => true,
                'requires_subscription' => true,
                'locked_message' => 'Subscribe to view advanced match predictions.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'gate_key' => 'checkout',
                'label' => 'Checkout / Purchasing',
                'description' => 'Require users to be signed in before purchasing.',
                'is_enabled' => true,
                'requires_login' => true,
                'requires_subscription' => false,
                'locked_message' => 'Sign in or create an account to purchase.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'gate_key' => 'bracket_simulator',
                'label' => 'Bracket Simulator',
                'description' => 'Interactive bracket or Pick’em simulator access.',
                'is_enabled' => true,
                'requires_login' => false,
                'requires_subscription' => false,
                'locked_message' => 'This simulator is currently restricted.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('content_gates');
    }
};
