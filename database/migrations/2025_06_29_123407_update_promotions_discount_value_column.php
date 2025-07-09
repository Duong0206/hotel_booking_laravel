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
        Schema::table('promotions', function (Blueprint $table) {
            // Thay đổi kiểu dữ liệu của cột discount_value để chứa giá trị lớn hơn
            $table->decimal('discount_value', 12, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            // Khôi phục kiểu dữ liệu cũ
            $table->decimal('discount_value', 5, 2)->change();
        });
    }
};
