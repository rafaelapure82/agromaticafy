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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->decimal('purchase_price', 14, 2)->default(0);
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->string('reference_type')->nullable(); // Sale, Purchase, Adjustment
            $table->unsignedBigInteger('reference_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropColumn(['reference_type', 'reference_id']);
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn(['purchase_price', 'supplier_id']);
        });
        Schema::dropIfExists('suppliers');
    }
};
