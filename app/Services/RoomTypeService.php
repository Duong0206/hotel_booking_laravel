<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use App\Interfaces\Services\RoomTypeServiceInterface;
use App\Interfaces\Repositories\RoomTypeRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RoomTypeService implements RoomTypeServiceInterface
{
    protected RoomTypeRepositoryInterface $roomTypeRepository;

    public function __construct(RoomTypeRepositoryInterface $roomTypeRepository)
    {
        $this->roomTypeRepository = $roomTypeRepository;
    }

    public function getAllRoomTypes(): Collection
    {
        return $this->roomTypeRepository->getAllRoomTypesWithServices();
    }

    public function findById(int $id)
    {
        return $this->roomTypeRepository->findById($id);
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
    public function searchRoomTypes(array $filters)
    {
        $this->validateFilters($filters);

        $query = $this->roomTypeRepository->newQuery();

        // Gộp tìm theo keyword hoặc loại phòng
        if (!empty($filters['keyword']) || !empty($filters['type'])) {
            $query->where(function ($q) use ($filters) {
                if (!empty($filters['keyword'])) {
                    $q->orWhere('name', 'like', '%' . $filters['keyword'] . '%');
                }

                if (!empty($filters['type'])) {
                    $q->orWhere('id', $filters['type']);
                }
            });
        }

        // Lọc theo khoảng giá (giữ nguyên là AND với các điều kiện trên)
        if (!empty($filters['price_min'])) {
            $query->where('price', '>=', (int)$filters['price_min']);
        }

        if (!empty($filters['price_max'])) {
            $query->where('price', '<=', (int)$filters['price_max']);
        }

        return $query->orderBy('price')->get();
    }
    
}
