<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\AdminPromotionRepositoryInterface;
use App\Models\Promotion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminPromotionRepository implements AdminPromotionRepositoryInterface
{
    protected $model;

    public function __construct(Promotion $model)
    {
        $this->model = $model;
    }

    /**
     * Lấy tất cả promotion với phân trang
     */
    public function getAllWithPagination(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Lấy promotion theo bộ lọc
     */
    public function getByFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->query();

        // Filter by status
        if (isset($filters['status'])) {
            switch ($filters['status']) {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'expired':
                    $query->where('expired_at', '<', now());
                    break;
                case 'valid':
                    $query->where('is_active', true)
                          ->where('expired_at', '>=', now());
                    break;
            }
        }

        // Filter by featured
        if (isset($filters['featured'])) {
            $query->where('is_featured', $filters['featured']);
        }

        // Filter by discount type
        if (isset($filters['discount_type']) && !empty($filters['discount_type'])) {
            $query->where('discount_type', $filters['discount_type']);
        }

        // Search by title, code, description
        if (isset($filters['search']) && !empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('code', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Tìm promotion theo ID
     */
    public function findById(int $id): ?Promotion
    {
        return $this->model->find($id);
    }

    /**
     * Tạo promotion mới
     */
    public function create(array $data): Promotion
    {
        return $this->model->create($data);
    }

    /**
     * Cập nhật promotion
     */
    public function update(int $id, array $data): bool
    {
        $promotion = $this->findById($id);
        if (!$promotion) {
            return false;
        }

        return $promotion->update($data);
    }

    /**
     * Xóa promotion
     */
    public function delete(int $id): bool
    {
        $promotion = $this->findById($id);
        if (!$promotion) {
            return false;
        }

        return $promotion->delete();
    }

    /**
     * Kiểm tra mã promotion có tồn tại
     */
    public function codeExists(string $code, ?int $excludeId = null): bool
    {
        $query = $this->model->where('code', $code);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Lấy thống kê promotion
     */
    public function getStats(): array
    {
        $total = $this->model->count();
        $active = $this->model->where('is_active', true)->count();
        $expired = $this->model->where('expired_at', '<', now())->count();
        $featured = $this->model->where('is_featured', true)->count();
        
        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $total - $active,
            'expired' => $expired,
            'featured' => $featured,
            'used_today' => $this->model->whereDate('created_at', today())->sum('used_count')
        ];
    }

    /**
     * Toggle trạng thái active
     */
    public function toggleActive(int $id): bool
    {
        $promotion = $this->findById($id);
        if (!$promotion) {
            return false;
        }

        $promotion->is_active = !$promotion->is_active;
        return $promotion->save();
    }

    /**
     * Toggle trạng thái featured
     */
    public function toggleFeatured(int $id): bool
    {
        $promotion = $this->findById($id);
        if (!$promotion) {
            return false;
        }

        $promotion->is_featured = !$promotion->is_featured;
        return $promotion->save();
    }
} 