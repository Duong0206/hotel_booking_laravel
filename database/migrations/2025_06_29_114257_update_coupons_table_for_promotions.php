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
        Schema::table('coupons', function (Blueprint $table) {
            // Thêm các trường mới cho khuyến mại
            $table->string('title')->after('id'); // Tiêu đề khuyến mại
            $table->text('description')->nullable()->after('title'); // Mô tả
            $table->enum('discount_type', ['percentage', 'fixed'])->default('fixed')->after('code'); // Loại giảm giá
            $table->renameColumn('discount', 'discount_value'); // Đổi tên cột discount thành discount_value
            $table->decimal('minimum_amount', 10, 2)->default(0)->after('discount_value'); // Số tiền tối thiểu
            $table->integer('usage_limit')->nullable()->after('minimum_amount'); // Giới hạn sử dụng
            $table->integer('used_count')->default(0)->after('usage_limit'); // Số lần đã sử dụng
            $table->boolean('is_active')->default(true)->after('expired_at'); // Trạng thái hoạt động
            $table->boolean('is_featured')->default(false)->after('is_active'); // Khuyến mại nổi bật
            $table->string('image')->nullable()->after('is_featured'); // Hình ảnh khuyến mại
            $table->text('terms_conditions')->nullable()->after('image'); // Điều khoản và điều kiện
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'description', 
                'discount_type',
                'minimum_amount',
                'usage_limit',
                'used_count',
                'is_active',
                'is_featured',
                'image',
                'terms_conditions'
            ]);
            $table->renameColumn('discount_value', 'discount');
        });
    }
};
