<?php

namespace App\Observers;

use App\Models\BookingNote;
use App\Interfaces\Services\Admin\AdminBookingServiceInterface;

class BookingNoteObserver
{
    protected $adminBookingService;

    public function __construct(AdminBookingServiceInterface $adminBookingService)
    {
        $this->adminBookingService = $adminBookingService;
    }

    /**
     * Handle the BookingNote "created" event.
     */
    public function created(BookingNote $bookingNote)
    {
        // Tạo thông báo admin khi có ghi chú mới
        $this->adminBookingService->createNoteNotification([
            'note_id' => $bookingNote->id,
            'booking_id' => $bookingNote->booking_id,
            'user_id' => $bookingNote->user_id,
            'type' => $bookingNote->type,
            'visibility' => $bookingNote->visibility,
            'is_internal' => $bookingNote->is_internal,
            'booking_code' => $bookingNote->booking->booking_id ?? 'N/A'
        ]);
    }

    /**
     * Handle the BookingNote "updated" event.
     */
    public function updated(BookingNote $bookingNote)
    {
        // Tạo thông báo admin khi ghi chú được cập nhật
        $this->adminBookingService->createNotification(
            'booking_note_updated',
            'Ghi chú được cập nhật',
            "Ghi chú cho đặt phòng #{$bookingNote->booking->booking_id} đã được cập nhật",
            [
                'note_id' => $bookingNote->id,
                'booking_id' => $bookingNote->booking_id,
                'user_id' => $bookingNote->user_id,
                'type' => $bookingNote->type,
                'visibility' => $bookingNote->visibility,
                'is_internal' => $bookingNote->is_internal,
                'booking_code' => $bookingNote->booking->booking_id ?? 'N/A'
            ],
            'normal',
            'fas fa-edit',
            'info'
        );
    }

    /**
     * Handle the BookingNote "deleted" event.
     */
    public function deleted(BookingNote $bookingNote)
    {
        // Tạo thông báo admin khi ghi chú bị xóa
        $this->adminBookingService->createNotification(
            'booking_note_deleted',
            'Ghi chú bị xóa',
            "Ghi chú cho đặt phòng #{$bookingNote->booking->booking_id} đã bị xóa",
            [
                'note_id' => $bookingNote->id,
                'booking_id' => $bookingNote->booking_id,
                'user_id' => $bookingNote->user_id,
                'type' => $bookingNote->type,
                'visibility' => $bookingNote->visibility,
                'is_internal' => $bookingNote->is_internal,
                'booking_code' => $bookingNote->booking->booking_id ?? 'N/A'
            ],
            'normal',
            'fas fa-trash',
            'warning'
        );
    }

    /**
     * Handle the BookingNote "restored" event.
     */
    public function restored(BookingNote $bookingNote)
    {
        // Tạo thông báo admin khi ghi chú được khôi phục
        $this->adminBookingService->createNotification(
            'booking_note_restored',
            'Ghi chú được khôi phục',
            "Ghi chú cho đặt phòng #{$bookingNote->booking->booking_id} đã được khôi phục",
            [
                'note_id' => $bookingNote->id,
                'booking_id' => $bookingNote->booking_id,
                'user_id' => $bookingNote->user_id,
                'type' => $bookingNote->type,
                'visibility' => $bookingNote->visibility,
                'is_internal' => $bookingNote->is_internal,
                'booking_code' => $bookingNote->booking->booking_id ?? 'N/A'
            ],
            'normal',
            'fas fa-undo',
            'success'
        );
    }

    /**
     * Handle the BookingNote "force deleted" event.
     */
    public function forceDeleted(BookingNote $bookingNote)
    {
        // Tạo thông báo admin khi ghi chú bị xóa vĩnh viễn
        $this->adminBookingService->createNotification(
            'booking_note_force_deleted',
            'Ghi chú bị xóa vĩnh viễn',
            "Ghi chú cho đặt phòng #{$bookingNote->booking->booking_id} đã bị xóa vĩnh viễn",
            [
                'note_id' => $bookingNote->id,
                'booking_id' => $bookingNote->booking_id,
                'user_id' => $bookingNote->user_id,
                'type' => $bookingNote->type,
                'visibility' => $bookingNote->visibility,
                'is_internal' => $bookingNote->is_internal,
                'booking_code' => $bookingNote->booking->booking_id ?? 'N/A'
            ],
            'high',
            'fas fa-trash-alt',
            'danger'
        );
    }
}
