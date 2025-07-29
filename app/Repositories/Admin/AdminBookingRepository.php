<?php

namespace App\Repositories\Admin;

use App\Models\Booking;
use App\Interfaces\Repositories\Admin\AdminBookingRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminBookingRepository implements AdminBookingRepositoryInterface
{
    protected $bookingModel;

    public function __construct(Booking $bookingModel)
    {
        $this->bookingModel = $bookingModel;
    }

    // ==================== BOOKING METHODS ====================

    /**
     * Lấy tất cả đặt phòng có phân trang
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllWithPagination(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->bookingModel->with(['user', 'room']);
        
        // Áp dụng bộ lọc
        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }
        
        return $query->latest()->paginate($perPage);
    }
    
    /**
     * Lấy đặt phòng theo booking_id (mã code)
     *
     * @param string $id
     * @return Booking|null
     */
    public function findById($id): ?Booking
    {
        // Nếu là số, thử tìm theo id số trước
        if (is_numeric($id)) {
            $booking = $this->bookingModel->with(['user', 'room'])->find($id);
            if ($booking) return $booking;
        }
        // Nếu không thấy hoặc là chuỗi, tìm theo booking_id (mã code)
        return $this->bookingModel->with(['user', 'room'])->where('booking_id', $id)->first();
    }
    
    /**
     * Tạo đặt phòng mới
     *
     * @param array $data
     * @return Booking
     */
    public function create(array $data): Booking
    {
        return $this->bookingModel->create($data);
    }
    
    /**
     * Cập nhật đặt phòng
     *
     * @param Booking $booking
     * @param array $data
     * @return bool
     */
    public function update(Booking $booking, array $data): bool
    {
        return $booking->update($data);
    }
    
    /**
     * Xóa đặt phòng
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->bookingModel->destroy($id) > 0;
    }
    
    /**
     * Cập nhật trạng thái đặt phòng
     *
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateStatus(int $id, string $status): bool
    {
        $booking = $this->bookingModel->find($id);
        if ($booking) {
            $booking->status = $status;
            return $booking->save();
        }
        return false;
    }
    
    /**
     * Lấy đặt phòng gần đây
     *
     * @param int $limit
     * @return Collection
     */
    public function getRecent(int $limit = 5): Collection
    {
        return $this->bookingModel->with(['user', 'room'])
                           ->latest()
                           ->limit($limit)
                           ->get();
    }
    
    /**
     * Đếm số đặt phòng theo trạng thái
     *
     * @param string $status
     * @return int
     */
    public function countByStatus(string $status): int
    {
        return $this->bookingModel->where('status', $status)->count();
    }
    
    /**
     * Đếm số đặt phòng hôm nay
     *
     * @return int
     */
    public function countToday(): int
    {
        return $this->bookingModel->whereDate('created_at', Carbon::today())->count();
    }
    
    /**
     * Tính tổng doanh thu trong tháng
     *
     * @return float
     */
    public function calculateMonthlyRevenue(): float
    {
        return $this->bookingModel->whereMonth('created_at', Carbon::now()->month)
                           ->whereYear('created_at', Carbon::now()->year)
                           ->where('status', '!=', 'cancelled')
                           ->sum('price');
    }
    
    /**
     * Lấy đặt phòng theo khoảng thời gian và trạng thái
     *
     * @param array $filters
     * @return Collection
     */
    public function getBookingsForReport(array $filters = []): Collection
    {
        $query = $this->bookingModel->with(['user', 'room']);
        
        // Lọc theo ngày
        if (isset($filters['from_date']) && $filters['from_date']) {
            $query->where('check_in_date', '>=', $filters['from_date']);
        }
        
        if (isset($filters['to_date']) && $filters['to_date']) {
            $query->where('check_out_date', '<=', $filters['to_date']);
        }
        
        // Lọc theo trạng thái
        if (isset($filters['status']) && $filters['status'] && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }
        
        return $query->get();
    }

    public function findByBookingCode(string $code): ?Booking
    {
        return $this->bookingModel->with(['user', 'room'])->where('booking_id', $code)->first();
    }
} 