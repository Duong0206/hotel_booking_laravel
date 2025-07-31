<?php

namespace App\Repositories;

use App\Models\RoomType;
use App\Interfaces\Repositories\RoomTypeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

class RoomTypeRepository implements RoomTypeRepositoryInterface
{
    protected RoomType $model;
    
    public function __construct(RoomType $model)
    {
        $this->model = $model;
    }

    /**
     * Tạo một query mới cho RoomType
     *
     * @return Builder
     */
    public function newQuery(): Builder
    {
        return $this->model->newQuery(); // hoặc RoomType::query()
    }

    /**
     * Lấy tất cả loại phòng
     *
     * @return Collection
     */
    public function getAllRoomTypes(): Collection
    {
        return $this->model->all();
    }

    /**
     * Lấy tất cả loại phòng với relationship services
     *
     * @return Collection
     */
    public function getAllRoomTypesWithServices(): Collection
    {
        return $this->model->with('services')->get();
    }

    /**
     * Tìm loại phòng theo ID
     *
     * @param int $id
     * @return RoomType|null
     */
    public function findById(int $id): ?RoomType
    {
        return $this->model->find($id);
    }

    /**
     * Tìm kiếm loại phòng theo các tiêu chí
     *
     * @param array $filters
     * @return Collection
     */
    public function searchRoomTypes(array $filters): Collection
    {
        $query = $this->newQuery();

        // Gộp tìm theo keyword hoặc loại phòng
        if (!empty($filters['keyword']) || !empty($filters['type'])) {
            $query->where(function ($q) use ($filters) {
                if (!empty($filters['keyword'])) {
                    $q->orWhere('name', 'like', '%' . $filters['keyword'] . '%');
                }

                if (!empty($filters['type'])) {
                    $q->orWhere('id', $filters['type']);
                }
            });
        }

        // Lọc theo khoảng giá (giữ nguyên là AND với các điều kiện trên)
        if (!empty($filters['price_min'])) {
            $query->where('price', '>=', (int)$filters['price_min']);
        }

        if (!empty($filters['price_max'])) {
            $query->where('price', '<=', (int)$filters['price_max']);
        }

        return $query->orderBy('price')->get();
    }

    /**
     * Lấy tất cả loại phòng với khuyến mại đang áp dụng
     *
     * @return Collection
     */
    public function getAllRoomTypesWithPromotions(): Collection
    {
        return $this->model->with(['promotions' => function($query) {
            $query->where('is_active', true)
                  ->where('valid_from', '<=', now())
                  ->where('expired_at', '>', now());
        }])->get();
    }
}
