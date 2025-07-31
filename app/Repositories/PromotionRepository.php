<?php

namespace App\Repositories;

use App\Interfaces\Repositories\PromotionRepositoryInterface;
use App\Models\Promotion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PromotionRepository implements PromotionRepositoryInterface
{
    protected $model;

    public function __construct(Promotion $model)
    {
        $this->model = $model;
    }

    /**
     * Lấy tất cả promotion
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->model->orderBy('created_at', 'desc')->get();
    }

    /**
     * Lấy promotion có phân trang
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllWithPagination(int $perPage = 12): LengthAwarePaginator
    {
        return $this->model->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Lấy promotion đang hoạt động
     *
     * @return Collection
     */
    public function getActive(): Collection
    {
        return $this->model->active()->available()->orderBy('created_at', 'desc')->get();
    }

    /**
     * Lấy promotion nổi bật
     *
     * @param int $limit
     * @return Collection
     */
    public function getFeatured(int $limit = 3): Collection
    {
        return $this->model->active()
                          ->featured()
                          ->available()
                          ->orderBy('created_at', 'desc')
                          ->limit($limit)
                          ->get();
    }

    /**
     * Tìm promotion theo ID
     *
     * @param int $id
     * @return Promotion|null
     */
    public function findById(int $id): ?Promotion
    {
        return $this->model->find($id);
    }

    /**
     * Tìm promotion theo mã code
     *
     * @param string $code
     * @return Promotion|null
     */
    public function findByCode(string $code): ?Promotion
    {
        return $this->model->where('code', $code)->first();
    }

    /**
     * Tạo promotion mới
     *
     * @param array $data
     * @return Promotion
     */
    public function create(array $data): Promotion
    {
        return $this->model->create($data);
    }

    /**
     * Cập nhật promotion
     *
     * @param Promotion $promotion
     * @param array $data
     * @return bool
     */
    public function update(Promotion $promotion, array $data): bool
    {
        return $promotion->update($data);
    }

    /**
     * Xóa promotion
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $promotion = $this->findById($id);
        if ($promotion) {
            return $promotion->delete();
        }
        return false;
    }

    /**
     * Tăng số lần sử dụng promotion
     *
     * @param int $id
     * @return bool
     */
    public function incrementUsedCount(int $id): bool
    {
        $promotion = $this->findById($id);
        if ($promotion) {
            $promotion->increment('used_count');
            return true;
        }
        return false;
    }

    /**
     * Lấy promotion theo bộ lọc
     *
     * @param array $filters
     * @return Collection
     */
    public function getByFilters(array $filters): Collection
    {
        $query = $this->model->query();

        if (isset($filters['active']) && $filters['active']) {
            $query->active();
        }

        if (isset($filters['featured']) && $filters['featured']) {
            $query->featured();
        }

        if (isset($filters['available']) && $filters['available']) {
            $query->available();
        }

        if (isset($filters['discount_type']) && !empty($filters['discount_type'])) {
            $query->where('discount_type', $filters['discount_type']);
        }

        // Thêm tìm kiếm theo text
        if (isset($filters['search']) && !empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('code', 'LIKE', "%{$searchTerm}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Lấy promotion có phân trang với bộ lọc
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllWithFilters(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = $this->model->query();

        if (isset($filters['active']) && $filters['active']) {
            $query->active();
        }

        if (isset($filters['featured']) && $filters['featured']) {
            $query->featured();
        }

        if (isset($filters['available']) && $filters['available']) {
            $query->available();
        }

        if (isset($filters['discount_type']) && !empty($filters['discount_type'])) {
            $query->where('discount_type', $filters['discount_type']);
        }

        // Thêm tìm kiếm theo text
        if (isset($filters['search']) && !empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('code', 'LIKE', "%{$searchTerm}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Lấy danh sách khuyến mãi có thể áp dụng cho loại phòng
     *
     * @param int $roomTypeId
     * @param float $price
     * @return Collection
     */
    public function getAvailablePromotionsForRoomType(int $roomTypeId, float $price): Collection
    {
        return $this->model->query()
            ->active()
            ->available()
            ->where(function($query) use ($roomTypeId) {
                $query->whereHas('roomTypes', function($q) use ($roomTypeId) {
                    $q->where('room_type_id', $roomTypeId);
                })->orWhere('apply_scope', 'all');
            })
            ->where('minimum_amount', '<=', $price)
            ->orderBy('created_at', 'desc')
            ->get();
    }
} 