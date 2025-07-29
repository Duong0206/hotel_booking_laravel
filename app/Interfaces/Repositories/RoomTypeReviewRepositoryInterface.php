<?php

namespace App\Interfaces\Repositories;

use App\Models\RoomTypeReview;
use Illuminate\Database\Eloquent\Collection;

interface RoomTypeReviewRepositoryInterface
{
    /**
     * Lấy tất cả reviews
     */
    public function getAllReviews(): Collection;

    /**
     * Lấy reviews theo loại phòng
     */
    public function getReviewsByRoomType(int $roomTypeId, int $perPage = 10): \Illuminate\Contracts\Pagination\LengthAwarePaginator;

    /**
     * Lấy reviews theo user
     */
    public function getReviewsByUser(int $userId, int $perPage = 10): \Illuminate\Contracts\Pagination\LengthAwarePaginator;

    /**
     * Lấy review theo ID
     */
    public function getReviewById(int $id): ?RoomTypeReview;

    /**
     * Tạo review mới
     */
    public function createReview(array $data): RoomTypeReview;

    /**
     * Cập nhật review
     */
    public function updateReview(int $id, array $data): bool;

    /**
     * Xóa review
     */
    public function deleteReview(int $id): bool;

    /**
     * Lấy reviews cho admin với filter
     */
    public function getAllReviewsForAdmin(array $filters = [], int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator;

    /**
     * Lấy thống kê reviews
     */
    public function getReviewStatistics(): array;

    /**
     * Duyệt review
     */
    public function approveReview(int $id): bool;

    /**
     * Từ chối review
     */
    public function rejectReview(int $id): bool;

    /**
     * Kiểm tra xem user đã đánh giá loại phòng này chưa
     */
    public function hasUserReviewedRoomType(int $userId, int $roomTypeId): bool;

    /**
     * Kiểm tra xem user đã đánh giá booking này chưa
     */
    public function hasUserReviewedBooking(int $userId, int $bookingId): bool;

    /**
     * Lấy rating trung bình của loại phòng
     */
    public function getAverageRatingByRoomType(int $roomTypeId): float;

    /**
     * Lấy số lượng reviews của loại phòng
     */
    public function getReviewsCountByRoomType(int $roomTypeId): int;

    /**
     * Lấy top loại phòng được đánh giá nhiều nhất
     */
    public function getTopRatedRoomTypes(int $limit = 10): Collection;

    /**
     * Lấy các loại phòng mà user đã booking hoàn thành nhưng chưa đánh giá
     */
    public function getCompletedRoomTypesWithoutReview(int $userId): Collection;
} 