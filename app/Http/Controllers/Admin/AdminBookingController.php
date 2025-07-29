<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Admin\AdminBookingServiceInterface;
use App\Interfaces\Services\Admin\AdminBookingServiceServiceInterface;
use App\Services\NotificationDataFormatterService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AdminBookingController extends Controller
{
    protected $bookingService;
    protected $dataFormatterService;
    protected $bookingServiceService;

    /**
     * Khởi tạo controller
     */
    public function __construct(
        AdminBookingServiceInterface $bookingService,
        NotificationDataFormatterService $dataFormatterService,
        AdminBookingServiceServiceInterface $bookingServiceService
    ) {
        $this->bookingService = $bookingService;
        $this->dataFormatterService = $dataFormatterService;
        $this->bookingServiceService = $bookingServiceService;
    }

    // ==================== BOOKING METHODS ====================

    /**
     * Hiển thị danh sách đặt phòng
     */
    public function index(Request $request)
    {
        $status = $request->get('status');
        $bookings = $this->bookingService->getBookingsWithPagination($request);
        
        return view('admin.bookings.index', compact('bookings', 'status'));
    }

    /**
     * Hiển thị chi tiết đặt phòng
     */
    public function show($id)
    {
        $booking = $this->bookingService->getBookingDetails($id);
        if (is_numeric($id)) {
            $validNextStatuses = $this->bookingService->getValidNextStatuses((int)$id);
        } else {
            $validNextStatuses = $this->bookingService->getValidNextStatusesByCode($id);
        }

        // Lấy dữ liệu services cho phần quản lý dịch vụ
        $bookingServices = $this->bookingServiceService->getBookingServices($booking->id);
        $availableRoomTypeServices = $this->bookingServiceService->getAvailableRoomTypeServices($booking->id);
        $availableServices = $this->bookingServiceService->getAvailableServices($booking->id);

        return view('admin.bookings.show', compact('booking', 'validNextStatuses', 'bookingServices', 'availableRoomTypeServices', 'availableServices'));
    }

    /**
     * Hiển thị form tạo đặt phòng mới
     */
    public function create()
    {
        $formData = $this->bookingService->getCreateFormData();
        
        return view('admin.bookings.create', [
            'rooms' => $formData['rooms'],
            'users' => $formData['users']
        ]);
    }

    /**
     * Lưu đặt phòng mới
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'status' => 'required|in:pending,confirmed',
        ]);
        
        $this->bookingService->createBooking($validatedData);
        
        return redirect()->route('admin.bookings.index')
            ->with('success', 'Đã tạo đặt phòng thành công.');
    }

    /**
     * Hiển thị form chỉnh sửa đặt phòng
     */
    public function edit($id)
    {
        $formData = $this->bookingService->getEditFormData($id);
        $validNextStatuses = $this->bookingService->getValidNextStatuses($id);
        
        return view('admin.bookings.edit', [
            'booking' => $formData['booking'],
            'rooms' => $formData['rooms'],
            'users' => $formData['users'],
            'validNextStatuses' => $validNextStatuses
        ]);
    }

    /**
     * Cập nhật thông tin đặt phòng
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,completed,cancelled,no_show',
        ]);
        
        $booking = $this->bookingService->updateBooking($id, $validatedData);
        
        return redirect()->route('admin.bookings.show', $booking->id)
            ->with('success', 'Đã cập nhật đặt phòng thành công.');
    }

    /**
     * Cập nhật trạng thái đặt phòng
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,completed,cancelled,no_show'
        ]);
        
        try {
            $this->bookingService->updateBookingStatus($id, $request->status);
            
            // Lấy thông tin booking để hiển thị trong thông báo
            $booking = $this->bookingService->getBookingDetails($id);
            $statusText = match($request->status) {
                'pending' => 'Chờ xác nhận',
                'confirmed' => 'Đã xác nhận',
                'checked_in' => 'Đã nhận phòng',
                'checked_out' => 'Đã trả phòng',
                'completed' => 'Hoàn thành',
                'cancelled' => 'Đã hủy',
                'no_show' => 'Khách không đến',
                default => 'Không xác định'
            };
            
            return redirect()->back()->with('success', "Đã cập nhật trạng thái đặt phòng #{$booking->booking_id} thành '{$statusText}'. Ghi chú và thông báo đã được tạo tự động.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Xóa đặt phòng
     */
    public function destroy($id)
    {
        // Xử lý xóa notification
        $notification = \App\Models\AdminNotification::findOrFail($id);
        $notification->delete();
        return redirect()->route('admin.notifications.index')
            ->with('success', 'Đã xóa thông báo thành công.');
    }
    
    /**
     * Hiển thị báo cáo đặt phòng
     */
    public function report(Request $request)
    {
        try {
            $reportData = $this->bookingService->getReportData($request);
            
            return view('admin.bookings.report', [
                'bookings' => $reportData['bookings'],
                'fromDate' => $reportData['filters']['from_date'] ?? null,
                'toDate' => $reportData['filters']['to_date'] ?? null,
                'status' => $reportData['filters']['status'] ?? null,
                'totalRevenue' => $reportData['totalRevenue'],
                'statusStats' => $reportData['statusStats']
            ]);
        } catch (\Exception $e) {
            return redirect()->route('admin.bookings.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Trả về partial chi tiết booking cho AJAX/modal (admin)
     */
    public function detailPartial($id)
    {
        $booking = $this->bookingService->getBookingDetails($id);
        return view('admin.bookings.detail', compact('booking'));
    }

    // ==================== NOTIFICATION METHODS ====================

    /**
     * Hiển thị trang quản lý thông báo
     */
    public function notificationsIndex(Request $request)
    {
        $type = $request->get('type', null);
        $priority = $request->get('priority', null);
        $isRead = $request->get('is_read', null);
        $search = $request->get('search', null);

        $query = \App\Models\AdminNotification::query();

        // Tìm kiếm theo từ khóa
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('priority', 'like', "%{$search}%")
                  ->orWhere('id', $search)
                  ->orWhere('is_read', $search)
                  ->orWhereDate('created_at', $search)
                  ->orWhereDate('updated_at', $search);
            });
        }

        // Lọc theo loại
        if ($type) {
            if ($type === 'unread') {
                $query->unread();
            } else {
                $query->ofType($type);
            }
        }

        // Lọc theo độ ưu tiên
        if ($priority) {
            $query->ofPriority($priority);
        }

        // Lọc theo trạng thái đọc
        if ($isRead !== null && $isRead !== '') {
            if ($isRead == '1') {
                $query->read();
            } else if ($isRead == '0') {
                $query->unread();
            }
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.notifications.index', compact('notifications', 'type', 'priority', 'isRead', 'search'));
    }

    /**
     * API: Lấy danh sách thông báo chưa đọc (cho AJAX)
     */
    public function getUnreadNotifications(): JsonResponse
    {
        try {
            $notifications = $this->bookingService->getUnreadNotifications(10);
            $count = $this->bookingService->getUnreadCount();

            return response()->json([
                'success' => true,
                'notifications' => $notifications,
                'count' => $count,
                'by_priority' => $this->bookingService->getUnreadCountByPriority()
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error getting unread notifications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tải thông báo',
                'notifications' => [],
                'count' => 0,
                'by_priority' => []
            ], 500);
        }
    }

    /**
     * API: Lấy số lượng thông báo chưa đọc (cho badge)
     */
    public function getUnreadCount(): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'count' => $this->bookingService->getUnreadCount(),
                'by_priority' => $this->bookingService->getUnreadCountByPriority()
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error getting unread count: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tải số lượng thông báo',
                'count' => 0,
                'by_priority' => []
            ], 500);
        }
    }

    /**
     * API: Đánh dấu thông báo đã đọc
     */
    public function markAsRead(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'notification_id' => 'required|integer|exists:admin_notifications,id'
            ]);

            $success = $this->bookingService->markAsRead($request->notification_id);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã đánh dấu thông báo đã đọc',
                    'count' => $this->bookingService->getUnreadCount()
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông báo'
            ], 404);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error marking notification as read: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi đánh dấu thông báo'
            ], 500);
        }
    }

    /**
     * API: Đánh dấu tất cả thông báo đã đọc
     */
    public function markAllAsRead(): JsonResponse
    {
        try {
            $count = $this->bookingService->markAllAsRead();

            return response()->json([
                'success' => true,
                'message' => "Đã đánh dấu {$count} thông báo đã đọc",
                'count' => 0
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error marking all notifications as read: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi đánh dấu tất cả thông báo'
            ], 500);
        }
    }

    /**
     * API: Xóa thông báo
     */
    public function deleteNotification(Request $request): JsonResponse
    {
        $request->validate([
            'notification_id' => 'required|integer|exists:admin_notifications,id'
        ]);

        $notification = \App\Models\AdminNotification::find($request->notification_id);
        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa thông báo',
            'count' => $this->bookingService->getUnreadCount()
        ]);
    }

    /**
     * API: Xóa tất cả thông báo đã đọc
     */
    public function deleteReadNotifications(): JsonResponse
    {
        $count = \App\Models\AdminNotification::read()->delete();

        return response()->json([
            'success' => true,
            'message' => "Đã xóa {$count} thông báo đã đọc"
        ]);
    }

    /**
     * API: Xóa thông báo cũ (quá 30 ngày)
     */
    public function deleteOldNotifications(): JsonResponse
    {
        $count = $this->bookingService->deleteOldNotifications();

        return response()->json([
            'success' => true,
            'message' => "Đã xóa {$count} thông báo cũ"
        ]);
    }

    /**
     * Hiển thị chi tiết thông báo
     */
    public function notificationShow($id)
    {
        $notification = \App\Models\AdminNotification::findOrFail($id);
        
        // Đánh dấu đã đọc khi xem chi tiết
        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        // Format dữ liệu bổ sung
        $formattedData = $this->dataFormatterService->formatData($notification->data);

        return view('admin.notifications.show', compact('notification', 'formattedData'));
    }

    /**
     * Tạo thông báo test (cho mục đích demo)
     */
    public function createTestNotification(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|string',
            'title' => 'required|string',
            'message' => 'required|string',
            'priority' => 'nullable|string|in:low,normal,high,urgent'
        ]);

        $notification = \App\Models\AdminNotification::createNotification(
            $request->type,
            $request->title,
            $request->message,
            $request->data ?? [],
            $request->priority ?? 'normal'
        );

        return response()->json([
            'success' => true,
            'message' => 'Đã tạo thông báo test',
            'notification' => $notification
        ]);
    }

    /**
     * Tạo thông báo test cho ghi chú
     */
    public function createTestNoteNotification(Request $request): JsonResponse
    {
        $notification = \App\Models\AdminNotification::createNotification(
            'booking_note_created',
            'Ghi chú mới (Test)',
            'Ghi chú mới từ Khách hàng cho đặt phòng #TEST001',
            [
                'note_id' => 1,
                'booking_id' => 1,
                'user_id' => 1,
                'type' => 'customer',
                'visibility' => 'public',
                'is_internal' => false,
                'booking_code' => 'TEST001'
            ],
            'normal',
            'fas fa-sticky-note',
            'info'
        );

        return response()->json([
            'success' => true,
            'message' => 'Đã tạo thông báo test cho ghi chú',
            'notification' => $notification
        ]);
    }

    /**
     * Tạo thông báo test cho đánh giá
     */
    public function createTestReviewNotification(Request $request): JsonResponse
    {
        $notification = \App\Models\AdminNotification::createNotification(
            'room_type_review_created',
            'Đánh giá phòng mới (Test)',
            'Đánh giá 5/5 sao cho Phòng Deluxe từ Nguyễn Văn A',
            [
                'review_id' => 1,
                'user_id' => 1,
                'room_type_id' => 1,
                'rating' => 5,
                'room_type_name' => 'Phòng Deluxe'
            ],
            'normal',
            'fas fa-star',
            'info'
        );

        return response()->json([
            'success' => true,
            'message' => 'Đã tạo thông báo test cho đánh giá',
            'notification' => $notification
        ]);
    }

    /**
     * API: Lấy danh sách thông báo với filter (cho AJAX)
     */
    public function getNotifications(Request $request): JsonResponse
    {
        $type = $request->get('type');
        $priority = $request->get('priority');
        $isRead = $request->get('is_read');
        $search = $request->get('search');
        $page = $request->get('page', 1);

        $query = \App\Models\AdminNotification::query();

        // Tìm kiếm theo từ khóa
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        if ($type && $type !== 'all') {
            if ($type === 'unread') {
                $query->unread();
            } else {
                $query->ofType($type);
            }
        }

        if ($priority && $priority !== 'all') {
            $query->ofPriority($priority);
        }

        if ($isRead !== null && $isRead !== '') {
            if ($isRead == '1') {
                $query->read();
            } else {
                $query->unread();
            }
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(20, ['*'], 'page', $page);

        // Format notifications for response
        $formattedNotifications = $notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'title' => $notification->title,
                'message' => $notification->message,
                'type' => $notification->type,
                'priority' => $notification->priority,
                'is_read' => $notification->is_read,
                'time_ago' => $notification->time_ago,
                'color' => $notification->color,
                'display_icon' => $notification->display_icon,
                'badge_color' => $notification->badge_color,
                'show_url' => route('admin.notifications.show', $notification->id),
                'mark_read_url' => route('admin.notifications.mark-read', $notification->id),
                'delete_url' => route('admin.notifications.delete', $notification->id),
            ];
        });

        return response()->json([
            'success' => true,
            'notifications' => $formattedNotifications,
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
                'from' => $notifications->firstItem(),
                'to' => $notifications->lastItem(),
            ],
            'filters' => [
                'type' => $type,
                'priority' => $priority,
                'is_read' => $isRead,
                'search' => $search,
            ]
        ]);
    }

    /**
     * API: Xóa hàng loạt thông báo (chuẩn Laravel, redirect về lại trang)
     */
    public function deleteNotifications(Request $request)
    {
        $ids = (array) $request->input('notification_id');
        $count = $this->bookingService->deleteNotifications($ids);
        return redirect()->route('admin.notifications.index')
            ->with('success', "Đã xóa $count thông báo");
    }

    /**
     * API: Đánh dấu đã đọc hàng loạt thông báo
     */
    public function markNotificationsAsRead(Request $request): \Illuminate\Http\JsonResponse
    {
        $ids = (array) $request->input('notification_id');
        $count = $this->bookingService->markNotificationsAsRead($ids);
        return response()->json([
            'success' => $count > 0,
            'message' => "Đã đánh dấu $count thông báo đã đọc",
            'count' => $this->bookingService->getUnreadCount()
        ]);
    }
    /**
     * Thêm dịch vụ vào booking
     */
    public function addServiceToBooking(Request $request, $id)
    {
        try {
            $booking = $this->bookingService->getBookingDetails($id);
            if (!$booking) {
                return redirect()->back()->with('error', 'Booking không tồn tại');
            }

            // Validate request
            $request->validate([
                'service_name' => 'required|string|max:255',
                'service_price' => 'required|numeric|min:0',
                'quantity' => 'required|integer|min:1',
                'notes' => 'nullable|string|max:500'
            ]);

            // Add custom service to booking
            $bookingService = $this->bookingServiceService->addCustomServiceToBooking(
                $booking->id,
                $request->service_name,
                $request->service_price,
                $request->quantity,
                $request->notes
            );

            return redirect()->back()->with('success', 'Đã thêm dịch vụ "' . $request->service_name . '" thành công!');

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error adding service to booking: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi thêm dịch vụ. Vui lòng thử lại.');
        }
    }

    /**
     * Xóa dịch vụ khỏi booking
     */
    public function destroyServiceFromBooking($bookingId, $bookingServiceId)
    {
        try {
            $this->bookingServiceService->destroyServiceFromBooking($bookingServiceId);
            return redirect()->back()->with('success', 'Đã xóa dịch vụ thành công!');
        } catch (\Exception $e) {
            Log::error('Error removing service from booking: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa dịch vụ.');
        }
    }
}