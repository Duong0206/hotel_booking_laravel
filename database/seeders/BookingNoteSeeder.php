<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BookingNote;
use App\Models\Booking;
use App\Models\User;

class BookingNoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy một số booking và user để tạo dữ liệu mẫu
        $bookings = Booking::take(5)->get();
        $users = User::take(3)->get();

        if ($bookings->isEmpty() || $users->isEmpty()) {
            $this->command->info('Không có đủ dữ liệu để tạo booking notes. Vui lòng chạy seeder khác trước.');
            return;
        }

        $sampleNotes = [
            [
                'content' => 'Khách hàng yêu cầu check-in sớm lúc 10h sáng. Đã xác nhận phòng sẵn sàng.',
                'type' => 'customer',
                'visibility' => 'public',
                'is_internal' => false
            ],
            [
                'content' => 'Khách hàng có yêu cầu đặc biệt về chế độ ăn chay. Cần chuẩn bị thực đơn phù hợp.',
                'type' => 'customer',
                'visibility' => 'public',
                'is_internal' => false
            ],
            [
                'content' => 'Khách hàng đến với trẻ em. Cần chuẩn bị thêm chăn gối và đồ dùng cho trẻ.',
                'type' => 'staff',
                'visibility' => 'internal',
                'is_internal' => false
            ],
            [
                'content' => 'Khách hàng thanh toán bằng thẻ tín dụng. Đã xác nhận thông tin thanh toán.',
                'type' => 'admin',
                'visibility' => 'internal',
                'is_internal' => true
            ],
            [
                'content' => 'Khách hàng yêu cầu phòng ở tầng cao, view đẹp. Đã sắp xếp phòng 801.',
                'type' => 'staff',
                'visibility' => 'public',
                'is_internal' => false
            ],
            [
                'content' => 'Khách hàng có dị ứng với phấn hoa. Cần tránh trang trí hoa trong phòng.',
                'type' => 'customer',
                'visibility' => 'private',
                'is_internal' => false
            ],
            [
                'content' => 'Khách hàng là VIP. Cần chuẩn bị dịch vụ đặc biệt và chào đón nồng nhiệt.',
                'type' => 'admin',
                'visibility' => 'internal',
                'is_internal' => true
            ],
            [
                'content' => 'Khách hàng yêu cầu giặt ủi quần áo trong thời gian lưu trú.',
                'type' => 'staff',
                'visibility' => 'public',
                'is_internal' => false
            ],
            [
                'content' => 'Khách hàng đến bằng xe riêng. Cần chuẩn bị chỗ đậu xe.',
                'type' => 'customer',
                'visibility' => 'public',
                'is_internal' => false
            ],
            [
                'content' => 'Khách hàng có yêu cầu về thời gian dọn phòng. Chỉ dọn vào buổi chiều.',
                'type' => 'staff',
                'visibility' => 'internal',
                'is_internal' => false
            ]
        ];

        foreach ($bookings as $booking) {
            // Tạo 2-3 ghi chú cho mỗi booking
            $numNotes = rand(2, 3);
            $selectedNotes = array_rand($sampleNotes, $numNotes);
            
            if (!is_array($selectedNotes)) {
                $selectedNotes = [$selectedNotes];
            }

            foreach ($selectedNotes as $noteIndex) {
                $noteData = $sampleNotes[$noteIndex];
                $user = $users->random();

                BookingNote::create([
                    'booking_id' => $booking->id,
                    'user_id' => $user->id,
                    'content' => $noteData['content'],
                    'type' => $noteData['type'],
                    'visibility' => $noteData['visibility'],
                    'is_internal' => $noteData['is_internal'],
                    'created_at' => now()->subDays(rand(1, 30)),
                    'updated_at' => now()->subDays(rand(1, 30))
                ]);
            }
        }

        $this->command->info('Đã tạo ' . count($bookings) * 3 . ' booking notes mẫu.');
    }
}
