<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\AdminBookingRepositoryInterface;
use App\Interfaces\Repositories\RoomRepositoryInterface;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Interfaces\Services\Admin\AdminBookingServiceInterface;
use App\Models\Booking;
use App\Models\Room;
use App\Models\AdminNotification;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;
use App\Repositories\Admin\AdminBookingRepository;

class AdminBookingService implements AdminBookingServiceInterface
{
    protected $adminBookingRepository;
    protected $roomRepository;
    protected $userRepository;

    public function __construct(
        AdminBookingRepositoryInterface $adminBookingRepository,
        RoomRepositoryInterface $roomRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->adminBookingRepository = $adminBookingRepository;
        $this->roomRepository = $roomRepository;
        $this->userRepository = $userRepository;
    }

    // ==================== BOOKING METHODS ====================

    /**
     * Lấy danh sách đặt phòng có phân trang
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getBookingsWithPagination(Request $request): LengthAwarePaginator
    {
        $filters = [
            'status' => $request->get('status')
        ];
        
        return $this->adminBookingRepository->getAllWithPagination($filters);
    }
    
    /**
     * Lấy chi tiết đặt phòng
     *
     * @param string $id
     * @return Booking
     */
    public function getBookingDetails($id): Booking
    {
        $booking = $this->adminBookingRepository->findById($id);
        
        if (!$booking) {
            throw new \Exception('Không tìm thấy đặt phòng');
        }
        
        return $booking;
    }
    
    /**
     * Tạo đặt phòng mới
     *
     * @param array $data
     * @return Booking
     */
    public function createBooking(array $data): Booking
    {
        // Xác thực dữ liệu
        $validator = Validator::make($data, [
            'user_id' => 'required|exists:users,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'status' => 'required|in:pending,confirmed',
        ]);
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        // Tính tổng tiền
        $room = $this->roomRepository->findById($data['room_id']);
        $checkIn = new \DateTime($data['check_in_date']);
        $checkOut = new \DateTime($data['check_out_date']);
        $nights = $checkIn->diff($checkOut)->days;
        $totalPrice = $room->price * $nights;
        
        // Tạo booking ID duy nhất
        $bookingId = 'BK' . date('ymd') . strtoupper(Str::random(5));
        
        // Chuẩn bị dữ liệu
        $bookingData = [
            'user_id' => $data['user_id'],
            'room_id' => $data['room_id'],
            'booking_id' => $bookingId,
            'check_in_date' => $data['check_in_date'],
            'check_out_date' => $data['check_out_date'],
            'price' => $totalPrice,
            'status' => $data['status']
        ];
        
        // Tạo đặt phòng
        return $this->adminBookingRepository->create($bookingData);
    }
    
    /**
     * Cập nhật đặt phòng
     *
     * @param int $id
     * @param array $data
     * @return Booking
     */
    public function updateBooking(int $id, array $data): Booking
    {
        // Xác thực dữ liệu
        $validator = Validator::make($data, [
            'user_id' => 'required|exists:users,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,completed,cancelled,no_show',
        ]);
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        $booking = $this->adminBookingRepository->findById($id);
        
        if (!$booking) {
            throw new \Exception('Không tìm thấy đặt phòng');
        }
        
        // Tính lại tổng tiền nếu thay đổi phòng hoặc ngày
        if ($booking->room_id != $data['room_id'] || 
            $booking->check_in_date != $data['check_in_date'] || 
            $booking->check_out_date != $data['check_out_date']) {
            
            $room = $this->roomRepository->findById($data['room_id']);
            $checkIn = new \DateTime($data['check_in_date']);
            $checkOut = new \DateTime($data['check_out_date']);
            $nights = $checkIn->diff($checkOut)->days;
            $totalPrice = $room->price * $nights;
            
            $data['price'] = $totalPrice;
        }
        
        // Cập nhật đặt phòng
        $this->adminBookingRepository->update($booking, $data);
        
        return $this->adminBookingRepository->findById($id);
    }
    
    /**
     * Xóa đặt phòng
     *
     * @param int $id
     * @return bool
     */
    public function deleteBooking(int $id): bool
    {
        return $this->adminBookingRepository->delete($id);
    }
    
