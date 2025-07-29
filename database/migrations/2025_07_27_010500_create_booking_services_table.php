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
        Schema::create('booking_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2); // Giá tại thời điểm đặt
            $table->decimal('total_price', 10, 2); // Tổng giá (quantity * unit_price)
            $table->text('notes')->nullable(); // Ghi chú cho dịch vụ này
            $table->enum('type', ['room_type', 'additional', 'custom'])->default('additional'); 
            $table->timestamps();

            // Đảm bảo không trùng lặp service cho cùng 1 booking
            $table->unique(['booking_id', 'service_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_services');
    }
};
