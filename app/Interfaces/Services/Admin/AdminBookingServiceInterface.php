<?php

namespace App\Interfaces\Services\Admin;

use App\Models\Booking;
use App\Models\AdminNotification;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface AdminBookingServiceInterface
{
    // ==================== BOOKING METHODS ====================

    /**
     * Lấy danh sách đặt phòng có phân trang
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getBookingsWithPagination(Request $request): LengthAwarePaginator;
    
    /**
     * Lấy chi tiết đặt phòng
     *
     * @param int $id
     * @return Booking
     */
    public function getBookingDetails(int $id): Booking;
    
    /**
     * Tạo đặt phòng mới
     *
     * @param array $data
     * @return Booking
     */
    public function createBooking(array $data): Booking;
    
    /**
     * Cập nhật đặt phòng
     *
     * @param int $id
     * @param array $data
     * @return Booking
     */
    public function updateBooking(int $id, array $data): Booking;
    
    /**
     * Xóa đặt phòng
     *
     * @param int $id
     * @return bool
     */
    public function deleteBooking(int $id): bool;
    
    /**
     * Cập nhật trạng thái đặt phòng
     *
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateBookingStatus(int $id, string $status): bool;
    
    /**
     * Lấy dữ liệu cho dashboard
     *
     * @return array
     */
    public function getDashboardData(): array;
    
    /**
     * Lấy dữ liệu cho báo cáo
     *
     * @param Request $request
     * @return array
     */
    public function getReportData(Request $request): array;
    
    /**
     * Lấy dữ liệu cho form tạo đặt phòng
     *
     * @return array
     */
    public function getCreateFormData(): array;
    
    /**
     * Lấy dữ liệu cho form chỉnh sửa đặt phòng
     *
     * @param int $id
     * @return array
     */
    public function getEditFormData(int $id): array;

    /**
     * Lấy danh sách trạng thái hợp lệ tiếp theo cho booking
     *
     * @param int $id
     * @return array
     */
    public function getValidNextStatuses(int $id): array;

    // ==================== NOTIFICATION METHODS ====================

    /**
     * Lấy số lượng thông báo chưa đọc
     *
     * @return int
     */
    public function getUnreadCount(): int;

    /**
     * Lấy số lượng thông báo chưa đọc theo mức độ ưu tiên
     *
     * @return array
     */
    public function getUnreadCountByPriority(): array;

    /**
     * Lấy danh sách thông báo chưa đọc
     *
     * @param int $limit
     * @return Collection
     */
    public function getUnreadNotifications(int $limit = 10): Collection;

    /**
     * Đánh dấu thông báo đã đọc
     *
     * @param int $notificationId
     * @return bool
     */
    public function markAsRead(int $notificationId): bool;

    /**
     * Đánh dấu tất cả thông báo đã đọc
     *
     * @return int
     */
    public function markAllAsRead(): int;

    /**
     * Xóa thông báo cũ (quá 30 ngày)
     *
     * @return int
     */
    public function deleteOldNotifications(): int;

    /**
     * Tạo thông báo mới
     *
     * @param string $type
     * @param string $title
     * @param string $message
     * @param array $data
     * @param string $priority
     * @param string|null $icon
     * @param string|null $color
     * @return AdminNotification
     */
    public function createNotification(
        string $type,
        string $title,
        string $message,
        array $data = [],
        string $priority = 'normal',
        string $icon = null,
        string $color = null
    ): AdminNotification;

    /**
     * Tạo thông báo cho ghi chú mới
     *
     * @param array $noteData
     * @return AdminNotification
     */
    public function createNoteNotification(array $noteData): AdminNotification;

    /**
     * Tạo thông báo cho đánh giá mới
     *
     * @param array $reviewData
     * @return AdminNotification
     */
    public function createReviewNotification(array $reviewData): AdminNotification;

    /**
     * Tạo thông báo cho đặt phòng mới
     *
     * @param array $bookingData
     * @return AdminNotification
     */
    public function createBookingNotification(array $bookingData): AdminNotification;

    /**
     * Tạo thông báo cho thay đổi trạng thái đặt phòng
     *
     * @param array $bookingData
     * @param string $oldStatus
     * @param string $newStatus
     * @return AdminNotification
     */
    public function createStatusChangeNotification(array $bookingData, string $oldStatus, string $newStatus): AdminNotification;

    /**
     * Xóa hàng loạt thông báo
     *
     * @param array $ids
     * @return int
     */
    public function deleteNotifications(array $ids): int;

    /**
     * Đánh dấu đã đọc hàng loạt thông báo
     *
     * @param array $ids
     * @return int
     */
    public function markNotificationsAsRead(array $ids): int;

    // ==================== EVENT HANDLER METHODS ====================

    /**
     * Xử lý sự kiện booking được tạo
     *
     * @param Booking $booking
     * @return void
     */
    public function onBookingCreated(Booking $booking): void;

    /**
     * Xử lý sự kiện booking được cập nhật
     *
     * @param Booking $booking
     * @param array $changes
     * @return void
     */
    public function onBookingUpdated(Booking $booking, array $changes): void;

    /**
     * Xử lý sự kiện booking bị hủy
     *
     * @param Booking $booking
     * @param string $reason
     * @return void
     */
    public function onBookingCancelled(Booking $booking, string $reason = ''): void;

    /**
     * Xử lý sự kiện booking được xác nhận
     *
     * @param Booking $booking
     * @return void
     */
    public function onBookingConfirmed(Booking $booking): void;

    /**
     * Xử lý sự kiện booking check-in
     *
     * @param Booking $booking
     * @return void
     */
    public function onBookingCheckedIn(Booking $booking): void;

    /**
     * Xử lý sự kiện booking check-out
     *
     * @param Booking $booking
     * @return void
     */
    public function onBookingCheckedOut(Booking $booking): void;

    /**
     * Xử lý sự kiện booking hoàn thành
     *
     * @param Booking $booking
     * @return void
     */
    public function onBookingCompleted(Booking $booking): void;

    /**
     * Xử lý sự kiện booking no-show
     *
     * @param Booking $booking
     * @return void
     */
    public function onBookingNoShow(Booking $booking): void;

    /**
     * Tạo thông báo admin cho booking được tạo
     *
     * @param Booking $booking
     * @return void
     */
    public function notifyBookingCreated(Booking $booking): void;

    /**
     * Tạo thông báo admin cho thay đổi trạng thái booking
     *
     * @param Booking $booking
     * @param string $oldStatus
     * @param string $newStatus
     * @return void
     */
    public function notifyBookingStatusChanged(Booking $booking, string $oldStatus, string $newStatus): void;

    /**
     * Tạo thông báo admin cho booking bị hủy
     *
     * @param Booking $booking
     * @param string $reason
     * @return void
     */
    public function notifyBookingCancelled(Booking $booking, string $reason): void;
} 