    /**
     * Cập nhật trạng thái đặt phòng
     *
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateBookingStatus(int $id, string $status): bool
    {
        // Danh sách trạng thái theo thứ tự một chiều
        $statusOrder = [
            'pending',
            'confirmed',
            'checked_in',
            'checked_out',
            'completed',
            'cancelled',
            'no_show',
        ];

        $booking = $this->adminBookingRepository->findById($id);
        if (!$booking) {
            throw new \Exception('Không tìm thấy đặt phòng');
        }

        $oldStatus = $booking->status;
        $currentIndex = array_search($oldStatus, $statusOrder);
        $newIndex = array_search($status, $statusOrder);

        // Kiểm tra trạng thái hợp lệ
        if ($newIndex === false || $currentIndex === false) {
            throw new \Exception('Trạng thái không hợp lệ.');
        }

        // Cho phép chuyển sang cancelled/no_show ở bất kỳ trạng thái nào
        if (in_array($status, ['cancelled', 'no_show'])) {
            $success = $this->adminBookingRepository->updateStatus($id, $status);
            if ($success) {
                $this->handleStatusChange($booking, $oldStatus, $status);
            }
            return $success;
        }

        // Chỉ cho phép chuyển tiếp (không lùi lại)
        if ($newIndex <= $currentIndex) {
            throw new \Exception('Không được chuyển trạng thái lùi lại hoặc lặp lại.');
        }

        // Kiểm tra xem trạng thái mới có trong danh sách hợp lệ không
        $validNextStatuses = $this->getValidNextStatuses($id);
        if (!array_key_exists($status, $validNextStatuses)) {
            throw new \Exception('Trạng thái không được phép chuyển đổi.');
        }

        $success = $this->adminBookingRepository->updateStatus($id, $status);
        if ($success) {
            $this->handleStatusChange($booking, $oldStatus, $status);
        }
        return $success;
    }
    
    /**
     * Lấy dữ liệu cho dashboard
     *
     * @return array
     */
    public function getDashboardData(): array
    {
        // Đếm số đặt phòng hôm nay
        $todayBookings = $this->adminBookingRepository->countToday();
        
        // Tính doanh thu tháng hiện tại
        $monthlyRevenue = $this->adminBookingRepository->calculateMonthlyRevenue();
        
        // Đếm số đặt phòng đang chờ xác nhận
        $pendingBookings = $this->adminBookingRepository->countByStatus('pending');
        
        // Lấy danh sách đặt phòng gần đây
        $recentBookings = $this->adminBookingRepository->getRecent(5);
        
        // Thống kê theo trạng thái
        $statusCounts = [
            'pending' => $this->adminBookingRepository->countByStatus('pending'),
            'confirmed' => $this->adminBookingRepository->countByStatus('confirmed'),
            'cancelled' => $this->adminBookingRepository->countByStatus('cancelled'),
            'completed' => $this->adminBookingRepository->countByStatus('completed'),
        ];
        
        return [
            'todayBookings' => $todayBookings,
            'monthlyRevenue' => $monthlyRevenue,
            'pendingBookings' => $pendingBookings,
            'recentBookings' => $recentBookings,
            'statusCounts' => $statusCounts
        ];
    }
    
    /**
     * Lấy dữ liệu cho báo cáo
     *
     * @param Request $request
     * @return array
     */
    public function getReportData(Request $request): array
    {
        // Kiểm tra quyền truy cập
        if (Auth::user()->role?->name == 'staff') {
            throw new \Exception('Bạn không có quyền truy cập trang báo cáo.');
        }
        
        $filters = [
            'from_date' => $request->get('from_date'),
            'to_date' => $request->get('to_date'),
            'status' => $request->get('status')
        ];
        
        $bookings = $this->adminBookingRepository->getBookingsForReport($filters);
        
        // Tính toán tổng doanh thu
        $totalRevenue = $bookings->sum('price');
        
        // Thống kê theo trạng thái
        $statusStats = $bookings->groupBy('status')
            ->map(function ($items) {
                return [
                    'count' => $items->count(),
                    'revenue' => $items->sum('price')
                ];
            });
        
        return [
            'bookings' => $bookings,
            'totalRevenue' => $totalRevenue,
            'statusStats' => $statusStats,
            'filters' => $filters
        ];
    }
    
    /**
     * Lấy dữ liệu cho form tạo đặt phòng
     *
     * @return array
     */
    public function getCreateFormData(): array
    {
        $rooms = $this->roomRepository->getAll();
        $users = $this->userRepository->getAll();
        
        return [
            'rooms' => $rooms,
            'users' => $users
        ];
    }
    
