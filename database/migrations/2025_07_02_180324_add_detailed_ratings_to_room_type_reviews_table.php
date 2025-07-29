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
            $table->integer('cleanliness_rating')->nullable()->after('rating')->comment('Điểm đánh giá vệ sinh từ 1-5');
            $table->integer('comfort_rating')->nullable()->after('cleanliness_rating')->comment('Điểm đánh giá tiện nghi từ 1-5');
            $table->integer('location_rating')->nullable()->after('comfort_rating')->comment('Điểm đánh giá vị trí từ 1-5');
            $table->integer('facilities_rating')->nullable()->after('location_rating')->comment('Điểm đánh giá cơ sở vật chất từ 1-5');
            $table->integer('value_rating')->nullable()->after('facilities_rating')->comment('Điểm đánh giá giá trị từ 1-5');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_type_reviews', function (Blueprint $table) {
            $table->dropColumn([
                'cleanliness_rating',
                'comfort_rating', 
                'location_rating',
                'facilities_rating',
                'value_rating'
            ]);
        });
    }
};
