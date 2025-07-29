<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RoomTypeReview;
use App\Models\RoomType;
use App\Models\User;

class RoomTypeReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy danh sách room types và users
        $roomTypes = RoomType::all();
        $users = User::all();

        if ($roomTypes->isEmpty() || $users->isEmpty()) {
            $this->command->info('Không có dữ liệu room types hoặc users để tạo reviews!');
            return;
        }

        $reviews = [
            [
                'rating' => 5,
                'comment' => 'Phòng rất đẹp và sạch sẽ, view tuyệt vời!',
                'cleanliness_rating' => 5,
                'comfort_rating' => 5,
                'location_rating' => 5,
                'facilities_rating' => 4,
                'value_rating' => 5,
            ],
            [
                'rating' => 4,
                'comment' => 'Phòng tốt, đầy đủ tiện nghi, giá cả hợp lý.',
                'cleanliness_rating' => 4,
                'comfort_rating' => 4,
                'location_rating' => 5,
                'facilities_rating' => 4,
                'value_rating' => 4,
            ],
            [
                'rating' => 5,
                'comment' => 'Tuyệt vời! Nhân viên phục vụ rất chu đáo, phòng sạch sẽ.',
                'cleanliness_rating' => 5,
                'comfort_rating' => 5,
                'location_rating' => 4,
                'facilities_rating' => 5,
                'value_rating' => 4,
            ],
            [
                'rating' => 3,
                'comment' => 'Phòng ổn, nhưng có thể cải thiện thêm về tiện nghi.',
                'cleanliness_rating' => 4,
                'comfort_rating' => 3,
                'location_rating' => 4,
                'facilities_rating' => 2,
                'value_rating' => 3,
            ],
            [
                'rating' => 4,
                'comment' => 'Vị trí thuận tiện, phòng đẹp, giá cả phải chăng.',
                'cleanliness_rating' => 4,
                'comfort_rating' => 4,
                'location_rating' => 5,
                'facilities_rating' => 3,
                'value_rating' => 5,
            ],
            [
                'rating' => 5,
                'comment' => 'Trải nghiệm tuyệt vời! Sẽ quay lại lần nữa.',
                'cleanliness_rating' => 5,
                'comfort_rating' => 5,
                'location_rating' => 5,
                'facilities_rating' => 5,
                'value_rating' => 5,
            ],
            [
                'rating' => 4,
                'comment' => 'Phòng đẹp, view đẹp, nhưng wifi hơi chậm.',
                'cleanliness_rating' => 5,
                'comfort_rating' => 4,
                'location_rating' => 4,
                'facilities_rating' => 3,
                'value_rating' => 4,
            ],
            [
                'rating' => 3,
                'comment' => 'Phòng cơ bản, đủ dùng cho chuyến đi ngắn.',
                'cleanliness_rating' => 3,
                'comfort_rating' => 3,
                'location_rating' => 4,
                'facilities_rating' => 2,
                'value_rating' => 4,
            ],
            [
                'rating' => 5,
                'comment' => 'Khách sạn sang trọng, phòng cao cấp, dịch vụ tốt.',
                'cleanliness_rating' => 5,
                'comfort_rating' => 5,
                'location_rating' => 5,
                'facilities_rating' => 5,
                'value_rating' => 4,
            ],
            [
                'rating' => 4,
                'comment' => 'Phòng đẹp, sạch sẽ, nhân viên thân thiện.',
                'cleanliness_rating' => 5,
                'comfort_rating' => 4,
                'location_rating' => 4,
                'facilities_rating' => 4,
                'value_rating' => 4,
            ],
        ];

        $createdCount = 0;

        foreach ($roomTypes as $roomType) {
            // Tạo 2-4 reviews cho mỗi room type
            $numReviews = rand(2, 4);
            
            for ($i = 0; $i < $numReviews; $i++) {
                $reviewData = $reviews[array_rand($reviews)];
                $user = $users->random();
                
                // Kiểm tra xem user đã review room type này chưa
                $existingReview = RoomTypeReview::where('user_id', $user->id)
                    ->where('room_type_id', $roomType->id)
                    ->first();
                
                if (!$existingReview) {
                    RoomTypeReview::create([
                        'user_id' => $user->id,
                        'room_type_id' => $roomType->id,
                        'rating' => $reviewData['rating'],
                        'comment' => $reviewData['comment'],
                        'cleanliness_rating' => $reviewData['cleanliness_rating'],
                        'comfort_rating' => $reviewData['comfort_rating'],
                        'location_rating' => $reviewData['location_rating'],
                        'facilities_rating' => $reviewData['facilities_rating'],
                        'value_rating' => $reviewData['value_rating'],
                        'status' => 'approved',
                        'created_at' => now()->subDays(rand(1, 30)),
                        'updated_at' => now()->subDays(rand(1, 30)),
                    ]);
                    $createdCount++;
                }
            }
        }

        $this->command->info("Đã tạo thành công {$createdCount} reviews mẫu cho hệ thống đánh giá loại phòng!");
    }
}
