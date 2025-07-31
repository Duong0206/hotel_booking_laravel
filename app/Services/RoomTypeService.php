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

        return $this->roomTypeRepository->searchRoomTypes($filters);
    }

    /**
     * Lấy tất cả loại phòng với khuyến mại đang áp dụng
     */
    public function getAllRoomTypesWithPromotions(): Collection
    {
        return $this->roomTypeRepository->getAllRoomTypesWithPromotions();
    }

    /**
     * Lấy khuyến mại đang áp dụng cho một loại phòng cụ thể
     */
    public function getActivePromotionsForRoomType(int $roomTypeId): Collection
    {
        $roomType = $this->roomTypeRepository->findById($roomTypeId);
        if (!$roomType) {
            return collect();
        }

        return $roomType->promotions()
            ->where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where('expired_at', '>', now())
            ->get();
    }

    /**
     * Tìm kiếm loại phòng với khuyến mại
     */
    public function searchRoomTypesWithPromotions(array $filters): Collection
    {
        $this->validateFilters($filters);
        
        // Lấy tất cả loại phòng với khuyến mại
        $roomTypes = $this->roomTypeRepository->getAllRoomTypesWithPromotions();
        
        // Áp dụng filter
        return $roomTypes->filter(function($roomType) use ($filters) {
            $match = true;
            
            if (!empty($filters['keyword'])) {
                $match = $match && (stripos($roomType->name, $filters['keyword']) !== false);
            }
            
            if (!empty($filters['type'])) {
                $match = $match && ($roomType->id == $filters['type']);
            }
            
            if (!empty($filters['price_min'])) {
                $match = $match && ($roomType->price >= (int)$filters['price_min']);
            }
            
            if (!empty($filters['price_max'])) {
                $match = $match && ($roomType->price <= (int)$filters['price_max']);
            }
            
            return $match;
        })->sortBy('price');
    }
}
