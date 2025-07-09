<?php

namespace App\Interfaces\Repositories\Admin;

use App\Models\Promotion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface AdminPromotionRepositoryInterface
{
    /**
     * Lấy tất cả promotion với phân trang
     */
    public function getAllWithPagination(int $perPage = 15): LengthAwarePaginator;

    /**
     * Lấy promotion theo bộ lọc
     */
    public function getByFilters(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * Tìm promotion theo ID
     */
    public function findById(int $id): ?Promotion;

    /**
     * Tạo promotion mới
     */
    public function create(array $data): Promotion;

    /**
     * Cập nhật promotion
     */
    public function update(int $id, array $data): bool;

    /**
     * Xóa promotion
     */
    public function delete(int $id): bool;

    /**
     * Kiểm tra mã promotion có tồn tại
     */
    public function codeExists(string $code, ?int $excludeId = null): bool;

    /**
     * Lấy thống kê promotion
     */
    public function getStats(): array;

    /**
     * Toggle trạng thái active
     */
    public function toggleActive(int $id): bool;

    /**
     * Toggle trạng thái featured
     */
    public function toggleFeatured(int $id): bool;
} 