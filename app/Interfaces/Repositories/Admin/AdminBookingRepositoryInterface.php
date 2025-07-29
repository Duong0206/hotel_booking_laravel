<?php

namespace App\Interfaces\Repositories\Admin;

use App\Models\Booking;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface AdminBookingRepositoryInterface
{
    /**
     * Lấy tất cả đặt phòng có phân trang
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllWithPagination(array $filters = [], int $perPage = 10): LengthAwarePaginator;
    
    /**
     * Lấy đặt phòng theo ID
     *
     * @param int $id
     * @return Booking|null
     */
    public function findById(int $id): ?Booking;
    
    /**
     * Tạo đặt phòng mới
     *
     * @param array $data
     * @return Booking
     */
    public function create(array $data): Booking;
    
    /**
     * Cập nhật đặt phòng
     *
     * @param Booking $booking
     * @param array $data
     * @return bool
     */
    public function update(Booking $booking, array $data): bool;
    
    /**
     * Xóa đặt phòng
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
    
    /**
     * Cập nhật trạng thái đặt phòng
     *
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateStatus(int $id, string $status): bool;
    
    /**
     * Lấy đặt phòng gần đây
     *
     * @param int $limit
     * @return Collection
     */
    public function getRecent(int $limit = 5): Collection;
    
    /**
     * Đếm số đặt phòng theo trạng thái
     *
     * @param string $status
     * @return int
     */
    public function countByStatus(string $status): int;
    
    /**
     * Đếm số đặt phòng hôm nay
     *
     * @return int
     */
    public function countToday(): int;
    
    /**
     * Tính tổng doanh thu trong tháng
     *
     * @return float
     */
    public function calculateMonthlyRevenue(): float;
    
    /**
     * Lấy đặt phòng theo khoảng thời gian và trạng thái
     *
     * @param array $filters
     * @return Collection
     */
    public function getBookingsForReport(array $filters = []): Collection;

    public function findByBookingCode(string $code): ?\App\Models\Booking;
} 