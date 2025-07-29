<?php

namespace App\Interfaces\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

interface RoomTypeRepositoryInterface
{
    /**
     * 
     *
     * @return Builder
     */
    public function newQuery(): Builder;
    /**
     * Lấy tất cả loại phòng
     *
     * @return Collection
     */
    public function getAllRoomTypes(): Collection;

    /**
     * Lấy tất cả loại phòng với relationship services
     *
     * @return Collection
     */
    public function getAllRoomTypesWithServices(): Collection;
}
