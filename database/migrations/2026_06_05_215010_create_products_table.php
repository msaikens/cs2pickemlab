<?php
/// This migration creates the 'products' table with fields for product details, pricing, status,
/// type, and other attributes.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug', 191)->unique();
            $table->string('sku', 191)->nullable()->unique();

            $table->string('short_description')->nullable();
            $table->longText('description')->nullable();

            $table->unsignedInteger('base_price')->default(0); // stored in cents

            $table->string('status')->default('draft');
            // draft, active, archived

            $table->string('product_type')->default('physical');
            // physical, digital, service, bundle, custom

            $table->boolean('requires_customization')->default(false);
            $table->boolean('requires_upload')->default(false);
            $table->boolean('is_featured')->default(false);

            $table->integer('sort_order')->default(0);
            $table->string('primary_image_path')->nullable();

            $table->timestamps();

            $table->index(['status', 'is_featured']);
            $table->index('product_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
