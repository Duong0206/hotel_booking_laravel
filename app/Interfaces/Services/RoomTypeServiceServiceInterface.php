<?php

namespace App\Interfaces\Services;

use Illuminate\Database\Eloquent\Collection;

interface RoomTypeServiceServiceInterface
{
    public function getServicesByRoomType(int $roomTypeId): Collection;
    public function syncServices(int $roomTypeId, array $serviceIds);
    public function all(): Collection;
    public function find(int $id);
    public function create(array $data);
    public function delete(int $id);
} 