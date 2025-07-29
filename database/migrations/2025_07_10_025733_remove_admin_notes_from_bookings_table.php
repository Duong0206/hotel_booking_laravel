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
        if (Schema::hasColumn('bookings', 'admin_notes')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropColumn('admin_notes');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->text('admin_notes')->nullable()->after('price')->comment('Ghi chú nội bộ cho quản lý');
        });
    }
};
