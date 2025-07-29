<?php 

namespace App\Interfaces\Repositories\Admin;

use Illuminate\Database\Eloquent\Collection;

interface AdminRoomRepositoryInterface
{
    public function getAll($status = null);
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);

    public function getAllRoomsWithFilters($filters = []);
    
    /**
     * Lấy danh sách phòng theo tầng
     *
     * @param int $floor
     * @return Collection
     */
    public function getRoomsByFloor($floor);
    
    /**
     * Lấy danh sách tầng có sẵn
     *
     * @return Collection
     */
    public function getAvailableFloors();
    
    /**
     * Lấy tổng quan tất cả tầng
     *
     * @return array
     */
    public function getFloorOverview();
    
    /**
     * Tạo nhiều phòng cùng lúc
     *
     * @param array $data
     * @return int
     */
    public function bulkCreateRooms($data);
    
    /**
     * Lấy tất cả loại phòng
     *
     * @return Collection
     */
    public function getAllRoomTypes(): Collection;
}