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
            $table->string('user_name', 255);
            $table->string('sku', 255)->unique();
            $table->string('shipping_name', 255);
            $table->string('shipping_phone', 15);
            $table->string('shipping_address', 255);
            $table->enum('order_status', [
                'Chưa xác nhận',
                'Xác nhận',
                'Đang vận chuyển',
                'Giao hàng thành công',
                'Hủy đơn'
            ])->default('Chưa xác nhận');
            $table->foreignId('discount_id')->nullable()->constrained('discounts')->nullOnDelete();
            $table->string('discount_code', 50)->nullable();
            $table->string('discount_value');
            $table->enum('payment_method', ['cod', 'banking', 'momo'])->default('cod');
            $table->string('payment_method_name', 255);
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
