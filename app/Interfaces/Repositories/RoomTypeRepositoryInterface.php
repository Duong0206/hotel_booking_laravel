<?php

namespace App\Interfaces\Repositories;

use App\Models\RoomType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

interface RoomTypeRepositoryInterface
{
    public function newQuery(): Builder;

    public function getAllRoomTypes(): Collection;

    public function getAllRoomTypesWithServices(): Collection;

    public function findById(int $id): ?RoomType;

    /**
     * Tìm kiếm loại phòng theo các tiêu chí
     */
    public function searchRoomTypes(array $filters): Collection;

    /**
     * Lấy tất cả loại phòng với khuyến mại đang áp dụng
     */
    public function getAllRoomTypesWithPromotions(): Collection;
}