    /**
     * Lấy dữ liệu cho form chỉnh sửa đặt phòng
     *
     * @param int $id
     * @return array
     */
    public function getEditFormData(int $id): array
    {
        $booking = $this->adminBookingRepository->findById($id);
        
        if (!$booking) {
            throw new \Exception('Không tìm thấy đặt phòng');
        }
        
        $rooms = $this->roomRepository->getAll();
        $users = $this->userRepository->getAll();
        
        return [
            'booking' => $booking,
            'rooms' => $rooms,
            'users' => $users
        ];
    }

    /**
     * Lấy danh sách trạng thái hợp lệ tiếp theo cho booking
     *
     * @param int $id
     * @return array
     */
    public function getValidNextStatuses(int $id): array
    {
        $booking = $this->adminBookingRepository->findById($id);
        if (!$booking) {
            return [];
        }

        $statusOrder = [
            'pending',
            'confirmed',
            'checked_in',
            'checked_out',
            'completed',
            'cancelled',
            'no_show',
        ];

        $currentIndex = array_search($booking->status, $statusOrder);
        if ($currentIndex === false) {
            return [];
        }

        $validStatuses = [];

        // Nếu trạng thái hiện tại KHÔNG phải là completed thì luôn cho phép chuyển sang cancelled và no_show
        if ($booking->status !== 'completed') {
            $validStatuses['cancelled'] = 'Đã hủy';
            $validStatuses['no_show'] = 'Khách không đến';
        }

        // Cho phép chuyển sang tất cả các trạng thái phía trước (sau trạng thái hiện tại)
        for ($i = $currentIndex + 1; $i < count($statusOrder); $i++) {
            $nextStatus = $statusOrder[$i];
            if (!in_array($nextStatus, ['cancelled', 'no_show'])) {
                $validStatuses[$nextStatus] = match($nextStatus) {
                    'pending' => 'Chờ xác nhận',
                    'confirmed' => 'Đã xác nhận',
                    'checked_in' => 'Đã nhận phòng',
                    'checked_out' => 'Đã trả phòng',
                    'completed' => 'Hoàn thành',
                    default => 'Không xác định'
                };
            }
        }

        return $validStatuses;
    }

    /**
     * Lấy danh sách trạng thái hợp lệ tiếp theo cho booking theo mã code
     *
     * @param string $bookingCode
     * @return array
     */
    public function getValidNextStatusesByCode(string $bookingCode): array
    {
        $booking = $this->adminBookingRepository->findByBookingCode($bookingCode);
        if (!$booking) {
            return [];
        }
        $statusOrder = [
            'pending',
            'confirmed',
            'checked_in',
            'checked_out',
            'cancelled',
        ];
        $currentStatus = $booking->status;
        $currentIndex = array_search($currentStatus, $statusOrder);
        if ($currentIndex === false) {
            return [];
        }
        return array_slice($statusOrder, $currentIndex + 1);
    }

    // ==================== NOTIFICATION METHODS ====================

    /**
     * Lấy số lượng thông báo chưa đọc
     */
    public function getUnreadCount(): int
    {
        return AdminNotification::unread()->count();
    }

    /**
     * Lấy số lượng thông báo chưa đọc theo mức độ ưu tiên
     */
    public function getUnreadCountByPriority(): array
    {
        return [
            'urgent' => AdminNotification::unread()->ofPriority('urgent')->count(),
            'high' => AdminNotification::unread()->ofPriority('high')->count(),
            'normal' => AdminNotification::unread()->ofPriority('normal')->count(),
            'low' => AdminNotification::unread()->ofPriority('low')->count(),
        ];
    }

