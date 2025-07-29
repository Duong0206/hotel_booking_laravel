<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->integer('floor')->after('room_type_id');
            // Đặt tên index rõ ràng
            $table->index(['floor', 'room_number'], 'rooms_floor_room_number_index');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Xóa index theo tên (bắt buộc)
            $table->dropIndex('rooms_floor_room_number_index');
            $table->dropColumn('floor');
        });
    }
};
