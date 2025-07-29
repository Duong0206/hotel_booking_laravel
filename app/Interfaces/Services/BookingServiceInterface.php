<?php

namespace App\Interfaces\Services;

use App\Models\Booking;
use App\Models\BookingNote;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

interface BookingServiceInterface
{
    // ==================== BOOKING METHODS ====================

    /**
     * Lấy thông tin trang đặt phòng
     *
     * @return array
     */
    public function getBookingPageData(): array;
    
    /**
     * Tạo đặt phòng mới
     *
     * @param array $data
     * @return Booking
     */
    public function createBooking(array $data): Booking;
    
    /**
     * Lấy danh sách đặt phòng của người dùng hiện tại (có phân trang)
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getCurrentUserBookings($perPage = 10);

    /**
     * Lấy các booking đã hoàn thành và chưa đánh giá của người dùng hiện tại
     *
     * @return Collection
     */
    public function getCompletedBookingsWithoutReview(): Collection;

    /**
     * Lấy các booking đã hoàn thành của người dùng hiện tại
     *
     * @return Collection
     */
    public function getCompletedBookings(): Collection;

    /**
     * Kiểm tra xem booking có thể đánh giá không
     *
     * @param int $bookingId
     * @return bool
     */
    public function canBeReviewed(int $bookingId): bool;
    
    /**
     * Hủy đặt phòng
     *
     * @param int $bookingId
     * @return bool
     */
    public function cancelBooking(int $bookingId): bool;

    /**
     * Lấy chi tiết đặt phòng theo ID
     *
     * @param int $id
     * @return Booking
     */
    public function getBookingDetail(int $id): Booking;

    /**
     * Lấy danh sách đặt phòng theo userId
     *
     * @param int $userId
     * @return Collection
     */
    public function getBookingsByUser(int $userId): Collection;

    // ==================== BOOKING NOTE METHODS ====================

    /**
     * Lấy ghi chú của một booking mà user có thể xem
     *
     * @param int $bookingId
     * @return Collection
     */
    public function getVisibleNotes(int $bookingId): Collection;

    /**
     * Tạo ghi chú mới
     *
     * @param array $data
     * @return BookingNote
     */
    public function createNote(array $data): BookingNote;

    /**
     * Cập nhật ghi chú
     *
     * @param int $noteId
     * @param array $data
     * @return bool
     */
    public function updateNote(int $noteId, array $data): bool;

    /**
     * Xóa ghi chú
     *
     * @param int $noteId
     * @return bool
     */
    public function deleteNote(int $noteId): bool;

    /**
     * Lấy ghi chú theo loại
     *
     * @param int $bookingId
     * @param string $type
     * @return Collection
     */
    public function getNotesByType(int $bookingId, string $type): Collection;

    /**
     * Lấy ghi chú theo visibility
     *
     * @param int $bookingId
     * @param string $visibility
     * @return Collection
     */
    public function getNotesByVisibility(int $bookingId, string $visibility): Collection;

    /**
     * Lấy ghi chú theo ID
     *
     * @param int $noteId
     * @return BookingNote|null
     */
    public function getNoteById(int $noteId): ?BookingNote;

    /**
     * Lấy ghi chú có phân trang
     *
     * @param int $bookingId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedNotes(int $bookingId, int $perPage = 10): LengthAwarePaginator;

    /**
     * Tìm kiếm ghi chú
     *
     * @param int $bookingId
     * @param string $keyword
     * @return Collection
     */
    public function searchNotes(int $bookingId, string $keyword): Collection;

    /**
     * Lấy thống kê ghi chú
     *
     * @param int $bookingId
     * @return array
     */
    public function getNoteStatistics(int $bookingId): array;

    /**
     * Kiểm tra quyền xem ghi chú
     *
     * @param int $noteId
     * @return bool
     */
    public function canViewNote(int $noteId): bool;

    /**
     * Kiểm tra quyền chỉnh sửa ghi chú
     *
     * @param int $noteId
     * @return bool
     */
    public function canEditNote(int $noteId): bool;

    /**
     * Kiểm tra quyền xóa ghi chú
     *
     * @param int $noteId
     * @return bool
     */
    public function canDeleteNote(int $noteId): bool;

    /**
     * Tạo ghi chú hệ thống tự động
     *
     * @param int $bookingId
     * @param string $content
     * @param string $type (admin, system)
     * @return BookingNote
     */
    public function createSystemNote(int $bookingId, string $content, string $type = 'system'): BookingNote;

    /**
     * Tạo ghi chú thông báo cho khách hàng
     *
     * @param int $bookingId
     * @param string $content
     * @return BookingNote
     */
    public function createCustomerNotification(int $bookingId, string $content): BookingNote;

    /**
     * Tạo ghi chú nội bộ cho nhân viên
     *
     * @param int $bookingId
     * @param string $content
     * @return BookingNote
     */
    public function createInternalNote(int $bookingId, string $content): BookingNote;

    /**
     * Tạo yêu cầu từ customer (chỉ admin/staff mới gọi được)
     *
     * @param int $bookingId
     * @param string $content
     * @param int $customerId
     * @return BookingNote
     */
    public function createCustomerRequest(int $bookingId, string $content, int $customerId): BookingNote;

    /**
     * Tạo phản hồi từ admin/staff cho customer
     *
     * @param int $bookingId
     * @param string $content
     * @param string $type
     * @return BookingNote
     */
    public function createAdminResponse(int $bookingId, string $content, string $type = 'admin'): BookingNote;

    /**
     * Lấy ghi chú gần đây nhất
     *
     * @param int $bookingId
     * @param int $limit
     * @return Collection
     */
    public function getRecentNotes(int $bookingId, int $limit = 5): Collection;

    /**
     * Lấy ghi chú theo ngày
     *
     * @param int $bookingId
     * @param string $date
     * @return Collection
     */
    public function getNotesByDate(int $bookingId, string $date): Collection;

    /**
     * Lấy ghi chú theo khoảng thời gian
     *
     * @param int $bookingId
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function getNotesByDateRange(int $bookingId, string $startDate, string $endDate): Collection;
} 