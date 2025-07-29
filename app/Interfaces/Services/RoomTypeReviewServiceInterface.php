<?php

namespace App\Interfaces\Services;

use App\Models\RoomTypeReview;
use Illuminate\Pagination\LengthAwarePaginator;

interface RoomTypeReviewServiceInterface
{
    /**
     * Tạo review mới cho admin
     */
    public function createReview(array $data, int $userId = null, int $roomTypeId = null): RoomTypeReview;

    /**
     * Tạo review mới cho user
     */
    public function createReviewForUser(array $data, int $roomTypeId): RoomTypeReview;

    /**
     * Cập nhật review
     */
    public function updateReview(int $reviewId, array $data): bool;

    /**
     * Xóa review (Admin)
     */
    public function deleteReviewForAdmin(int $reviewId): bool;

    /**
     * Xóa review
     */
    public function deleteReview(int $reviewId): bool;

    /**
     * Lấy review theo ID
     */
    public function getReviewById(int $id): ?RoomTypeReview;

    /**
     * Lấy reviews của user
     */
    public function getUserReviews(int $perPage = 10): LengthAwarePaginator;

    /**
     * Lấy reviews của loại phòng
     */
    public function getRoomTypeReviews(int $roomTypeId, int $perPage = 10): LengthAwarePaginator;

    /**
     * Lấy tất cả reviews cho admin
     */
    public function getAllReviewsForAdmin(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Duyệt review (Admin)
     */
    public function approveReview(int $reviewId): bool;

    /**
     * Từ chối review (Admin)
     */
    public function rejectReview(int $reviewId): bool;

    /**
     * Lấy thống kê reviews
     */
    public function getReviewStatistics(): array;

    /**
     * Kiểm tra xem review có thể chỉnh sửa không
     */
    public function canBeEdited(RoomTypeReview $review): bool;

    /**
     * Kiểm tra xem review có thể xóa không
     */
    public function canBeDeleted(RoomTypeReview $review): bool;

    /**
     * Lấy rating trung bình của loại phòng
     */
    public function getRoomTypeAverageRating(int $roomTypeId): float;

    /**
     * Lấy số lượng reviews của loại phòng
     */
    public function getRoomTypeReviewsCount(int $roomTypeId): int;

    /**
     * Lấy các loại phòng mà user có thể đánh giá
     */
    public function getReviewableRoomTypes(): \Illuminate\Database\Eloquent\Collection;

    /**
     * Kiểm tra xem user có thể đánh giá loại phòng này không
     */
    public function canReviewRoomType(int $roomTypeId): bool;

    /**
     * Kiểm tra xem user đã đánh giá loại phòng này chưa
     */
    public function hasUserReviewedRoomType(int $userId, int $roomTypeId): bool;

    /**
     * Validate review data
     */
    public function validateReviewData(array $data): array;
} 