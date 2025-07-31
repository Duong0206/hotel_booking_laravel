<?php

namespace App\Interfaces\Services;

use Illuminate\Database\Eloquent\Collection;

interface RoomTypeServiceInterface
{
    public function getAllRoomTypes(): Collection;

    public function findById(int $id);

    public function searchRoomTypes(array $filters);

    /**
     * Lấy tất cả loại phòng với khuyến mại đang áp dụng
     */
    public function getAllRoomTypesWithPromotions(): Collection;

    /**
     * Lấy khuyến mại đang áp dụng cho một loại phòng cụ thể
     */
    public function getActivePromotionsForRoomType(int $roomTypeId): Collection;

    /**
     * Tìm kiếm loại phòng với khuyến mại
     */
    public function searchRoomTypesWithPromotions(array $filters): Collection;
}
