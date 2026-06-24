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
        Schema::create('wallet_terms_acceptances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('terms_version', 64);

            $table->timestamp('accepted_at')->useCurrent();

            $table->string('ip_address', 45)->nullable();

            $table->text('user_agent')->nullable();

            $table->string('source', 64)->default('wallet_terms_page');

            $table->timestamps();

            $table->unique(['user_id', 'terms_version'], 'wallet_terms_user_version_unique');
            $table->index(['terms_version', 'accepted_at'], 'wallet_terms_version_accepted_at_index');
            $table->index('source', 'wallet_terms_source_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_terms_acceptances');
    }
};