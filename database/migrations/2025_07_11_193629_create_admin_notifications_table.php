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
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // booking_created, booking_status_changed, payment_received, etc.
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // Dữ liệu bổ sung (booking_id, user_id, etc.)
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->string('priority')->default('normal'); // low, normal, high, urgent
            $table->string('icon')->nullable(); // Icon cho thông báo
            $table->string('color')->default('primary'); // Màu sắc cho thông báo
            $table->timestamps();
            
            // Index để tối ưu truy vấn
            $table->index(['type', 'is_read']);
            $table->index(['priority', 'created_at']);
            $table->index('is_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};
