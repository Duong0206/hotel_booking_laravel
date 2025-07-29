<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\AdminBookingRepositoryInterface;
use App\Interfaces\Repositories\RoomRepositoryInterface;
use App\Interfaces\Services\Admin\AdminDashboardServiceInterface;
use App\Models\Room;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardService implements AdminDashboardServiceInterface
{
    protected $adminBookingRepository;
    protected $roomRepository;

    public function __construct(
        AdminBookingRepositoryInterface $adminBookingRepository,
        RoomRepositoryInterface $roomRepository
    ) {
        $this->adminBookingRepository = $adminBookingRepository;
        $this->roomRepository = $roomRepository;
    }

    /**
     * Lấy dữ liệu thống kê cho dashboard
     *
     * @return array
     */
    public function getDashboardStatistics(): array
    {
        // Đếm số đặt phòng hôm nay
        $todayBookings = $this->adminBookingRepository->countToday();
        
        // Tính doanh thu tháng hiện tại
        $monthlyRevenue = $this->adminBookingRepository->calculateMonthlyRevenue();
        
        // Tỷ lệ đặt phòng
        $bookingRate = $this->calculateBookingRate();
        
        // Đếm số đặt phòng đang chờ xác nhận
        $pendingBookings = $this->adminBookingRepository->countByStatus('pending');
        
        return [
            'todayBookings' => $todayBookings,
            'monthlyRevenue' => $monthlyRevenue,
            'bookingRate' => $bookingRate,
            'pendingBookings' => $pendingBookings
        ];
    }
    
    /**
     * Lấy danh sách đặt phòng gần đây
     *
     * @param int $limit
     * @return array
     */
    public function getRecentBookings(int $limit = 5): array
    {
        $recentBookings = $this->adminBookingRepository->getRecent($limit);
        
        return [
            'recentBookings' => $recentBookings
        ];
    }
    
    /**
     * Lấy thống kê theo trạng thái đặt phòng
     *
     * @return array
     */
    public function getBookingStatusStatistics(): array
    {
        $statusCounts = [
            'pending' => $this->adminBookingRepository->countByStatus('pending'),
            'confirmed' => $this->adminBookingRepository->countByStatus('confirmed'),
            'checked_in' => $this->adminBookingRepository->countByStatus('checked_in'),
            'checked_out' => $this->adminBookingRepository->countByStatus('checked_out'),
            'completed' => $this->adminBookingRepository->countByStatus('completed'),
            'cancelled' => $this->adminBookingRepository->countByStatus('cancelled'),
            'no_show' => $this->adminBookingRepository->countByStatus('no_show'),
        ];
        
        return [
            'statusCounts' => $statusCounts
        ];
    }
    
    /**
     * Tính tỷ lệ đặt phòng (phòng đã đặt / tổng số phòng)
     *
     * @return int
     */
    public function calculateBookingRate(): int
    {
        $totalRooms = $this->roomRepository->getAll()->count();
        
        // Đếm số phòng đang được đặt (check-in <= hôm nay và check-out >= hôm nay)
        $bookedRooms = Booking::where('status', '!=', 'cancelled')
                           ->whereDate('check_in_date', '<=', Carbon::today())
                           ->whereDate('check_out_date', '>=', Carbon::today())
                           ->count();
        
        return $totalRooms > 0 ? round(($bookedRooms / $totalRooms) * 100) : 0;
    }
} 