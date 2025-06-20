<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Coupon;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Tạo các khuyến mãi nổi bật
        Coupon::create([
            'code' => 'SUMMER2024',
            'discount' => 25.00,
            'expired_at' => '2024-08-31',
        ]);

        Coupon::create([
            'code' => 'WEEKEND50',
            'discount' => 50.00,
            'expired_at' => '2024-07-31',
        ]);

        Coupon::create([
            'code' => 'NEWCUSTOMER',
            'discount' => 15.00,
            'expired_at' => '2024-12-31',
        ]);

        Coupon::create([
            'code' => 'HOLIDAY30',
            'discount' => 30.00,
            'expired_at' => '2024-09-15',
        ]);

        Coupon::create([
            'code' => 'EARLYBIRD',
            'discount' => 20.00,
            'expired_at' => '2024-10-31',
        ]);

        Coupon::create([
            'code' => 'FAMILY40',
            'discount' => 40.00,
            'expired_at' => '2024-08-15',
        ]);
    }
}
