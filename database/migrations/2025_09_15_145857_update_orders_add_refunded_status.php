<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN order_status ENUM(
            'Chưa xác nhận',
            'Xác nhận',
            'Đang vận chuyển',
            'Giao hàng thành công',
            'Hủy đơn',
            'Đã nhận hàng',
            'Đã hoàn hàng'
        ) DEFAULT 'Chưa xác nhận'");

        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status ENUM(
            'pending',
            'paid',
            'failed',
            'refunded'
        ) NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN order_status ENUM(
            'Chưa xác nhận',
            'Xác nhận',
            'Đang vận chuyển',
            'Giao hàng thành công',
            'Hủy đơn',
            'Đã nhận hàng'
        ) DEFAULT 'Chưa xác nhận'");

        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status ENUM(
            'pending',
            'paid',
            'failed'
        ) NOT NULL DEFAULT 'pending'");
    }
};
