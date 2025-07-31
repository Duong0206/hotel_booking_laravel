<?php

namespace App\Interfaces\Repositories;

use App\Models\Promotion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface PromotionRepositoryInterface
{
    /**
     * Lấy tất cả promotion
     *
     * @return Collection
     */
    public function getAll(): Collection;

    /**
     * Lấy promotion có phân trang
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllWithPagination(int $perPage = 12): LengthAwarePaginator;

    /**
     * Lấy promotion đang hoạt động
     *
     * @return Collection
     */
    public function getActive(): Collection;

    /**
     * Lấy promotion nổi bật
     *
     * @param int $limit
     * @return Collection
     */
    public function getFeatured(int $limit = 3): Collection;

    /**
     * Tìm promotion theo ID
     *
     * @param int $id
     * @return Promotion|null
     */
    public function findById(int $id): ?Promotion;

    /**
     * Tìm promotion theo mã code
     *
     * @param string $code
     * @return Promotion|null
     */
    public function findByCode(string $code): ?Promotion;

    /**
     * Tạo promotion mới
     *
     * @param array $data
     * @return Promotion
     */
    public function create(array $data): Promotion;

    /**
     * Cập nhật promotion
     *
     * @param Promotion $promotion
     * @param array $data
     * @return bool
     */
    public function update(Promotion $promotion, array $data): bool;

    /**
     * Xóa promotion
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Tăng số lần sử dụng promotion
     *
     * @param int $id
     * @return bool
     */
    public function incrementUsedCount(int $id): bool;

    /**
     * Lấy promotion theo bộ lọc
     *
     * @param array $filters
     * @return Collection
     */
    public function getByFilters(array $filters): Collection;

    /**
     * Lấy promotion có phân trang với bộ lọc
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllWithFilters(array $filters = [], int $perPage = 12): LengthAwarePaginator;

    /**
     * Lấy danh sách khuyến mãi có thể áp dụng cho loại phòng
     *
     * @param int $roomTypeId
     * @param float $price
     * @return Collection
     */
    public function getAvailablePromotionsForRoomType(int $roomTypeId, float $price): Collection;
} 