<?php

namespace App\Repositories;

use App\Interfaces\Repositories\ServiceCategoryRepositoryInterface;
use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Collection;

class ServiceCategoryRepository implements ServiceCategoryRepositoryInterface
{
    public function all(): Collection
    {
        return ServiceCategory::all();
    }

    public function find(int $id)
    {
        return ServiceCategory::find($id);
    }

    public function create(array $data)
    {
        return ServiceCategory::create($data);
    }

    public function update(int $id, array $data)
    {
        $category = ServiceCategory::findOrFail($id);
        $category->update($data);
        return $category;
    }

    public function delete(int $id)
    {
        $category = ServiceCategory::findOrFail($id);
        return $category->delete();
    }
} 