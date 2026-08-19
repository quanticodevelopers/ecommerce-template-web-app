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
        Schema::create('products', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('brand_id')->constrained()->restrictOnDelete();
            $table->foreignUlid('category_id')->constrained()->restrictOnDelete();
            $table->string('sku', 24)->unique();
            $table->char('barcode', 13)->unique();
            $table->string('name', 128);
            $table->string('slug')->unique();
            $table->string('short_description')->nullable();
            $table->text('description')->nullable();
            $table->decimal('base_price', 10, 2)->nullable();
            $table->decimal('sale_price', 10, 2);
            $table->string('flag', 50)->nullable();
            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            $table->index('name');
            $table->index('published_at');
            $table->index(['brand_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
