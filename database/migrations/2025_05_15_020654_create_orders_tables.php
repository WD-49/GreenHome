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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('shipping_name', 255);
            $table->string('shipping_phone', 15);
            $table->string('shipping_address', 255);
            $table->foreignId('status_id')->constrained('order_statuses')->cascadeOnDelete();
            $table->foreignId('discount_id')->nullable()->constrained('discounts')->cascadeOnDelete();
            $table->enum('payment_method', ['cod', 'banking', 'momo'])->default('cod');
            $table->enum('payment_status', ['pending', 'paid', 'failed']);
            $table->decimal('discount_amount', 10, 2);
            $table->decimal('shipping_fee', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->text('note')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
