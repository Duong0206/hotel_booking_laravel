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
        Schema::table('room_type_reviews', function (Blueprint $table) {
            // Gỡ foreign key trước (nếu có)
            $table->dropForeign(['user_id']);
            $table->dropForeign(['room_type_id']);

            // Xoá unique cũ
            $table->dropUnique('room_type_reviews_user_id_room_type_id_unique');

            // Thêm unique mới
            $table->unique(['user_id', 'booking_id']);

            // Thêm lại foreign key (nếu cần tiếp tục ràng buộc)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('room_type_id')->references('id')->on('room_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_type_reviews', function (Blueprint $table) {
            // Gỡ ràng buộc mới
            $table->dropForeign(['user_id']);
            $table->dropForeign(['room_type_id']);

            $table->dropUnique(['user_id', 'booking_id']);

            // Thêm lại unique cũ
            $table->unique(['user_id', 'room_type_id']);

            // Thêm lại foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('room_type_id')->references('id')->on('room_types')->onDelete('cascade');
        });
    }
};
