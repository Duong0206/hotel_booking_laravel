<?php

namespace App\Repositories;

use App\Interfaces\Repositories\RoomTypeReviewRepositoryInterface;
use App\Models\RoomTypeReview;
use App\Models\Booking;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class RoomTypeReviewRepository implements RoomTypeReviewRepositoryInterface
{
    /**
     * Lấy tất cả reviews
     */
    public function getAllReviews(): Collection
    {
        return RoomTypeReview::with(['user', 'roomType'])->get();
    }

    /**
     * Lấy reviews theo loại phòng
     */
    public function getReviewsByRoomType(int $roomTypeId, int $perPage = 10): LengthAwarePaginator
    {
        return RoomTypeReview::where('room_type_id', $roomTypeId)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Lấy reviews theo user
     */
    public function getReviewsByUser(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return RoomTypeReview::where('user_id', $userId)
            ->with('roomType')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Lấy review theo ID
     */
    public function getReviewById(int $id): ?RoomTypeReview
    {
        return RoomTypeReview::with(['user', 'roomType'])->find($id);
    }

    /**
     * Tạo review mới
     */
    public function createReview(array $data): RoomTypeReview
    {
        return RoomTypeReview::create($data);
    }

    /**
     * Cập nhật review
     */
    public function updateReview(int $id, array $data): bool
    {
        $review = RoomTypeReview::find($id);
        return $review ? $review->update($data) : false;
    }

    /**
     * Xóa review
     */
    public function deleteReview(int $id): bool
    {
        $review = RoomTypeReview::find($id);
        return $review ? $review->delete() : false;
    }

    /**
     * Lấy reviews cho admin với filter
     */
    public function getAllReviewsForAdmin(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = RoomTypeReview::with(['user', 'roomType']);

        // Filter theo trạng thái
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter theo loại phòng
        if (isset($filters['room_type_id'])) {
            $query->where('room_type_id', $filters['room_type_id']);
        }

        // Filter theo rating
        if (isset($filters['rating'])) {
            $query->where('rating', $filters['rating']);
        }

        // Filter theo user
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // Tìm kiếm theo tên khách hàng
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Lấy thống kê reviews
     */
    public function getReviewStatistics(): array
    {
        $totalReviews = RoomTypeReview::where('status', 'approved')->count();
        $pendingReviews = RoomTypeReview::where('status', 'pending')->count();
        $approvedReviews = RoomTypeReview::where('status', 'approved')->count();
        $rejectedReviews = RoomTypeReview::where('status', 'rejected')->count();
        $averageRating = RoomTypeReview::where('status', 'approved')->avg('rating');
        $ratingStats = [];
        for ($i = 1; $i <= 5; $i++) {
            $ratingStats[$i] = RoomTypeReview::where('status', 'approved')
                ->where('rating', $i)
                ->count();
        }

        // Top loại phòng được đánh giá nhiều nhất
        $topRoomTypes = \App\Models\RoomType::withCount(['reviews as approved_reviews_count' => function($query) {
            $query->where('status', 'approved');
        }])
        ->withAvg(['reviews as average_rating' => function($query) {
            $query->where('status', 'approved');
        }], 'rating')
        ->orderBy('approved_reviews_count', 'desc')
        ->limit(5)
        ->get();

        // Thống kê đánh giá chi tiết
        $detailedStats = [
            'cleanliness' => RoomTypeReview::where('status', 'approved')
                ->whereNotNull('cleanliness_rating')
                ->avg('cleanliness_rating'),
            'comfort' => RoomTypeReview::where('status', 'approved')
                ->whereNotNull('comfort_rating')
                ->avg('comfort_rating'),
            'location' => RoomTypeReview::where('status', 'approved')
                ->whereNotNull('location_rating')
                ->avg('location_rating'),
            'facilities' => RoomTypeReview::where('status', 'approved')
                ->whereNotNull('facilities_rating')
                ->avg('facilities_rating'),
            'value' => RoomTypeReview::where('status', 'approved')
                ->whereNotNull('value_rating')
                ->avg('value_rating'),
        ];

        // Làm tròn các giá trị
        foreach ($detailedStats as $key => $value) {
            $detailedStats[$key] = $value ? round($value, 1) : 0;
        }

        return [
            'total_reviews' => $totalReviews,
            'pending_reviews_count' => $pendingReviews,
            'approved_reviews_count' => $approvedReviews,
            'rejected_reviews_count' => $rejectedReviews,
            'average_rating' => $averageRating,
            'rating_stats' => $ratingStats,
            'top_room_types' => $topRoomTypes,
            'detailed_stats' => $detailedStats
        ];
    }

    /**
     * Duyệt review
     */
    public function approveReview(int $id): bool
    {
        $review = RoomTypeReview::find($id);
        return $review ? $review->update(['status' => 'approved']) : false;
    }

    /**
     * Từ chối review
     */
    public function rejectReview(int $id): bool
    {
        $review = RoomTypeReview::find($id);
        return $review ? $review->update(['status' => 'rejected']) : false;
    }

    /**
     * Kiểm tra xem user đã đánh giá loại phòng này chưa
     */
    public function hasUserReviewedRoomType(int $userId, int $roomTypeId): bool
    {
        return RoomTypeReview::where('user_id', $userId)
            ->where('room_type_id', $roomTypeId)
            ->exists();
    }

    /**
     * Kiểm tra xem user đã đánh giá booking này chưa
     */
    public function hasUserReviewedBooking(int $userId, int $bookingId): bool
    {
        return RoomTypeReview::where('user_id', $userId)
            ->where('booking_id', $bookingId)
            ->exists();
    }

    /**
     * Lấy rating trung bình của loại phòng
     */
    public function getAverageRatingByRoomType(int $roomTypeId): float
    {
        $rating = RoomTypeReview::where('room_type_id', $roomTypeId)
            ->avg('rating');
        
        return $rating ? round($rating, 1) : 0;
    }

    /**
     * Lấy số lượng reviews của loại phòng
     */
    public function getReviewsCountByRoomType(int $roomTypeId): int
    {
        return RoomTypeReview::where('room_type_id', $roomTypeId)
            ->count();
    }

    /**
     * Lấy top loại phòng được đánh giá nhiều nhất
     */
    public function getTopRatedRoomTypes(int $limit = 10): Collection
    {
        return \App\Models\RoomType::withCount(['reviews as approved_reviews_count' => function($query) {
            $query->where('status', 'approved');
        }])
        ->orderBy('approved_reviews_count', 'desc')
        ->limit($limit)
        ->get();
    }

    /**
     * Lấy các loại phòng mà user đã booking hoàn thành nhưng chưa đánh giá
     */
    public function getCompletedRoomTypesWithoutReview(int $userId): Collection
    {
        // Lấy các loại phòng mà user đã booking hoàn thành
        $completedRoomTypeIds = Booking::where('user_id', $userId)
            ->where('bookings.status', 'completed')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->pluck('rooms.room_type_id')
            ->unique();

        // Lọc ra những loại phòng chưa được user đánh giá
        $reviewedRoomTypeIds = RoomTypeReview::where('user_id', $userId)
            ->pluck('room_type_id');

        $roomTypeIdsToReview = $completedRoomTypeIds->diff($reviewedRoomTypeIds);

        return \App\Models\RoomType::whereIn('id', $roomTypeIdsToReview)->get();
    }
} 