<?php

namespace App\Repositories;

use App\Models\Room;
use App\Interfaces\Repositories\RoomRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

class RoomRepository implements RoomRepositoryInterface
{
    protected Room $model;

    public function __construct(Room $model)
    {
        $this->model = $model;
    }

    public function getAll(): Collection
    {
        return $this->model->all();
    }

    public function findById(int $id): ?Room
    {
        return $this->model->with('roomType')->find($id);
    }

    public function newQuery(): Builder
    {
        return $this->model->newQuery();
    }

    public function search(array $filters): Collection
    {
        return $this->model->newQuery()->get();
    }
    
    public function findAvailableRoomByType(int $roomTypeId, string $checkInDateTime, string $checkOutDateTime): ?Room
    {
        return $this->model
            ->where('room_type_id', $roomTypeId)
            ->whereDoesntHave('bookings', function ($query) use ($checkInDateTime, $checkOutDateTime) {
                $query->where(function ($q) use ($checkInDateTime, $checkOutDateTime) {
                    $q->where('check_in_date', '<', $checkOutDateTime)
                      ->where('check_out_date', '>', $checkInDateTime);
                })->where('status', '!=', 'cancelled');
            })
            ->first();
    }

    public function getByRoomType(int $roomTypeId, int $limit = null): Collection
    {
        $query = $this->model->where('room_type_id', $roomTypeId)->with('roomType');
        
        if ($limit) {
            $query->limit($limit);
        }
        
        return $query->get();
    }
}
