<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('product_attribute')->nullable()->after('product_variant_sku');
            $table->renameColumn('poduct_name', 'product_name'); // Đổi tên cột
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('product_attribute');
            $table->renameColumn('product_name', 'poduct_name');
        });
    }
};
