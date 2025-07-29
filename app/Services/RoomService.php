<?php

namespace App\Services;

use App\Interfaces\Services\RoomServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Interfaces\Repositories\RoomRepositoryInterface;
use App\Models\Room;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RoomService implements RoomServiceInterface
{
    protected $roomRepository;

    public function __construct(RoomRepositoryInterface $roomRepository)
    {
        $this->roomRepository = $roomRepository;
    }

    public function getAll(): Collection
    {
        return $this->roomRepository->getAll();
    }

    public function findById(int $id)
    {
        return $this->roomRepository->findById($id);
    }

    public function validateFilters(array $filters)
    {
        $validator = Validator::make($filters, [
            'keyword'   => 'nullable|string',
            'type'      => 'nullable|integer',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function searchRooms(array $filters): Collection
    {
        $this->validateFilters($filters);
        $query = $this->roomRepository->newQuery();

        $query->where(function ($q) use ($filters) {
            // Tìm theo keyword
            if (!empty($filters['keyword'])) {
                $keyword = $filters['keyword'];
                $q->where(function ($subQuery) use ($keyword) {
                    $subQuery->where('room_number', 'like', '%' . $keyword . '%')
                        ->orWhereHas('roomType', function ($typeQuery) use ($keyword) {
                            $typeQuery->where('name', 'like', '%' . $keyword . '%');
                        });
                });
            }

            // Tìm theo loại phòng
            if (!empty($filters['type'])) {
                $q->orWhere('room_type_id', $filters['type']);
            }
        });

        // Lọc theo khoảng giá (giữ nguyên là AND với các điều kiện trên)
        if (!empty($filters['price_min']) || !empty($filters['price_max'])) {
            $query->whereHas('roomType', function ($q) use ($filters) {
                if (!empty($filters['price_min'])) {
                    $q->where('price', '>=', (int) $filters['price_min']);
                }
                if (!empty($filters['price_max'])) {
                    $q->where('price', '<=', (int) $filters['price_max']);
                }
            });
        }

        $query->orderBy('room_number');
        return $query->get();
    }
}
