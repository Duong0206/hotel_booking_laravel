<?php

namespace App\Services;

use App\Interfaces\Repositories\RoomTypeReviewRepositoryInterface;
use App\Interfaces\Services\RoomTypeReviewServiceInterface;
use App\Models\RoomTypeReview;
use App\Models\RoomType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class RoomTypeReviewService implements RoomTypeReviewServiceInterface
{
    protected $roomTypeReviewRepository;

    public function __construct(RoomTypeReviewRepositoryInterface $roomTypeReviewRepository)
    {
        $this->roomTypeReviewRepository = $roomTypeReviewRepository;
    }

    /**
     * Tạo review mới cho admin
     */
    public function createReview(array $data, int $userId = null, int $roomTypeId = null): RoomTypeReview
    {
        $reviewData = [
            'user_id' => $userId ?? $data['user_id'],
            'room_type_id' => $roomTypeId ?? $data['room_type_id'],
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
            'cleanliness_rating' => $data['cleanliness_rating'] ?? null,
            'comfort_rating' => $data['comfort_rating'] ?? null,
            'location_rating' => $data['location_rating'] ?? null,
            'facilities_rating' => $data['facilities_rating'] ?? null,
            'value_rating' => $data['value_rating'] ?? null,
            'is_anonymous' => $data['is_anonymous'] ?? false,
            'status' => $data['status'] ?? 'pending',
        ];

        return $this->roomTypeReviewRepository->createReview($reviewData);
    }

    /**
     * Tạo review mới cho user
     */
    public function createReviewForUser(array $data, int $roomTypeId): RoomTypeReview
    {
        $bookingId = $data['booking_id'] ?? null;
        
        if (!$bookingId) {
            throw new \Exception('Thiếu thông tin booking.');
        }

        // Kiểm tra xem booking có tồn tại và thuộc về user hiện tại không
        $booking = \App\Models\Booking::with('room.roomType')
            ->where('id', $bookingId)
            ->where('user_id', Auth::id())
            ->where('status', 'completed')
            ->first();

        if (!$booking) {
            throw new \Exception('Booking không tồn tại hoặc không hợp lệ.');
        }

        // Kiểm tra xem booking có thuộc loại phòng đang đánh giá không
        if ($booking->room->room_type_id != $roomTypeId) {
            throw new \Exception('Booking không thuộc loại phòng này.');
        }

        // Kiểm tra xem booking đã được đánh giá chưa
        if ($this->roomTypeReviewRepository->hasUserReviewedBooking(Auth::id(), $bookingId)) {
            throw new \Exception('Bạn đã đánh giá booking này rồi.');
        }

        $reviewData = [
            'user_id' => Auth::id(),
            'room_type_id' => $roomTypeId,
            'booking_id' => $bookingId,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
            'cleanliness_rating' => $data['cleanliness_rating'] ?? null,
            'comfort_rating' => $data['comfort_rating'] ?? null,
            'location_rating' => $data['location_rating'] ?? null,
            'facilities_rating' => $data['facilities_rating'] ?? null,
            'value_rating' => $data['value_rating'] ?? null,
            'is_anonymous' => isset($data['is_anonymous']) && ($data['is_anonymous'] == '1' || $data['is_anonymous'] === true),
            'status' => 'approved',
        ];

        return $this->roomTypeReviewRepository->createReview($reviewData);
    }

    /**
     * Cập nhật review
     */
    public function updateReview(int $reviewId, array $data): bool
    {
        $review = $this->roomTypeReviewRepository->getReviewById($reviewId);
        
        if (!$review || $review->user_id !== Auth::id()) {
            throw new \Exception('Không tìm thấy review hoặc không có quyền chỉnh sửa.');
        }

        if (!$this->canBeEdited($review)) {
            throw new \Exception('Không thể chỉnh sửa đánh giá này.');
        }

        return $this->roomTypeReviewRepository->updateReview($reviewId, $data);
    }

    /**
     * Xóa review (Admin)
     */
    public function deleteReviewForAdmin(int $reviewId): bool
    {
        $review = $this->roomTypeReviewRepository->getReviewById($reviewId);
        
        if (!$review) {
            throw new \Exception('Không tìm thấy review.');
        }

        return $this->roomTypeReviewRepository->deleteReview($reviewId);
    }

    /**
     * Xóa review
     */
    public function deleteReview(int $reviewId): bool
    {
        $review = $this->roomTypeReviewRepository->getReviewById($reviewId);
        
        if (!$review || $review->user_id !== Auth::id()) {
            throw new \Exception('Không tìm thấy review hoặc không có quyền xóa.');
        }

        if (!$this->canBeDeleted($review)) {
            throw new \Exception('Không thể xóa đánh giá này.');
        }

        return $this->roomTypeReviewRepository->deleteReview($reviewId);
    }

    /**
     * Lấy review theo ID
     */
    public function getReviewById(int $id): ?RoomTypeReview
    {
        return $this->roomTypeReviewRepository->getReviewById($id);
    }

    /**
     * Lấy reviews của user
     */
    public function getUserReviews(int $perPage = 10): LengthAwarePaginator
    {
        return $this->roomTypeReviewRepository->getReviewsByUser(Auth::id(), $perPage);
    }

    /**
     * Lấy reviews của loại phòng
     */
    public function getRoomTypeReviews(int $roomTypeId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->roomTypeReviewRepository->getReviewsByRoomType($roomTypeId, $perPage);
    }

    /**
     * Lấy tất cả reviews cho admin
     */
    public function getAllReviewsForAdmin(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->roomTypeReviewRepository->getAllReviewsForAdmin($filters, $perPage);
    }

    /**
     * Duyệt review (Admin)
     */
    public function approveReview(int $reviewId): bool
    {
        return $this->roomTypeReviewRepository->approveReview($reviewId);
    }

    /**
     * Từ chối review (Admin)
     */
    public function rejectReview(int $reviewId): bool
    {
        return $this->roomTypeReviewRepository->rejectReview($reviewId);
    }

    /**
     * Lấy thống kê reviews
     */
    public function getReviewStatistics(): array
    {
        return $this->roomTypeReviewRepository->getReviewStatistics();
    }

    /**
     * Kiểm tra xem review có thể chỉnh sửa không
     */
    public function canBeEdited(RoomTypeReview $review): bool
    {
        return $review->status === 'pending';
    }

    /**
     * Kiểm tra xem review có thể xóa không
     */
    public function canBeDeleted(RoomTypeReview $review): bool
    {
        return $review->status === 'pending';
    }

    /**
     * Lấy rating trung bình của loại phòng
     */
    public function getRoomTypeAverageRating(int $roomTypeId): float
    {
        return $this->roomTypeReviewRepository->getAverageRatingByRoomType($roomTypeId);
    }

    /**
     * Lấy số lượng reviews của loại phòng
     */
    public function getRoomTypeReviewsCount(int $roomTypeId): int
    {
        return $this->roomTypeReviewRepository->getReviewsCountByRoomType($roomTypeId);
    }

    /**
     * Lấy các loại phòng mà user có thể đánh giá
     */
    public function getReviewableRoomTypes(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->roomTypeReviewRepository->getCompletedRoomTypesWithoutReview(Auth::id());
    }

    /**
     * Kiểm tra xem user có thể đánh giá loại phòng này không
     */
    public function canReviewRoomType(int $roomTypeId): bool
    {
        // Kiểm tra xem user đã đăng nhập chưa
        if (!Auth::check()) {
            return false;
        }
        
        // Kiểm tra xem user đã đánh giá chưa
        if ($this->roomTypeReviewRepository->hasUserReviewedRoomType(Auth::id(), $roomTypeId)) {
            return false;
        }

        // Kiểm tra xem user đã có booking hoàn thành cho loại phòng này chưa
        return \App\Models\Booking::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->whereHas('room', function($query) use ($roomTypeId) {
                $query->where('room_type_id', $roomTypeId);
            })
            ->exists();
    }

    /**
     * Kiểm tra xem user đã đánh giá loại phòng này chưa
     */
    public function hasUserReviewedRoomType(int $userId, int $roomTypeId): bool
    {
        return $this->roomTypeReviewRepository->hasUserReviewedRoomType($userId, $roomTypeId);
    }

    /**
     * Validate review data
     */
    public function validateReviewData(array $data): array
    {
        $rules = [
            'room_type_id' => ['required', 'integer', 'exists:room_types,id'],
            'booking_id' => ['required', 'integer', 'exists:bookings,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'cleanliness_rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'comfort_rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'location_rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'facilities_rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'value_rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'is_anonymous' => ['boolean'],
        ];

        $messages = [
            'room_type_id.required' => 'Vui lòng chọn loại phòng.',
            'room_type_id.exists' => 'Loại phòng không tồn tại.',
            'booking_id.required' => 'Thiếu thông tin booking.',
            'booking_id.exists' => 'Booking không tồn tại.',
            'rating.required' => 'Vui lòng chọn điểm đánh giá tổng thể.',
            'rating.min' => 'Điểm đánh giá phải từ 1-5.',
            'rating.max' => 'Điểm đánh giá phải từ 1-5.',
            'comment.max' => 'Bình luận không được quá 1000 ký tự.',
            'cleanliness_rating.min' => 'Điểm đánh giá vệ sinh phải từ 1-5.',
            'cleanliness_rating.max' => 'Điểm đánh giá vệ sinh phải từ 1-5.',
            'comfort_rating.min' => 'Điểm đánh giá tiện nghi phải từ 1-5.',
            'comfort_rating.max' => 'Điểm đánh giá tiện nghi phải từ 1-5.',
            'location_rating.min' => 'Điểm đánh giá vị trí phải từ 1-5.',
            'location_rating.max' => 'Điểm đánh giá vị trí phải từ 1-5.',
            'facilities_rating.min' => 'Điểm đánh giá cơ sở vật chất phải từ 1-5.',
            'facilities_rating.max' => 'Điểm đánh giá cơ sở vật chất phải từ 1-5.',
            'value_rating.min' => 'Điểm đánh giá giá trị phải từ 1-5.',
            'value_rating.max' => 'Điểm đánh giá giá trị phải từ 1-5.',
        ];

        $validator = \Illuminate\Support\Facades\Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        return $validator->validated();
    }

    public function canReviewBooking(int $bookingId, int $roomTypeId): bool
    {
        if (!\Auth::check()) return false;
        $booking = \App\Models\Booking::with('room')->where('id', $bookingId)
            ->where('user_id', \Auth::id())
            ->where('status', 'completed')
            ->first();
        if (!$booking) return false;
        if ($booking->room->room_type_id != $roomTypeId) return false;
        return !$this->roomTypeReviewRepository->hasUserReviewedBooking(\Auth::id(), $bookingId);
    }
} 