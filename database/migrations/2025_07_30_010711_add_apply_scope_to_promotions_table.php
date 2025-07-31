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
            $table->enum('apply_scope', ['all', 'room_types', 'specific_rooms'])
                  ->default('all')
                  ->after('can_combine')
                  ->comment('Phạm vi áp dụng khuyến mại: all = tất cả phòng, room_types = theo loại phòng, specific_rooms = phòng cụ thể');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn('apply_scope');
        });
    }
};
