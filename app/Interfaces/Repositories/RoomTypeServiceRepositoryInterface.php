<?php

namespace App\Interfaces\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface RoomTypeServiceRepositoryInterface
{
    public function getByRoomType(int $roomTypeId): Collection;
    public function syncServices(int $roomTypeId, array $serviceIds);
    public function all(): Collection;
    public function find(int $id);
    public function create(array $data);
    public function delete(int $id);
}
