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
        Schema::create('price_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Mayorista, VIP, etc.
            $table->timestamps();
        });

        Schema::create('product_price_tier', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('price_tier_id')->constrained()->onDelete('cascade');
            $table->decimal('price', 14, 2);
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->integer('committed_stock')->default(0);
            $table->integer('min_stock')->default(5);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['committed_stock', 'min_stock']);
        });
        Schema::dropIfExists('product_price_tier');
        Schema::dropIfExists('price_tiers');
    }
};
