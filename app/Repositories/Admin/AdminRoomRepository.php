<?php

namespace App\Repositories\Admin;

use App\Models\Room;
use App\Models\RoomType;
use App\Interfaces\Repositories\Admin\AdminRoomRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminRoomRepository implements AdminRoomRepositoryInterface
{
    protected $roomModel;

    public function __construct(Room $roomModel)
    {
        $this->roomModel = $roomModel;
    }

    // ==================== ROOM METHODS ====================

    /**
     * Lấy tất cả phòng
     */
    public function getAll($status = null)
    {
        $query = Room::with(['roomType', 'bookings']);
    
        if ($status) {
            if ($status === 'booked') {
                // Lọc phòng có booking đang pending hoặc confirmed
                $query->whereHas('bookings', function($q) {
                    $q->whereIn('status', ['pending', 'confirmed']);
                })->orWhere('status', 'booked');;
            } else {
                // Lọc theo trạng thái phòng
                $query->where('status', $status);
            }
        }
    
        return $query->paginate(10);
    }

    /**
     * Lấy tất cả phòng với bộ lọc
     */
    public function getAllRoomsWithFilters($filters = [])
    {
        $query = Room::with(['roomType', 'bookings', 'primaryImage']);

        if (!empty($filters['floor'])) {
            $query->where('floor', $filters['floor']);
        }

        if (!empty($filters['room_type'])) {
            $query->where('room_type_id', $filters['room_type']);
        }

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'booked') {
                $query->whereHas('bookings', function($q) {
                    $q->whereIn('status', ['pending', 'confirmed']);
                })->orWhere('status', 'booked');
            } else {
                $query->where('status', $filters['status']);
            }
        }

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('room_number', 'like', '%' . $filters['search'] . '%')
                ->orWhere('floor', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->orderBy('floor', 'asc')
                    ->orderBy('room_number', 'asc')
                    ->get(); 
    }

    /**
     * Lấy phòng theo tầng
     */
    public function getRoomsByFloor($floor)
    {
        return Room::with(['roomType', 'bookings'])
                ->where('floor', $floor)
                ->orderBy('room_number', 'asc')
                ->get();
    }

    /**
     * Lấy danh sách tầng có sẵn
     */
    public function getAvailableFloors()
    {
        return Room::distinct()->pluck('floor')->sort()->values();
    }

    /**
     * Lấy tổng quan tầng
     */
    public function getFloorOverview()
    {
        $floors = range(1, 30); // Giả sử 30 tầng
        $floorData = [];    
        
        foreach ($floors as $floor) {
            $rooms = Room::where('floor', $floor)->get();
            $floorData[$floor] = [
                'total' => $rooms->count(),
                'available' => $rooms->where('status', 'available')->count(),
                'booked' => $rooms->where('status', 'booked')->count(),
                'repair' => $rooms->where('status', 'repair')->count(),
                'types' => $rooms->groupBy('room_type_id')->map->count()
            ];
        }
        
        return $floorData;
    }

    /**
     * Tạo hàng loạt phòng
     */
    public function bulkCreateRooms($data)
    {
        $floor = $data['floor'];
        $roomTypeId = $data['room_type_id'];
        $startNumber = $data['start_number'];
        $endNumber = $data['end_number'];
        $price = $data['price'] ?? null;
        
        $rooms = [];
        for ($i = $startNumber; $i <= $endNumber; $i++) {
            $roomNumber = str_pad($i, 2, '0', STR_PAD_LEFT); // 01, 02, 03...
            
            // Kiểm tra phòng đã tồn tại
            $exists = Room::where('floor', $floor)
                        ->where('room_number', $roomNumber)
                        ->exists();
            
            if (!$exists) {
                $rooms[] = [
                    'floor' => $floor,
                    'room_type_id' => $roomTypeId,
                    'room_number' => $roomNumber,
                    'status' => 'available',
                    'price' => $price,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }
        
        if (!empty($rooms)) {
            Room::insert($rooms);
        }
        
        return count($rooms);
    }

    /**
     * Tìm phòng theo ID
     */
    public function find($id)
    {
        return Room::with(['roomType', 'images'])->findOrFail($id);
    }

    /**
     * Tạo phòng mới
     */
    public function create(array $data)
    {
        return Room::create($data);
    }

    /**
     * Cập nhật phòng
     */
    public function update($id, array $data)
    {
        $room = Room::findOrFail($id);
        $room->update($data);
        return $room;
    }

    /**
     * Xóa phòng
     */
    public function delete($id)
    {
        $room = Room::with('bookings')->findOrFail($id);

        // Kiểm tra xem phòng có booking đang pending hoặc confirmed không
        if ($room->bookings()->whereIn('status', ['pending', 'confirmed'])->count() > 0) {
            return [
                'success' => false,
                'message' => 'Không thể xóa phòng vì đang có đơn đặt phòng đang xử lý!'
            ];
        }

        $room->delete();

        return [
            'success' => true,
            'message' => 'Xóa phòng thành công!'
        ];
    }

    /**
     * Lấy tất cả loại phòng
     *
     * @return Collection
     */
    public function getAllRoomTypes(): Collection
    {
        return RoomType::all();
    }
}