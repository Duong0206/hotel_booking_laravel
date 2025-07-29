<?php

namespace App\Services;

use App\Interfaces\Services\RoomTypeServiceServiceInterface;
use App\Interfaces\Repositories\RoomTypeServiceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class RoomTypeServiceService implements RoomTypeServiceServiceInterface
{
    protected $roomTypeServiceRepository;

    public function __construct(RoomTypeServiceRepositoryInterface $roomTypeServiceRepository)
    {
        $this->roomTypeServiceRepository = $roomTypeServiceRepository;
    }

    public function getServicesByRoomType(int $roomTypeId): Collection
    {
        return $this->roomTypeServiceRepository->getByRoomType($roomTypeId);
    }

    public function syncServices(int $roomTypeId, array $serviceIds)
    {
        return $this->roomTypeServiceRepository->syncServices($roomTypeId, $serviceIds);
    }

    public function all(): Collection
    {
        return $this->roomTypeServiceRepository->all();
    }

    public function find(int $id)
    {
        return $this->roomTypeServiceRepository->find($id);
    }

    public function create(array $data)
    {
        return $this->roomTypeServiceRepository->create($data);
    }

    public function delete(int $id)
    {
        return $this->roomTypeServiceRepository->delete($id);
    }
}
