<?php

namespace App\Observers;

use App\Models\Booking;
use App\Interfaces\Services\Admin\AdminBookingServiceInterface;
use Illuminate\Support\Facades\Log;

class BookingObserver
{
    protected $adminBookingService;

    public function __construct(
        AdminBookingServiceInterface $adminBookingService
    ) {
        $this->adminBookingService = $adminBookingService;
    }

    /**
     * Handle the Booking "created" event.
     */
    public function created(Booking $booking): void
    {
        // Tạo ghi chú khi booking được tạo
        $this->adminBookingService->onBookingCreated($booking);
        
        // Tạo thông báo admin
        $this->adminBookingService->notifyBookingCreated($booking);
    }

    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        // Kiểm tra các thay đổi quan trọng
        $changes = $booking->getChanges();
        $importantFields = ['status', 'check_in_date', 'check_out_date', 'room_id', 'price'];
        $importantChanges = array_intersect_key($changes, array_flip($importantFields));

        if (!empty($importantChanges)) {
            // Tạo ghi chú cho thay đổi trạng thái
            if (isset($changes['status'])) {
                $this->handleStatusChange($booking, $changes['status']);
                
                // Tạo thông báo admin cho thay đổi trạng thái
                $originalStatus = $booking->getOriginal('status');
                $this->adminBookingService->notifyBookingStatusChanged($booking, $originalStatus, $changes['status']);
            }

            // Tạo ghi chú cho các thay đổi khác
            if (count($importantChanges) > 1 || !isset($changes['status'])) {
                $this->adminBookingService->onBookingUpdated($booking, $importantChanges);
            }
        }
    }

    /**
     * Handle the Booking "deleted" event.
     */
    public function deleted(Booking $booking): void
    {
        // Tạo ghi chú khi booking bị xóa
        $this->adminBookingService->onBookingCancelled($booking, 'Đã xóa bởi admin');
        
        // Tạo thông báo admin
        $this->adminBookingService->notifyBookingCancelled($booking, 'Đã xóa bởi admin');
    }

    /**
     * Handle the Booking "restored" event.
     */
    public function restored(Booking $booking): void
    {
        // Tạo ghi chú khi booking được khôi phục
        $this->adminBookingService->onBookingConfirmed($booking);
    }

    /**
     * Handle the Booking "force deleted" event.
     */
    public function forceDeleted(Booking $booking): void
    {
        // Tạo ghi chú khi booking bị xóa vĩnh viễn
        $this->adminBookingService->onBookingCancelled($booking, 'Đã xóa vĩnh viễn');
        
        // Tạo thông báo admin
        $this->adminBookingService->notifyBookingCancelled($booking, 'Đã xóa vĩnh viễn');
    }

    /**
     * Xử lý thay đổi trạng thái booking
     */
    private function handleStatusChange(Booking $booking, string $newStatus): void
    {
        switch ($newStatus) {
            case 'confirmed':
                $this->adminBookingService->onBookingConfirmed($booking);
                break;
            case 'checked_in':
                $this->adminBookingService->onBookingCheckedIn($booking);
                break;
            case 'checked_out':
                $this->adminBookingService->onBookingCheckedOut($booking);
                break;
            case 'completed':
                $this->adminBookingService->onBookingCompleted($booking);
                break;
            case 'cancelled':
                $this->adminBookingService->onBookingCancelled($booking);
                break;
            case 'no_show':
                $this->adminBookingService->onBookingNoShow($booking);
                break;
        }
    }
} 
