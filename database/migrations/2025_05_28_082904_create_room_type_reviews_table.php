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
        Schema::create('room_type_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('room_type_id')->constrained('room_types')->onDelete('cascade');
            $table->integer('rating')->comment('Điểm đánh giá từ 1-5');
            $table->text('comment')->nullable()->comment('Bình luận của khách hàng');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->comment('Trạng thái duyệt đánh giá');
            $table->boolean('is_anonymous')->default(false)->comment('Đánh giá ẩn danh');
            $table->timestamps();
            
            // Đảm bảo mỗi user chỉ đánh giá 1 lần cho mỗi loại phòng
            $table->unique(['user_id', 'room_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_type_reviews');
    }
}; 