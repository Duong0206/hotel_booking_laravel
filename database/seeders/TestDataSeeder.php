<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoomType;
use App\Models\Room;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tạo các loại phòng
        $roomTypes = [
            [
                'name' => 'Phòng Đơn Tiêu Chuẩn',
                'description' => 'Phòng đơn tiêu chuẩn với đầy đủ tiện nghi cơ bản',
                'price' => 500000,
                'capacity' => 2
            ],
            [
                'name' => 'Phòng Đôi Tiêu Chuẩn',
                'description' => 'Phòng đôi tiêu chuẩn với đầy đủ tiện nghi cơ bản',
                'price' => 800000,
                'capacity' => 4
            ],
            [
                'name' => 'Phòng VIP',
                'description' => 'Phòng VIP với nhiều tiện nghi cao cấp',
                'price' => 1500000,
                'capacity' => 2
            ]
        ];
        
        foreach ($roomTypes as $type) {
            RoomType::create($type);
        }
        
        // 2. Tạo các phòng
        $floors = [1, 2, 3];
        $roomTypes = RoomType::all();
        
        foreach ($roomTypes as $type) {
            foreach ($floors as $floor) {
                // Mỗi loại phòng có 3 phòng mỗi tầng
                for ($i = 1; $i <= 3; $i++) {
                    $roomNumber = $floor . str_pad($i, 2, '0', STR_PAD_LEFT);
                    
                    Room::create([
                        'room_type_id' => $type->id,
                        'room_number' => $roomNumber,
                        'floor' => $floor,
                        'status' => 'available'
                    ]);
                }
            }
        }
    }
} 