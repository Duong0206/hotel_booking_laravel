<?php

// Test file để kiểm tra promotion system
// Chạy file này để test logic promotion

require_once 'vendor/autoload.php';

use App\Models\RoomType;
use App\Models\Promotion;

echo "=== TESTING PROMOTION SYSTEM ===\n\n";

// Test 1: Kiểm tra RoomType có load promotions không
echo "1. Testing RoomType with promotions:\n";
$roomTypes = RoomType::with(['promotions' => function($query) {
    $query->active()->available();
}])->take(3)->get();

foreach($roomTypes as $roomType) {
    echo "Room: {$roomType->name}\n";
    echo "- Has promotions: " . ($roomType->has_active_promotions ? 'Yes' : 'No') . "\n";
    
    if($roomType->has_active_promotions) {
        echo "- Best promotion: {$roomType->best_promotion->title}\n";
        echo "- Original price: " . number_format($roomType->price) . "đ\n";
        echo "- Discounted price: " . number_format($roomType->discounted_price) . "đ\n";
        echo "- Savings: " . number_format($roomType->savings_amount) . "đ\n";
    }
    echo "\n";
}

// Test 2: Kiểm tra active promotions
echo "2. Testing active promotions:\n";
$activePromotions = Promotion::active()->available()->get();
echo "Active promotions count: " . $activePromotions->count() . "\n";

foreach($activePromotions->take(3) as $promotion) {
    echo "- {$promotion->title} ({$promotion->discount_text})\n";
    echo "  Code: {$promotion->code}\n";
    echo "  Valid until: {$promotion->expired_at->format('d/m/Y')}\n";
}

echo "\n=== TEST COMPLETED ===\n"; 