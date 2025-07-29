<?php

namespace App\Interfaces\Repositories;

use App\Models\Booking;
use App\Models\BookingNote;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface BookingRepositoryInterface
{
    // ==================== BOOKING METHODS ====================

    /**
     * Lấy tất cả đặt phòng của người dùng
     *
     * @param int $userId
     * @return Collection
     */
    public function getByUserId(int $userId): Collection;

    /**
     * Lấy các booking đã hoàn thành và chưa đánh giá của người dùng
     *
     * @param int $userId
     * @return Collection
     */
    public function getCompletedBookingsWithoutReview(int $userId): Collection;

    /**
     * Lấy các booking đã hoàn thành của người dùng (có thể đã đánh giá hoặc chưa)
     *
     * @param int $userId
     * @return Collection
     */
    public function getCompletedBookings(int $userId): Collection;

    /**
     * Kiểm tra xem booking có thể đánh giá không
     *
     * @param int $bookingId
     * @param int $userId
     * @return bool
     */
    public function canBeReviewed(int $bookingId, int $userId): bool;

    /**
     * Tạo đặt phòng mới
     *
     * @param array $data
     * @return Booking
     */
    public function create(array $data): Booking;

    /**
     * Tìm đặt phòng theo ID
     *
     * @param int $id
     * @param int $userId
     * @return Booking|null
     */
    public function findByIdAndUserId(int $id, int $userId): ?Booking;

    /**
     * Cập nhật đặt phòng
     *
     * @param Booking $booking
     * @param array $data
     * @return bool
     */
    public function update(Booking $booking, array $data): bool;

    /**
     * Lấy chi tiết đặt phòng theo ID
     *
     * @param int $id
     * @return Booking|null
     */
    public function getDetailById(int $id): ?Booking;

    /**
     * Lấy tất cả đặt phòng của người dùng (có phân trang)
     *
     * @param int $userId
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByUserIdPaginate(int $userId, $perPage = 10);

    /**
     * Kiểm tra xem user đã có booking hoàn thành cho loại phòng này chưa
     *
     * @param int $userId
     * @param int $roomTypeId
     * @return bool
     */
    public function hasUserCompletedBookingForRoomType(int $userId, int $roomTypeId): bool;

    /**
     * Hàm kiểm tra phòng còn trống theo datetime
     *
     * @param int $roomId
     * @param string $checkInDateTime
     * @param string $checkOutDateTime
     * @return bool
     */
    public function isRoomAvailable($roomId, $checkInDateTime, $checkOutDateTime): bool;

    // ==================== BOOKING NOTE METHODS ====================

    /**
     * Lấy tất cả ghi chú của một booking
     *
     * @param int $bookingId
     * @return Collection
     */
    public function getByBookingId(int $bookingId): Collection;

    /**
     * Lấy ghi chú công khai của một booking
     *
     * @param int $bookingId
     * @return Collection
     */
    public function getPublicNotesByBookingId(int $bookingId): Collection;

    /**
     * Lấy ghi chú nội bộ của một booking
     *
     * @param int $bookingId
     * @return Collection
     */
    public function getInternalNotesByBookingId(int $bookingId): Collection;

    /**
     * Lấy ghi chú mà user có thể xem
     *
     * @param int $bookingId
     * @param int $userId
     * @param array $userRoles
     * @return Collection
     */
    public function getVisibleNotesByBookingId(int $bookingId, int $userId, array $userRoles = []): Collection;

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
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateNote(int $id, array $data): bool;

    /**
     * Xóa ghi chú
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Tìm ghi chú theo ID
     *
     * @param int $id
     * @return BookingNote|null
     */
    public function findById(int $id): ?BookingNote;

    /**
     * Kiểm tra xem user có thể xem ghi chú không
     *
     * @param int $noteId
     * @param int $userId
     * @param array $userRoles
     * @return bool
     */
    public function canUserViewNote(int $noteId, int $userId, array $userRoles = []): bool;

    /**
     * Kiểm tra xem user có thể chỉnh sửa ghi chú không
     *
     * @param int $noteId
     * @param int $userId
     * @param array $userRoles
     * @return bool
     */
    public function canUserEditNote(int $noteId, int $userId, array $userRoles = []): bool;

    /**
     * Kiểm tra xem user có thể xóa ghi chú không
     *
     * @param int $noteId
     * @param int $userId
     * @param array $userRoles
     * @return bool
     */
    public function canUserDeleteNote(int $noteId, int $userId, array $userRoles = []): bool;

    /**
     * Lấy ghi chú theo loại
     *
     * @param int $bookingId
     * @param string $type
     * @return Collection
     */
    public function getByType(int $bookingId, string $type): Collection;

    /**
     * Lấy ghi chú theo visibility
     *
     * @param int $bookingId
     * @param string $visibility
     * @return Collection
     */
    public function getByVisibility(int $bookingId, string $visibility): Collection;

    /**
     * Lấy ghi chú có phân trang
     *
     * @param int $bookingId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginated(int $bookingId, int $perPage = 10): LengthAwarePaginator;

    /**
     * Tìm kiếm ghi chú theo từ khóa
     *
     * @param int $bookingId
     * @param string $keyword
     * @return Collection
     */
    public function search(int $bookingId, string $keyword): Collection;

    /**
     * Lấy thống kê ghi chú của booking
     *
     * @param int $bookingId
     * @return array
     */
    public function getStatistics(int $bookingId): array;
}