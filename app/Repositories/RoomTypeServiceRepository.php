<?php

namespace App\Repositories;

use App\Interfaces\Repositories\RoomTypeServiceRepositoryInterface;
use App\Models\RoomTypeService;
use Illuminate\Database\Eloquent\Collection;

class RoomTypeServiceRepository implements RoomTypeServiceRepositoryInterface
{
    public function getByRoomType(int $roomTypeId): Collection
    {
        return RoomTypeService::where('room_type_id', $roomTypeId)->get();
    }

    public function syncServices(int $roomTypeId, array $serviceIds)
    {
        // Xóa các dịch vụ cũ không còn trong danh sách
        RoomTypeService::where('room_type_id', $roomTypeId)
            ->whereNotIn('service_id', $serviceIds)
            ->delete();
        // Thêm các dịch vụ mới
        foreach ($serviceIds as $serviceId) {
            RoomTypeService::firstOrCreate([
                'room_type_id' => $roomTypeId,
                'service_id' => $serviceId
            ]);
        }
    }

    public function all(): Collection
    {
        return RoomTypeService::all();
    }

    public function find(int $id)
    {
        return RoomTypeService::find($id);
    }

    public function create(array $data)
    {
        return RoomTypeService::create($data);
    }

    public function delete(int $id)
    {
        $item = RoomTypeService::findOrFail($id);
        return $item->delete();
    }
} 