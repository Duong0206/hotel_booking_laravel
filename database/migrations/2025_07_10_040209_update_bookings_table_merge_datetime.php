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
        // Thêm cột datetime tạm thời
        Schema::table('bookings', function (Blueprint $table) {
            $table->datetime('check_in_datetime_temp')->nullable()->after('check_in_date');
            $table->datetime('check_out_datetime_temp')->nullable()->after('check_out_date');
        });

        // Migrate dữ liệu hiện có
        DB::statement("
            UPDATE bookings 
            SET check_in_datetime_temp = CONCAT(check_in_date, ' ', COALESCE(check_in_time, '12:00:00')),
                check_out_datetime_temp = CONCAT(check_out_date, ' ', COALESCE(check_out_time, '14:00:00'))
        ");

        // Xóa các cột cũ
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['check_in_date', 'check_in_time', 'check_out_date', 'check_out_time']);
        });

        // Đổi tên cột datetime thành tên cũ
        Schema::table('bookings', function (Blueprint $table) {
            $table->renameColumn('check_in_datetime_temp', 'check_in_date');
            $table->renameColumn('check_out_datetime_temp', 'check_out_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Thêm lại các cột cũ
        Schema::table('bookings', function (Blueprint $table) {
            $table->date('check_in_date_old')->after('check_in_date');
            $table->time('check_in_time')->nullable()->after('check_in_date_old');
            $table->date('check_out_date_old')->after('check_in_time');
            $table->time('check_out_time')->nullable()->after('check_out_date_old');
        });

        // Migrate dữ liệu ngược lại
        DB::statement("
            UPDATE bookings 
            SET check_in_date_old = DATE(check_in_date),
                check_in_time = TIME(check_in_date),
                check_out_date_old = DATE(check_out_date),
                check_out_time = TIME(check_out_date)
        ");

        // Xóa cột datetime và đổi tên
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['check_in_date', 'check_out_date']);
            $table->renameColumn('check_in_date_old', 'check_in_date');
            $table->renameColumn('check_out_date_old', 'check_out_date');
        });
    }
};
