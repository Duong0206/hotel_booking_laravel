<?php

namespace App\Interfaces\Services;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

interface RoomServiceInterface
{
    /**
     * Lấy tất cả phòng
     *
     * @return Collection
     */
    public function getAll(): Collection;

    /**
     * Tìm phòng theo ID
     *
     * @param int $id
     * @return mixed
     */
    public function findById(int $id);

    /**
     * Tìm kiếm phòng theo các bộ lọc
     *
     * @return array
     */
    public function searchRooms(array $filters);
}