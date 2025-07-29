<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Support\Facades\DB;

class NewRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // xoá dữ liệu cũ 
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('rooms')->truncate();
        DB::table('room_types')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        // Đầu tiên tạo các loại phòng
        $roomTypes = [
            [
                'name' => 'Phòng Đơn Tiêu Chuẩn',
                'description' => 'Phòng đơn tiêu chuẩn với đầy đủ tiện nghi cơ bản, phù hợp cho 1-2 người.',
                'price' => 500000,
                'capacity' => 2
            ],
            [
                'name' => 'Phòng Đôi Tiêu Chuẩn',
                'description' => 'Phòng đôi tiêu chuẩn với 2 giường đơn, thích hợp cho gia đình nhỏ hoặc nhóm bạn.',
                'price' => 800000,
                'capacity' => 4 
            ],
            [
                'name' => 'Phòng Deluxe',
                'description' => 'Phòng deluxe với thiết kế sang trọng, đầy đủ tiện nghi cao cấp, view đẹp.',
                'price' => 1200000,
                'capacity' => 3
            ],
            [
                'name' => 'Phòng Suite',
                'description' => 'Phòng suite sang trọng bậc nhất với không gian rộng rãi, bao gồm phòng khách và phòng ngủ riêng biệt.',
                'price' => 2000000,
                'capacity' => 4
            ],
            [
                'name' => 'Phòng Gia Đình',
                'description' => 'Phòng gia đình rộng rãi với 2 phòng ngủ, phù hợp cho gia đình 4-6 người.',
                'price' => 1500000,
                'capacity' => 6
            ],
        ];
        
        // Tạo các room types
        $createdRoomTypes = [];
        foreach ($roomTypes as $roomType) {
            $createdRoomTypes[] = RoomType::create($roomType);
        }
        
        // Tạo 300 phòng cho 30 tầng (mỗi tầng 10 phòng)
        $totalFloors = 30;
        $roomsPerFloor = 10;
        
        for ($floor = 1; $floor <= $totalFloors; $floor++) {
            for ($roomNumber = 1; $roomNumber <= $roomsPerFloor; $roomNumber++) {
                // Phân bố loại phòng theo pattern:
                // Phòng 1-2: Phòng Đơn
                // Phòng 3-4: Phòng Đôi  
                // Phòng 5-6: Phòng Deluxe
                // Phòng 7-8: Phòng Suite
                // Phòng 9-10: Phòng Gia Đình
                
                $roomTypeIndex = floor(($roomNumber - 1) / 2);
                if ($roomTypeIndex >= count($createdRoomTypes)) {
                    $roomTypeIndex = count($createdRoomTypes) - 1; // Fallback
                }
                
                $roomType = $createdRoomTypes[$roomTypeIndex];
                
                // Tạo số phòng theo format: Tầng + Số phòng (VD: 101, 102, 201, 202...)
                $formattedRoomNumber = $floor . str_pad($roomNumber, 2, '0', STR_PAD_LEFT);
                
                // Random trạng thái (chủ yếu available, một số ít booked/repair)
                $statuses = ['available', 'available', 'available', 'available', 'available', 
                           'available', 'available', 'available', 'booked', 'repair'];
                $status = $statuses[array_rand($statuses)];
                
                Room::create([
                    'room_type_id' => $roomType->id,
                    'floor' => $floor,
                    'room_number' => $formattedRoomNumber,
                    'status' => $status,
                    'price' => $roomType->price,
                    'capacity' => $roomType->capacity,
                ]);
            }
        }
        
        $this->command->info("Đã tạo " . count($createdRoomTypes) . " loại phòng và " . ($totalFloors * $roomsPerFloor) . " phòng mẫu!");
    }
}