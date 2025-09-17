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
        Schema::create('refund_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');

            $table->text('refund_reason')->nullable();
            $table->string('refund_image')->nullable();

            $table->enum('refund_status', [
                'pending',
                'approved',
                'rejected',
                'refund_pending',
                'refunded'
            ])->default('pending');

            // Thông tin ngân hàng do khách nhập
            $table->string('refund_account_name')->nullable();       // Tên chủ TK
            $table->string('refund_account_bank')->nullable();       // Ngân hàng
            $table->string('refund_account_number')->nullable();        // Số tài khoản
            $table->string('refund_account_qr')->nullable();    // Ảnh QR

            // Thông tin hoàn tiền
            $table->decimal('refund_cost', 12, 2)->nullable();
            $table->string('refund_proof_image')->nullable(); // Ảnh chứng từ shop upload
            $table->dateTime('refund_date')->nullable();

            $table->text('admin_note')->nullable(); // Ghi chú/từ chối

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refund_transactions');
    }
};
