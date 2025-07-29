<?php

namespace App\Services;

use App\Interfaces\Services\ServiceCategoryServiceInterface;
use App\Interfaces\Repositories\ServiceCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ServiceCategoryService implements ServiceCategoryServiceInterface
{
    protected $serviceCategoryRepository;

    public function __construct(ServiceCategoryRepositoryInterface $serviceCategoryRepository)
    {
        $this->serviceCategoryRepository = $serviceCategoryRepository;
    }

    public function getAll(): Collection
    {
        return $this->serviceCategoryRepository->all();
    }

    public function getById(int $id)
    {
        return $this->serviceCategoryRepository->find($id);
    }

    public function create(array $data)
    {
        return $this->serviceCategoryRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->serviceCategoryRepository->update($id, $data);
    }

    public function delete(int $id)
    {
        return $this->serviceCategoryRepository->delete($id);
    }
} 