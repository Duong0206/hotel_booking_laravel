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
     * @return mixed
     */
    public function findById(int $id)
    {
        return $this->model->find($id);
    }
}
