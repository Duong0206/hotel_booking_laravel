<?php

namespace App\Services;

use App\Interfaces\Services\ServiceServiceInterface;
use App\Interfaces\Repositories\ServiceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ServiceService implements ServiceServiceInterface
{
    protected $serviceRepository;

    public function __construct(ServiceRepositoryInterface $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    public function getAll(): Collection
    {
        return $this->serviceRepository->all();
    }

    public function getAllWithFilter(?int $categoryId = null, ?int $roomTypeId = null): Collection
    {
        return $this->serviceRepository->getAllWithFilter($categoryId, $roomTypeId);
    }

    public function getById(int $id)
    {
        return $this->serviceRepository->find($id);
    }

    public function create(array $data)
    {
        return $this->serviceRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->serviceRepository->update($id, $data);
    }

    public function delete(int $id)
    {
        return $this->serviceRepository->delete($id);
    }

    public function paginateWithFilter(?int $categoryId = null, ?int $roomTypeId = null, int $perPage = 10, ?string $keyword = null)
    {
        return $this->serviceRepository->paginateWithFilter($categoryId, $roomTypeId, $perPage, $keyword);
    }
} 