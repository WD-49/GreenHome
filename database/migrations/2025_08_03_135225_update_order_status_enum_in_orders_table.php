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
        DB::statement("ALTER TABLE orders MODIFY order_status ENUM(
            'Chưa xác nhận',
            'Xác nhận',
            'Đang vận chuyển',
            'Giao hàng thành công',
            'Hủy đơn',
            'Đã nhận hàng'
        ) DEFAULT 'Chưa xác nhận'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY order_status ENUM(
            'Chưa xác nhận',
            'Xác nhận',
            'Đang vận chuyển',
            'Giao hàng thành công',
            'Hủy đơn'
        ) DEFAULT 'Chưa xác nhận'");
    }
};
