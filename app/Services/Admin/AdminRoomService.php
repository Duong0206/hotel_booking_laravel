<?php

namespace App\Services\Admin;

use App\Repositories\Admin\AdminRoomRepository;
use App\Interfaces\Services\Admin\AdminRoomServiceInterface;
use App\Interfaces\Repositories\Admin\AdminRoomRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class AdminRoomService implements AdminRoomServiceInterface
{
    protected $roomRepository;

    public function __construct(AdminRoomRepositoryInterface $roomRepository)
    {
        $this->roomRepository = $roomRepository;
    }
    public function getAllRooms($status = null)
    {
        return $this->roomRepository->getAll($status);
    }

    public function getAllRoomsWithFilters($filters)
    {
        return $this->roomRepository->getAllRoomsWithFilters($filters);
    }

    public function getRoomsByFloor($floor)
    {
        return $this->roomRepository->getRoomsByFloor($floor);
    }
    
    public function getAvailableFloors()
    {
        return $this->roomRepository->getAvailableFloors();
    }
    
    public function getFloorOverview()
    {
        return $this->roomRepository->getFloorOverview();
    }
    
    public function bulkCreateRooms($data)
    {
        return $this->roomRepository->bulkCreateRooms($data);
    }

    public function getRoom($id)
    {
        return $this->roomRepository->find($id);
    }

    public function getRoomTypes(): Collection
    {
        return $this->roomRepository->getAllRoomTypes();
    }

    public function createRoom(array $data)
    {
        $this->validateRoomData($data);
        // Xử lý upload ảnh nếu có
        if (isset($data['images']) && is_array($data['images'])) {
            $room = $this->roomRepository->create($data);
            $this->handleImageUpload($room, $data['images']);
            return $room;
        }
        return $this->roomRepository->create($data);
    }

    public function updateRoom($id, array $data)
    {
        $this->validateRoomData($data);
        $room = $this->roomRepository->find($id);
        
        // Xử lý upload ảnh nếu có
        if (isset($data['images']) && is_array($data['images'])) {
            $this->handleImageUpload($room, $data['images']);
        }
        return $this->roomRepository->update($id, $data);
    }

    public function deleteRoom($id): array
    {
        $room = $this->roomRepository->find($id);
        
        // Xóa tất cả ảnh của phòng
        if ($room->images) {
            foreach ($room->images as $image) {
                // Xóa file ảnh
                if (Storage::disk('public')->exists($image->image_url)) {
                    Storage::disk('public')->delete($image->image_url);
                }
            }
        }
        // Xóa phòng (ảnh sẽ tự động bị xóa do cascade)
        return $this->roomRepository->delete($id);
    }

    /**
     * Xử lý upload ảnh cho phòng
     */
    private function handleImageUpload($room, array $images)
    {
        // Kiểm tra xem phòng đã có ảnh chính chưa
        $hasPrimaryImage = $room->images()->where('is_primary', true)->exists();
        foreach ($images as $index => $image) {
            if ($image && $image->isValid()) {
                $path = $image->store('rooms/' . $room->id, 'public');
                
                // Chỉ ảnh đầu tiên và phòng chưa có ảnh chính mới đặt làm ảnh chính
                $isPrimary = ($index === 0 && !$hasPrimaryImage);
                
                $room->images()->create([
                    'image_url' => $path,
                    'is_primary' => $isPrimary
                ]);
            }
        }
    }

    /**
     * Xóa ảnh của phòng
     */
    public function deleteRoomImage($roomId, $imageId): array
    {
        $room = $this->roomRepository->find($roomId);
        $image = $room->images()->find($imageId);
        
        if (!$image) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy ảnh!'
            ];
        }
        
        // Xóa file ảnh
        Storage::disk('public')->delete($image->image_url);
        
        // Xóa record trong database
        $image->delete();
        
        return [
            'success' => true,
            'message' => 'Xóa ảnh thành công!'
        ];
    }

    /**
     * Đặt ảnh làm ảnh chính
     */
    public function setPrimaryImage($roomId, $imageId): array
    {
        $room = $this->roomRepository->find($roomId);
        $image = $room->images()->find($imageId);
        
        if (!$image) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy ảnh!'
            ];
        }
        
        // Bỏ ảnh chính hiện tại
        $room->images()->where('is_primary', true)->update(['is_primary' => false]);
        
        // Đặt ảnh mới làm ảnh chính
        $image->update(['is_primary' => true]);
        
        return [
            'success' => true,
            'message' => 'Đặt ảnh chính thành công!'
        ];
    }

    /**
     * Validate dữ liệu phòng
     *
     * @param array $data
     * @throws ValidationException
     */
    protected function validateRoomData(array $data)
{
    $validator = Validator::make($data, [
        'room_type_id' => 'required|exists:room_types,id',
        'room_number' => 'required|string|max:20',
        'status' => 'required|in:available,booked,repair',
        'price' => 'nullable|numeric|min:0',
        'capacity' => 'nullable|integer|min:1','images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // Kiểm tra validate cơ bản
    if ($validator->fails()) {
        throw new ValidationException($validator);
    }

    
}
}