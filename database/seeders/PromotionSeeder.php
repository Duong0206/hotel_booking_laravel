<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Promotion;
use Carbon\Carbon;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Promotion::create([
            'title' => 'Giảm giá 20% cho khách hàng mới',
            'description' => 'Ưu đãi đặc biệt cho khách hàng lần đầu đặt phòng tại khách sạn. Áp dụng cho tất cả các loại phòng.',
            'code' => 'NEWCUST20',
            'discount_type' => 'percentage',
            'discount_value' => 20.00,
            'minimum_amount' => 1000000.00,
            'usage_limit' => 100,
            'used_count' => 15,
            'expired_at' => Carbon::now()->addMonths(3),
            'is_active' => true,
            'is_featured' => true,
            'terms_conditions' => 'Không áp dụng cùng với các chương trình khuyến mại khác. Áp dụng cho đặt phòng trực tiếp tại website.'
        ]);

        Promotion::create([
            'title' => 'Giảm 500.000đ cho booking cuối tuần',
            'description' => 'Giảm ngay 500.000đ cho các đặt phòng vào cuối tuần (thứ 6, 7, chủ nhật).',
            'code' => 'WEEKEND500',
            'discount_type' => 'fixed',
            'discount_value' => 500000.00,
            'minimum_amount' => 2000000.00,
            'usage_limit' => 50,
            'used_count' => 8,
            'expired_at' => Carbon::now()->addMonths(2),
            'is_active' => true,
            'is_featured' => true,
            'terms_conditions' => 'Chỉ áp dụng cho đặt phòng từ thứ 6 đến chủ nhật. Tối thiểu 2 đêm.'
        ]);

        Promotion::create([
            'title' => 'Ưu đãi 15% cho đặt phòng dài hạn',
            'description' => 'Giảm 15% cho các đặt phòng từ 7 đêm trở lên. Thích hợp cho kỳ nghỉ dài ngày.',
            'code' => 'LONGSTAY15',
            'discount_type' => 'percentage',
            'discount_value' => 15.00,
            'minimum_amount' => 3000000.00,
            'usage_limit' => null,
            'used_count' => 23,
            'expired_at' => Carbon::now()->addMonths(6),
            'is_active' => true,
            'is_featured' => false,
            'terms_conditions' => 'Áp dụng cho đặt phòng tối thiểu 7 đêm. Không hoàn tiền khi hủy đặt phòng.'
        ]);

        Promotion::create([
            'title' => 'Flash Sale - Giảm 30%',
            'description' => 'Flash sale cuối năm! Giảm ngay 30% cho tất cả các loại phòng. Có hạn!',
            'code' => 'FLASH30',
            'discount_type' => 'percentage',
            'discount_value' => 30.00,
            'minimum_amount' => 1500000.00,
            'usage_limit' => 30,
            'used_count' => 28,
            'expired_at' => Carbon::now()->addDays(15),
            'is_active' => true,
            'is_featured' => true,
            'terms_conditions' => 'Chương trình có thời hạn. Không áp dụng với các ưu đãi khác.'
        ]);

        Promotion::create([
            'title' => 'Giảm giá sinh nhật',
            'description' => 'Chúc mừng sinh nhật! Nhận ngay ưu đãi 25% cho chuyến du lịch đáng nhớ.',
            'code' => 'BIRTHDAY25',
            'discount_type' => 'percentage',
            'discount_value' => 25.00,
            'minimum_amount' => 800000.00,
            'usage_limit' => null,
            'used_count' => 5,
            'expired_at' => Carbon::now()->addYear(),
            'is_active' => true,
            'is_featured' => false,
            'terms_conditions' => 'Khách hàng cần xuất trình chứng minh thư để xác nhận ngày sinh.'
        ]);
    }
}
