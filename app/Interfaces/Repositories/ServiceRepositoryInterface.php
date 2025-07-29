<?php

namespace App\Interfaces\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface ServiceRepositoryInterface
{
    public function all(): Collection;
    public function getAllWithFilter(?int $categoryId = null, ?int $roomTypeId = null): Collection;
    public function find(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function paginateWithFilter(?int $categoryId = null, ?int $roomTypeId = null, int $perPage = 10, ?string $keyword = null);
} 