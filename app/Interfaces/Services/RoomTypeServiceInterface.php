<?php

namespace App\Interfaces\Services;

use Illuminate\Database\Eloquent\Collection;

interface RoomTypeServiceInterface
{
    public function getAllRoomTypes(): Collection;

    public function findById(int $id);

    public function searchRoomTypes(array $filters);
}
