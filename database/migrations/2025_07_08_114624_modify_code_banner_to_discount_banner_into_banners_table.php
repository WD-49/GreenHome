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
      DB::statement("ALTER TABLE banners MODIFY COLUMN type ENUM('slider', 'category_banner', 'discount_banner') NOT NULL DEFAULT 'slider'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       DB::statement("ALTER TABLE banners MODIFY COLUMN type ENUM('slider', 'category_banner', 'code_banner') NOT NULL DEFAULT 'slider'");
    }
};
