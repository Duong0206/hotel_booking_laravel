<?php

namespace App\Interfaces\Services;

use Illuminate\Database\Eloquent\Collection;

interface ServiceCategoryServiceInterface
{
    public function getAll(): Collection;
    public function getById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
} 