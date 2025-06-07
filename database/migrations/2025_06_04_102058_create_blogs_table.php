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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();

            // Tiêu đề và nội dung
            $table->string('title');
            $table->string('slug')->unique(); // SEO-friendly URL
            $table->text('summary')->nullable(); // Tóm tắt bài viết
            $table->longText('content'); // Nội dung đầy đủ

            // Ảnh đại diện
            $table->string('thumbnail')->nullable(); // URL ảnh

            // Trạng thái
            $table->boolean('status');

            // Tác giả
            $table->foreignId('author_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete(); 

            // Gắn danh mục nếu cần
            $table->foreignId('blog_category_id')->nullable()->constrained('blog_categories')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes(); // Soft delete hỗ trợ khôi phục bài viết
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
