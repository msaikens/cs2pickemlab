<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('provider', 100);
            $table->string('provider_id', 100);
            $table->string('provider_email', 100)->nullable();
            $table->string('provider_name', 100)->nullable();
            $table->string('avatar_url', 100)->nullable();

            $table->timestamps();

            $table->unique(['provider', 'provider_id']);
            $table->index(['provider', 'provider_email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_accounts');
    }
};
