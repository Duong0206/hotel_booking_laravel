<?php

namespace App\Interfaces\Services;

use Illuminate\Database\Eloquent\Collection;

interface UserServiceInterface
{
    /**
     * Lấy tất cả người dùng
     *
     * @return Collection
     */
    public function getAll(): Collection;

    /**
     * Tìm người dùng theo ID
     *
     * @param int $id
     * @return mixed
     */
    public function findById(int $id);

    /**
     * Cập nhật thông tin người dùng
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateUser(int $id, array $data): bool;

    /**
     * Xóa người dùng
     *
     * @param int $id
     * @return bool
     */
    public function deleteUser(int $id): bool;
} 