    /**
     * Lấy danh sách thông báo chưa đọc
     */
    public function getUnreadNotifications(int $limit = 10): Collection
    {
        return AdminNotification::unread()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'priority' => $notification->priority,
                    'time_ago' => $notification->time_ago,
                    'color' => $notification->color,
                    'display_icon' => $notification->display_icon,
                    'badge_color' => $notification->badge_color,
                ];
            });
    }

    /**
     * Đánh dấu thông báo đã đọc
     */
    public function markAsRead(int $notificationId): bool
    {
        $notification = AdminNotification::find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            return true;
        }
        return false;
    }

    /**
     * Đánh dấu tất cả thông báo đã đọc
     */
    public function markAllAsRead(): int
    {
        return AdminNotification::unread()->update(['is_read' => true]);
    }

    /**
     * Xóa thông báo cũ (quá 30 ngày)
     */
    public function deleteOldNotifications(): int
    {
        $cutoffDate = Carbon::now()->subDays(30);
        return AdminNotification::where('created_at', '<', $cutoffDate)->delete();
    }

    /**
     * Xóa hàng loạt thông báo
     *
     * @param array $ids
     * @return int
     */
    public function deleteNotifications(array $ids): int
    {
        return \App\Models\AdminNotification::whereIn('id', $ids)->delete();
    }

    /**
     * Đánh dấu đã đọc hàng loạt thông báo
     *
     * @param array $ids
     * @return int
     */
    public function markNotificationsAsRead(array $ids): int
    {
        return \App\Models\AdminNotification::whereIn('id', $ids)->where('is_read', false)->update(['is_read' => true]);
    }

    /**
     * Tạo thông báo mới
     */
    public function createNotification(
        string $type,
        string $title,
        string $message,
        array $data = [],
        string $priority = 'normal',
        string $icon = null,
        string $color = null
    ): AdminNotification {
        return AdminNotification::create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'priority' => $priority,
            'icon' => $icon,
            'color' => $color,
            'is_read' => false,
        ]);
    }

    /**
     * Tạo thông báo cho ghi chú mới
     */
    public function createNoteNotification(array $noteData): AdminNotification
    {
        $booking = \App\Models\Booking::find($noteData['booking_id']);
        $user = \App\Models\User::find($noteData['user_id']);

        $title = 'Ghi chú mới';
        $message = "Ghi chú mới từ {$user->name} cho đặt phòng #{$booking->booking_id}";

        return $this->createNotification(
            'booking_note_created',
            $title,
            $message,
            $noteData,
            'normal',
            'fas fa-sticky-note',
            'info'
        );
    }

    /**
     * Tạo thông báo cho đánh giá mới
     */
    public function createReviewNotification(array $reviewData): AdminNotification
    {
        $user = \App\Models\User::find($reviewData['user_id']);
        $roomType = \App\Models\RoomType::find($reviewData['room_type_id']);

        $title = 'Đánh giá phòng mới';
        $message = "Đánh giá {$reviewData['rating']}/5 sao cho {$roomType->name} từ {$user->name}";

        return $this->createNotification(
            'room_type_review_created',
            $title,
            $message,
            $reviewData,
            'normal',
            'fas fa-star',
            'info'
        );
    }

    /**
     * Tạo thông báo cho đặt phòng mới
     */
    public function createBookingNotification(array $bookingData): AdminNotification
    {
        $user = \App\Models\User::find($bookingData['user_id']);
        $room = \App\Models\Room::find($bookingData['room_id']);

        $title = 'Đặt phòng mới';
        $message = "Đặt phòng mới từ {$user->name} cho {$room->roomType->name}";

        return $this->createNotification(
            'booking_created',
            $title,
            $message,
            $bookingData,
            'normal',
            'fas fa-calendar-check',
            'success'
        );
    }

    /**
     * Tạo thông báo cho thay đổi trạng thái đặt phòng
     */
    public function createStatusChangeNotification(array $bookingData, string $oldStatus, string $newStatus): AdminNotification
    {
        $user = \App\Models\User::find($bookingData['user_id']);
        $room = \App\Models\Room::find($bookingData['room_id']);

        $statusText = match($newStatus) {
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'checked_in' => 'Đã nhận phòng',
            'checked_out' => 'Đã trả phòng',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            'no_show' => 'Khách không đến',
            default => 'Không xác định'
        };

        $title = 'Thay đổi trạng thái đặt phòng';
        $message = "Đặt phòng #{$bookingData['booking_id']} đã chuyển sang trạng thái: {$statusText}";

        return $this->createNotification(
            'booking_status_changed',
            $title,
            $message,
            array_merge($bookingData, ['old_status' => $oldStatus, 'new_status' => $newStatus]),
            'normal',
            'fas fa-exchange-alt',
            'warning'
        );
    }

    // ==================== EVENT HANDLER METHODS ====================

    /**
     * Xử lý sự kiện booking được tạo
     */
    public function onBookingCreated(Booking $booking): void
    {
        // Tạo ghi chú hệ thống
        $this->createSystemNote($booking->id, 'Đặt phòng mới được tạo', 'system');
        
        // Tạo thông báo admin
        $this->createBookingNotification($booking->toArray());
    }

    /**
     * Xử lý sự kiện booking được cập nhật
     */
    public function onBookingUpdated(Booking $booking, array $changes): void
    {
        $changeText = [];
        foreach ($changes as $field => $value) {
            $changeText[] = ucfirst($field) . ': ' . $value;
        }
        
        $this->createSystemNote($booking->id, 'Cập nhật thông tin: ' . implode(', ', $changeText), 'system');
    }

    /**
     * Xử lý sự kiện booking bị hủy
     */
    public function onBookingCancelled(Booking $booking, string $reason = 'Đã hủy'): void
    {
        $this->createSystemNote($booking->id, $reason, 'system');
    }

    /**
     * Xử lý sự kiện booking được xác nhận
     */
    public function onBookingConfirmed(Booking $booking): void
    {
        $this->createSystemNote($booking->id, 'Đặt phòng đã được xác nhận', 'system');
    }

    /**
     * Xử lý sự kiện booking check-in
     */
    public function onBookingCheckedIn(Booking $booking): void
    {
        $this->createSystemNote($booking->id, 'Khách đã nhận phòng', 'system');
    }

    /**
     * Xử lý sự kiện booking check-out
     */
    public function onBookingCheckedOut(Booking $booking): void
    {
        $this->createSystemNote($booking->id, 'Khách đã trả phòng', 'system');
    }

    /**
     * Xử lý sự kiện booking hoàn thành
     */
    public function onBookingCompleted(Booking $booking): void
    {
        $this->createSystemNote($booking->id, 'Đặt phòng đã hoàn thành', 'system');
    }

    /**
     * Xử lý sự kiện booking no-show
     */
    public function onBookingNoShow(Booking $booking): void
    {
        $this->createSystemNote($booking->id, 'Khách không đến (No-show)', 'system');
    }

    /**
     * Tạo ghi chú hệ thống
     */
    private function createSystemNote(int $bookingId, string $content, string $type = 'system'): void
    {
        \App\Models\BookingNote::create([
            'booking_id' => $bookingId,
            'user_id' => 1, // Admin user ID 1
            'content' => $content,
            'type' => $type,
            'visibility' => 'internal',
            'is_internal' => true,
        ]);
    }

    /**
     * Tạo thông báo admin cho booking được tạo
     */
    public function notifyBookingCreated(Booking $booking): void
    {
        $this->createBookingNotification($booking->toArray());
    }

    /**
     * Tạo thông báo admin cho thay đổi trạng thái booking
     */
    public function notifyBookingStatusChanged(Booking $booking, string $oldStatus, string $newStatus): void
    {
        $this->createStatusChangeNotification($booking->toArray(), $oldStatus, $newStatus);
    }

    /**
     * Tạo thông báo admin cho booking bị hủy
     */
    public function notifyBookingCancelled(Booking $booking, string $reason): void
    {
        $this->createNotification(
            'booking_cancelled',
            'Đặt phòng bị hủy',
            "Đặt phòng #{$booking->booking_id} đã bị hủy: {$reason}",
            $booking->toArray(),
            'high',
            'fas fa-times-circle',
            'danger'
        );
    }

    /**
     * Xử lý thay đổi trạng thái booking
     */
    private function handleStatusChange(Booking $booking, string $oldStatus, string $newStatus): void
    {
        // Tạo ghi chú hệ thống
        $statusText = match($newStatus) {
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'checked_in' => 'Đã nhận phòng',
            'checked_out' => 'Đã trả phòng',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            'no_show' => 'Khách không đến',
            default => 'Không xác định'
        };

        $oldStatusText = match($oldStatus) {
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'checked_in' => 'Đã nhận phòng',
            'checked_out' => 'Đã trả phòng',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            'no_show' => 'Khách không đến',
            default => 'Không xác định'
        };

        $noteContent = "Thay đổi trạng thái từ '{$oldStatusText}' sang '{$statusText}'";
        $this->createSystemNote($booking->id, $noteContent, 'system');

        // Tạo thông báo admin
        $this->createStatusChangeNotification($booking->toArray(), $oldStatus, $newStatus);

        // Gọi các event handler tương ứng
        switch ($newStatus) {
            case 'confirmed':
                $this->onBookingConfirmed($booking);
                break;
            case 'checked_in':
                $this->onBookingCheckedIn($booking);
                break;
            case 'checked_out':
                $this->onBookingCheckedOut($booking);
                break;
            case 'completed':
                $this->onBookingCompleted($booking);
                break;
            case 'cancelled':
                $this->onBookingCancelled($booking, 'Đã hủy bởi admin');
                break;
            case 'no_show':
                $this->onBookingNoShow($booking);
                break;
        }
    }
} 