<?php

// Script tạo sample promotion data nhanh
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Promotion;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

echo "🚀 Tạo sample promotion data...\n\n";

// Xóa relationships trước
DB::table('promotion_room_type')->delete();
DB::table('promotion_room')->delete();
echo "✅ Đã xóa relationships cũ\n";

// Xóa promotion cũ
Promotion::query()->delete();
echo "✅ Đã xóa promotion cũ\n";

// Tạo promotions mới
$promotions = [
    [
        'title' => 'Flash Sale Cuối Tuần',
        'description' => 'Giảm giá sốc cho khách đặt phòng cuối tuần',
        'code' => 'WEEKEND30',
        'discount_type' => 'percentage',
        'discount_value' => 30,
        'minimum_amount' => 0,
        'usage_limit' => 100,
        'used_count' => 0,
        'valid_from' => Carbon::now(),
        'expired_at' => Carbon::now()->addDays(7),
        'is_active' => true,
        'is_featured' => true,
        'can_combine' => false,
    ],
    [
        'title' => 'Ưu Đãi Tháng 12',
        'description' => 'Giảm 500k cho đơn hàng từ 2 triệu',
        'code' => 'DEC500K',
        'discount_type' => 'fixed',
        'discount_value' => 500000,
        'minimum_amount' => 2000000,
        'usage_limit' => 50,
        'used_count' => 0,
        'valid_from' => Carbon::now(),
        'expired_at' => Carbon::now()->addDays(30),
        'is_active' => true,
        'is_featured' => true,
        'can_combine' => false,
    ],
    [
        'title' => 'Happy Hour',
        'description' => 'Giảm 15% cho booking trong ngày',
        'code' => 'HAPPY15',
        'discount_type' => 'percentage',
        'discount_value' => 15,
        'minimum_amount' => 0,
        'usage_limit' => null,
        'used_count' => 0,
        'valid_from' => Carbon::now(),
        'expired_at' => Carbon::now()->addDays(1),
        'is_active' => true,
        'is_featured' => false,
        'can_combine' => true,
    ]
];

foreach ($promotions as $promoData) {
    $promotion = Promotion::create($promoData);
    echo "✅ Tạo promotion: {$promotion->title} - {$promotion->discount_text}\n";
}

// Gán promotions cho room types
$roomTypes = RoomType::all();
$promotionIds = Promotion::pluck('id')->toArray();

foreach ($roomTypes as $roomType) {
    // Gán ngẫu nhiên 1-2 promotions cho mỗi room type
    $randomPromotions = array_rand($promotionIds, rand(1, min(2, count($promotionIds))));
    if (!is_array($randomPromotions)) {
        $randomPromotions = [$randomPromotions];
    }
    
    $assignedIds = [];
    foreach ($randomPromotions as $index) {
        $assignedIds[] = $promotionIds[$index];
    }
    
    $roomType->promotions()->sync($assignedIds);
    echo "✅ Gán promotion cho room type: {$roomType->name}\n";
}

echo "\n🎉 HOÀN THÀNH! Đã tạo " . count($promotions) . " promotions\n";
echo "💻 Giờ hãy check website để xem promotion hiển thị!\n"; 