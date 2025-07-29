<?php

namespace App\Interfaces\Services;

use Illuminate\Database\Eloquent\Collection;

interface ServiceServiceInterface
{
    public function getAll(): Collection;
    public function getAllWithFilter(?int $categoryId = null, ?int $roomTypeId = null): Collection;
    public function getById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function paginateWithFilter(?int $categoryId = null, ?int $roomTypeId = null, int $perPage = 10, ?string $keyword = null);
} 