<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Thay đổi enum type để thêm 'system'
        DB::statement("ALTER TABLE booking_notes MODIFY COLUMN type ENUM('customer', 'staff', 'admin', 'system') DEFAULT 'customer'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Khôi phục enum type về trạng thái ban đầu
        DB::statement("ALTER TABLE booking_notes MODIFY COLUMN type ENUM('customer', 'staff', 'admin') DEFAULT 'customer'");
    }
};
