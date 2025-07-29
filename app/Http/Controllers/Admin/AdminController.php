<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\AdminDashboardServiceInterface;

class AdminController extends Controller
{
    protected $dashboardService;

    /**
     * Khởi tạo controller
     */
    public function __construct(AdminDashboardServiceInterface $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Hiển thị trang dashboard
     */
    public function dashboard(Request $request)
    {
        // Lấy dữ liệu thống kê
        $stats = $this->dashboardService->getDashboardStatistics();

        // Lấy đặt phòng gần đây
        $recentData = $this->dashboardService->getRecentBookings();

        // Lấy thống kê theo trạng thái
        $statusData = $this->dashboardService->getBookingStatusStatistics();

        return view('admin.dashboard', [
            'todayBookings' => $stats['todayBookings'],
            'monthlyRevenue' => $stats['monthlyRevenue'],
            'bookingRate' => $stats['bookingRate'],
            'pendingBookings' => $stats['pendingBookings'],
            'recentBookings' => $recentData['recentBookings'],
            'statusCounts' => $statusData['statusCounts']
        ]);
    }
}
