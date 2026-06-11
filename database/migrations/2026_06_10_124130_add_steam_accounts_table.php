<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('steam_accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('steam_id_64')->unique();
            $table->string('persona_name')->nullable();
            $table->string('profile_url')->nullable();
            $table->string('avatar_url')->nullable();
            $table->string('profile_visibility')->nullable();

            $table->timestamp('linked_at')->nullable();
            $table->timestamp('last_verified_at')->nullable();

            $table->timestamps();

            $table->index('user_id');
            $table->index('steam_id_64');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('steam_accounts');
    }
};