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
        // 1. Drop các bảng cũ
        Schema::dropIfExists('promotion_room');
        Schema::dropIfExists('promotion_room_type');
        Schema::dropIfExists('promotions');
        
        // 2. Tạo bảng promotions mới
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            
            // Thông tin cơ bản
            $table->string('title');
            $table->string('code')->unique();
            $table->text('description');
            $table->text('terms_conditions')->nullable();
            $table->string('image')->nullable();
            
            // Loại và giá trị giảm giá
            $table->enum('discount_type', ['percentage', 'fixed']);
            $table->decimal('discount_value', 10, 2);
            $table->decimal('minimum_amount', 10, 2)->default(0);
            
            // Phạm vi áp dụng
            $table->enum('apply_scope', ['all', 'room_types', 'specific_rooms'])->default('all');
            
            // Thời gian và giới hạn
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('expired_at');
            $table->integer('usage_limit')->unsigned()->nullable();
            $table->integer('used_count')->unsigned()->default(0);
            
            // Trạng thái
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('can_combine')->default(false);
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('code');
            $table->index('is_active');
            $table->index('is_featured');
            $table->index(['valid_from', 'expired_at']);
        });
        
        // 3. Tạo bảng pivot cho room types
        Schema::create('promotion_room_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id');
            $table->foreignId('room_type_id');
            $table->timestamps();
            
            // Unique constraint
            $table->unique(['promotion_id', 'room_type_id'], 'prt_promotion_room_type_unique');
            
            // Foreign keys với tên custom
            $table->foreign('promotion_id', 'prt_promotion_id_foreign')
                ->references('id')
                ->on('promotions')
                ->onDelete('cascade');
                
            $table->foreign('room_type_id', 'prt_room_type_id_foreign')
                ->references('id')
                ->on('room_types')
                ->onDelete('cascade');
        });
        
        // 4. Tạo bảng pivot cho specific rooms
        Schema::create('promotion_room', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id');
            $table->foreignId('room_id');
            $table->timestamps();
            
            // Unique constraint
            $table->unique(['promotion_id', 'room_id'], 'pr_promotion_room_unique');
            
            // Foreign keys với tên custom
            $table->foreign('promotion_id', 'pr_promotion_id_foreign')
                ->references('id')
                ->on('promotions')
                ->onDelete('cascade');
                
            $table->foreign('room_id', 'pr_room_id_foreign')
                ->references('id')
                ->on('rooms')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_room');
        Schema::dropIfExists('promotion_room_type');
        Schema::dropIfExists('promotions');
    }
}; 