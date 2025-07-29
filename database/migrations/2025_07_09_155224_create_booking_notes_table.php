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
        Schema::create('booking_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('content');
            $table->enum('type', ['customer', 'staff', 'admin'])->default('customer');
            $table->enum('visibility', ['public', 'private', 'internal'])->default('public');
            $table->boolean('is_internal')->default(false)->comment('Ghi chú nội bộ chỉ admin thấy');
            $table->timestamps();
            
            // Index để tối ưu truy vấn
            $table->index(['booking_id', 'type']);
            $table->index(['booking_id', 'visibility']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_notes');
    }
};
