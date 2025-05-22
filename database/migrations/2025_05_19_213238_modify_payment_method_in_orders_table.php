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
        Schema::table('orders', function (Blueprint $table) {
            Schema::table('orders', function (Blueprint $table) {
                // Thêm cột foreign key mới (nullable để tránh lỗi nếu có đơn chưa có payment_method_id)
                $table->foreignId('payment_method_id')->nullable()->after('discount_id')->constrained('payment_methods')->cascadeOnDelete();
            });

            // (Tuỳ chọn) Chuyển dữ liệu từ payment_method (enum) sang payment_method_id
            // Bạn cần tự viết logic chuyển đổi trong Seeder hoặc Command vì migration không phù hợp thao tác dữ liệu phức tạp.

            Schema::table('orders', function (Blueprint $table) {
                // Xoá cột enum cũ
                $table->dropColumn('payment_method');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_method', ['cod', 'banking', 'momo'])->default('cod')->after('discount_id');
            $table->dropForeign(['payment_method_id']);
            $table->dropColumn('payment_method_id');
        });
    }
};
