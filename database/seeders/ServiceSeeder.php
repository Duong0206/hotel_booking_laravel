<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceCategory;
use App\Models\Service;
use App\Models\RoomType;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Thông tin phòng',
                'services' => [
                    ['name' => 'Diện tích 20m²', 'description' => 'Phòng diện tích 20 mét vuông', 'price' => 0, 'quantity' => 1],
                    ['name' => '1 người', 'description' => 'Sức chứa tối đa 1 người', 'price' => 0, 'quantity' => 1],
                    ['name' => '2 người', 'description' => 'Sức chứa tối đa 2 người', 'price' => 0, 'quantity' => 1],
                    ['name' => '3 người', 'description' => 'Sức chứa tối đa 3 người', 'price' => 0, 'quantity' => 1],
                    ['name' => '4 người', 'description' => 'Sức chứa tối đa 4 người', 'price' => 0, 'quantity' => 1],
                    ['name' => '6 người', 'description' => 'Sức chứa tối đa 6 người', 'price' => 0, 'quantity' => 1],
                    ['name' => '1 giường đôi', 'description' => 'Phòng có 1 giường đôi', 'price' => 0, 'quantity' => 1],
                    ['name' => '2 giường đôi', 'description' => 'Phòng có 2 giường đôi rộng rãi', 'price' => 0, 'quantity' => 2],
                    ['name' => '1 giường đôi + 1 giường phụ', 'description' => 'Phòng có giường đôi và giường phụ', 'price' => 0, 'quantity' => 2],
                    ['name' => '2 giường đôi + 2 giường đơn', 'description' => 'Phòng có 2 giường đôi và 2 giường đơn', 'price' => 0, 'quantity' => 4],
                ]
            ],
            [
                'name' => 'Tính năng nổi bật',
                'services' => [
                    ['name' => 'Ban công riêng', 'description' => 'Ban công riêng với view đẹp', 'price' => 0, 'quantity' => 1],
                    ['name' => 'View thành phố', 'description' => 'Tầm nhìn ra trung tâm thành phố', 'price' => 0, 'quantity' => 1],
                    ['name' => 'View biển', 'description' => 'Tầm nhìn hướng biển', 'price' => 0, 'quantity' => 1],
                    ['name' => 'Có khu bếp riêng', 'description' => 'Có khu bếp nhỏ trong phòng', 'price' => 0, 'quantity' => 1],
                    ['name' => 'Có bàn ăn', 'description' => 'Phòng có bàn ăn riêng', 'price' => 0, 'quantity' => 1],
                    ['name' => 'Có thể kê giường phụ', 'description' => 'Có thể kê thêm giường phụ theo yêu cầu', 'price' => 0, 'quantity' => 1],
                    ['name' => 'Có cũi cho trẻ em', 'description' => 'Cung cấp cũi miễn phí cho trẻ nhỏ', 'price' => 0, 'quantity' => 1],
                    ['name' => 'Phòng khách riêng', 'description' => 'Phòng có không gian khách riêng biệt', 'price' => 0, 'quantity' => 1],
                ]
            ],
            [
                'name' => 'Tiện nghi trong phòng',
                'services' => [
                    ['name' => 'Wifi miễn phí', 'description' => 'Internet tốc độ cao', 'price' => 0, 'quantity' => 1],
                    ['name' => 'TV màn hình phẳng', 'description' => 'Tivi LCD với truyền hình cáp', 'price' => 0, 'quantity' => 1],
                    ['name' => 'Máy lạnh', 'description' => 'Điều hòa không khí hiện đại', 'price' => 0, 'quantity' => 1],
                    ['name' => 'Tủ lạnh mini', 'description' => 'Tủ lạnh mini chứa đồ uống', 'price' => 0, 'quantity' => 1],
                    ['name' => 'Nước uống miễn phí', 'description' => 'Nước đóng chai miễn phí mỗi ngày', 'price' => 0, 'quantity' => 4],
                    ['name' => 'Trà & cà phê', 'description' => 'Trà và cà phê miễn phí', 'price' => 0, 'quantity' => 4],
                    ['name' => 'Ấm siêu tốc', 'description' => 'Ấm đun nước điện', 'price' => 0, 'quantity' => 1],
                    ['name' => 'Ly tách, cốc sứ', 'description' => 'Bộ ly tách cho trà & cà phê', 'price' => 0, 'quantity' => 4],
                    ['name' => 'Bàn làm việc', 'description' => 'Bàn làm việc gọn gàng', 'price' => 0, 'quantity' => 1],
                    ['name' => 'Sofa thư giãn', 'description' => 'Ghế sofa êm ái để nghỉ ngơi', 'price' => 0, 'quantity' => 1],
                    ['name' => 'Tủ quần áo', 'description' => 'Tủ gỗ đựng đồ', 'price' => 0, 'quantity' => 1],
                    ['name' => 'Két an toàn', 'description' => 'Két sắt bảo mật', 'price' => 0, 'quantity' => 1],
                ]
            ],
            [
                'name' => 'Tiện nghi phòng tắm',
                'services' => [
                    ['name' => 'Phòng tắm riêng', 'description' => 'Phòng tắm riêng tư', 'price' => 0, 'quantity' => 1],
                    ['name' => 'Vòi sen', 'description' => 'Vòi sen hiện đại', 'price' => 0, 'quantity' => 1],
                    ['name' => 'Bồn tắm', 'description' => 'Bồn tắm nằm', 'price' => 0, 'quantity' => 1],
                    ['name' => 'Khăn tắm', 'description' => 'Khăn tắm mềm mịn', 'price' => 0, 'quantity' => 4],
                    ['name' => 'Bàn chải đánh răng', 'description' => 'Bộ bàn chải + kem đánh răng', 'price' => 0, 'quantity' => 4],
                    ['name' => 'Xà phòng, dầu gội', 'description' => 'Đầy đủ xà phòng và dầu gội', 'price' => 0, 'quantity' => 2],
                    ['name' => 'Máy sấy tóc', 'description' => 'Máy sấy tóc mini', 'price' => 0, 'quantity' => 1],
                    ['name' => 'Gương soi lớn', 'description' => 'Gương toàn thân trong phòng tắm', 'price' => 0, 'quantity' => 1],
                    ['name' => 'Thảm chống trơn', 'description' => 'Thảm lót sàn chống trượt', 'price' => 0, 'quantity' => 1],
                    ['name' => 'Áo choàng tắm', 'description' => 'Áo choàng cao cấp', 'price' => 0, 'quantity' => 2],
                ]
            ]
        ];

        foreach ($categories as $categoryData) {
            $category = ServiceCategory::create(['name' => $categoryData['name']]);

            foreach ($categoryData['services'] as $serviceData) {
                Service::create([
                    'name' => $serviceData['name'],
                    'description' => $serviceData['description'],
                    'price' => $serviceData['price'],
                    'quantity' => $serviceData['quantity'] ?? 1,
                    'service_category_id' => $category->id
                ]);
            }
        }

        // Gán dịch vụ vào loại phòng
        $roomServiceMap = [
            'Phòng đơn tiêu chuẩn' => ['2 người', '1 giường đôi', 'Wifi miễn phí', 'TV màn hình phẳng', 'Máy lạnh', 'Tủ lạnh mini', 'Nước uống miễn phí', 'Trà & cà phê', 'Ấm siêu tốc', 'Tủ quần áo', 'Phòng tắm riêng', 'Khăn tắm', 'Bàn chải đánh răng', 'Xà phòng, dầu gội', 'Gương soi lớn'],
            'Phòng đôi tiêu chuẩn' => ['4 người', '2 giường đôi', 'Wifi miễn phí', 'TV màn hình phẳng', 'Máy lạnh', 'Tủ lạnh mini', 'Nước uống miễn phí', 'Trà & cà phê', 'Tủ quần áo', 'Phòng tắm riêng', 'Vòi sen', 'Khăn tắm', 'Bàn chải đánh răng', 'Xà phòng, dầu gội', 'Máy sấy tóc'],
            'Phòng Deluxe' => ['3 người', '1 giường đôi + 1 giường phụ', 'View thành phố', 'Ban công riêng', 'Wifi miễn phí', 'TV màn hình phẳng', 'Máy lạnh', 'Tủ lạnh mini', 'Nước uống miễn phí', 'Trà & cà phê', 'Ấm siêu tốc', 'Ly tách, cốc sứ', 'Bàn làm việc', 'Phòng tắm riêng', 'Bồn tắm', 'Máy sấy tóc', 'Khăn tắm', 'Gương soi lớn', 'Áo choàng tắm'],
            'Phòng Suite' => ['4 người', '2 giường đôi', 'Ban công riêng', 'View biển', 'Có khu bếp riêng', 'Phòng khách riêng', 'Wifi miễn phí', 'Máy lạnh', 'TV màn hình phẳng', 'Sofa thư giãn', 'Bàn làm việc', 'Két an toàn', 'Tủ quần áo', 'Phòng tắm riêng', 'Bồn tắm', 'Áo choàng tắm'],
            'Phòng Gia đình' => ['6 người', '2 giường đôi + 2 giường đơn', 'Có khu bếp riêng', 'Phòng khách riêng', 'Có cũi cho trẻ em', 'Wifi miễn phí', 'TV màn hình phẳng', 'Máy lạnh', 'Tủ lạnh mini', 'Trà & cà phê', 'Nước uống miễn phí', 'Ly tách, cốc sứ', 'Phòng tắm riêng', 'Máy sấy tóc', 'Bàn chải đánh răng', 'Khăn tắm', 'Thảm chống trơn', 'Gương soi lớn'],
        ];

        foreach ($roomServiceMap as $roomName => $serviceNames) {
            $roomType = RoomType::where('name', $roomName)->first();
            if ($roomType) {
                $serviceIds = Service::whereIn('name', $serviceNames)->pluck('id');
                $roomType->services()->syncWithoutDetaching($serviceIds);
            }
        }
    }
}
