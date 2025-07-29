<?php

namespace App\Observers;

use App\Models\RoomTypeReview;
use App\Interfaces\Services\Admin\AdminBookingServiceInterface;

class RoomTypeReviewObserver
{
    protected $adminBookingService;

    public function __construct(AdminBookingServiceInterface $adminBookingService)
    {
        $this->adminBookingService = $adminBookingService;
    }

    /**
     * Handle the RoomTypeReview "created" event.
     */
    public function created(RoomTypeReview $roomTypeReview)
    {
        // Tạo thông báo admin khi có đánh giá mới
        $this->adminBookingService->createReviewNotification([
            'review_id' => $roomTypeReview->id,
            'user_id' => $roomTypeReview->user_id,
            'room_type_id' => $roomTypeReview->room_type_id,
            'rating' => $roomTypeReview->rating,
            'room_type_name' => $roomTypeReview->roomType->name ?? 'N/A'
        ]);
    }

    /**
     * Handle the RoomTypeReview "updated" event.
     */
    public function updated(RoomTypeReview $roomTypeReview)
    {
        // Tạo thông báo admin khi đánh giá được cập nhật
        $this->adminBookingService->createNotification(
            'room_type_review_updated',
            'Đánh giá phòng được cập nhật',
            "Đánh giá {$roomTypeReview->rating}/5 sao cho {$roomTypeReview->roomType->name} đã được cập nhật",
            [
                'review_id' => $roomTypeReview->id,
                'user_id' => $roomTypeReview->user_id,
                'room_type_id' => $roomTypeReview->room_type_id,
                'rating' => $roomTypeReview->rating,
                'room_type_name' => $roomTypeReview->roomType->name ?? 'N/A'
            ],
            'normal',
            'fas fa-edit',
            'info'
        );
    }

    /**
     * Handle the RoomTypeReview "deleted" event.
     */
    public function deleted(RoomTypeReview $roomTypeReview)
    {
        // Tạo thông báo admin khi đánh giá bị xóa
        $this->adminBookingService->createNotification(
            'room_type_review_deleted',
            'Đánh giá phòng bị xóa',
            "Đánh giá cho {$roomTypeReview->roomType->name} đã bị xóa",
            [
                'review_id' => $roomTypeReview->id,
                'user_id' => $roomTypeReview->user_id,
                'room_type_id' => $roomTypeReview->room_type_id,
                'rating' => $roomTypeReview->rating,
                'room_type_name' => $roomTypeReview->roomType->name ?? 'N/A'
            ],
            'normal',
            'fas fa-trash',
            'warning'
        );
    }

    /**
     * Handle the RoomTypeReview "restored" event.
     */
    public function restored(RoomTypeReview $roomTypeReview): void
    {
        // Có thể thêm thông báo khi đánh giá được khôi phục nếu cần
    }

    /**
     * Handle the RoomTypeReview "force deleted" event.
     */
    public function forceDeleted(RoomTypeReview $roomTypeReview): void
    {
        // Có thể thêm thông báo khi đánh giá bị xóa vĩnh viễn nếu cần
    }
}
