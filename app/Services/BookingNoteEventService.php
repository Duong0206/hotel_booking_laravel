<?php

namespace App\Services;

use App\Interfaces\Services\BookingServiceInterface;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class BookingNoteEventService
{
    protected $bookingNoteService;

    public function __construct(BookingServiceInterface $bookingService)
    {
        $this->bookingNoteService = $bookingService;
    }

    /**
     * Tạo ghi chú khi booking được tạo
     */
    public function onBookingCreated(Booking $booking): void
    {
        $content = "Đặt phòng mới được tạo bởi khách hàng {$booking->user->name}. " .
                   "Phòng: {$booking->room->name}, " .
                   "Check-in: " . $booking->check_in_date->format('d/m/Y') . ", " .
                   "Check-out: " . $booking->check_out_date->format('d/m/Y') . ", " .
                   "Tổng tiền: " . number_format($booking->price) . " VND";

        $this->bookingNoteService->createSystemNote($booking->id, $content, 'admin');
    }

    /**
     * Tạo ghi chú khi booking được xác nhận
     */
    public function onBookingConfirmed(Booking $booking): void
    {
        $content = "Đặt phòng đã được xác nhận. " .
                   "Khách hàng: {$booking->user->name}, " .
                   "Phòng: {$booking->room->name}";

        $this->bookingNoteService->createSystemNote($booking->id, $content, 'admin');
        
        // Tạo thông báo cho khách hàng
        $customerContent = "Đặt phòng của bạn đã được xác nhận thành công. " .
                          "Vui lòng đến khách sạn đúng giờ để check-in.";
        
        $this->bookingNoteService->createCustomerNotification($booking->id, $customerContent);
    }

    /**
     * Tạo ghi chú khi khách check-in
     */
    public function onBookingCheckedIn(Booking $booking): void
    {
        $content = "Khách hàng {$booking->user->name} đã check-in thành công. " .
                   "Phòng: {$booking->room->name}, " .
                   "Thời gian: " . now()->format('d/m/Y H:i');

        $this->bookingNoteService->createSystemNote($booking->id, $content, 'staff');
    }

    /**
     * Tạo ghi chú khi khách check-out
     */
    public function onBookingCheckedOut(Booking $booking): void
    {
        $content = "Khách hàng {$booking->user->name} đã check-out. " .
                   "Phòng: {$booking->room->name}, " .
                   "Thời gian: " . now()->format('d/m/Y H:i');

        $this->bookingNoteService->createSystemNote($booking->id, $content, 'staff');
    }

    /**
     * Tạo ghi chú khi booking bị hủy
     */
    public function onBookingCancelled(Booking $booking, string $reason = ''): void
    {
        $content = "Đặt phòng đã bị hủy. " .
                   "Khách hàng: {$booking->user->name}, " .
                   "Phòng: {$booking->room->name}";
        
        if ($reason) {
            $content .= ", Lý do: {$reason}";
        }

        $this->bookingNoteService->createSystemNote($booking->id, $content, 'admin');
    }

    /**
     * Tạo ghi chú khi khách không đến (no-show)
     */
    public function onBookingNoShow(Booking $booking): void
    {
        $content = "Khách hàng {$booking->user->name} không đến (no-show). " .
                   "Phòng: {$booking->room->name}, " .
                   "Ngày check-in: " . $booking->check_in_date->format('d/m/Y');

        $this->bookingNoteService->createSystemNote($booking->id, $content, 'admin');
    }

    /**
     * Tạo ghi chú khi booking hoàn thành
     */
    public function onBookingCompleted(Booking $booking): void
    {
        $content = "Đặt phòng đã hoàn thành. " .
                   "Khách hàng: {$booking->user->name}, " .
                   "Phòng: {$booking->room->name}, " .
                   "Thời gian: " . now()->format('d/m/Y H:i');

        $this->bookingNoteService->createSystemNote($booking->id, $content, 'admin');
    }

    /**
     * Tạo ghi chú khi thay đổi thông tin booking
     */
    public function onBookingUpdated(Booking $booking, array $changes): void
    {
        $changeDescriptions = [];
        
        foreach ($changes as $field => $value) {
            switch ($field) {
                case 'check_in_date':
                    $changeDescriptions[] = "Ngày check-in: " . $booking->getOriginal('check_in_date') . " → " . $value;
                    break;
                case 'check_out_date':
                    $changeDescriptions[] = "Ngày check-out: " . $booking->getOriginal('check_out_date') . " → " . $value;
                    break;
                case 'room_id':
                    $oldRoom = \App\Models\Room::find($booking->getOriginal('room_id'));
                    $newRoom = $booking->room;
                    $changeDescriptions[] = "Phòng: {$oldRoom->name} → {$newRoom->name}";
                    break;
                case 'price':
                    $changeDescriptions[] = "Giá: " . number_format($booking->getOriginal('price')) . " → " . number_format($value) . " VND";
                    break;
                case 'status':
                    $changeDescriptions[] = "Trạng thái: " . $booking->getOriginal('status') . " → " . $value;
                    break;
            }
        }

        if (!empty($changeDescriptions)) {
            $content = "Thông tin đặt phòng đã được cập nhật:\n" . implode("\n", $changeDescriptions);
            $this->bookingNoteService->createSystemNote($booking->id, $content, 'admin');
        }
    }

    /**
     * Tạo ghi chú khi có yêu cầu đặc biệt
     */
    public function createSpecialRequestNote(Booking $booking, string $request): void
    {
        $content = "Yêu cầu đặc biệt từ khách hàng {$booking->user->name}: {$request}";
        
        $this->bookingNoteService->createInternalNote($booking->id, $content);
    }

    /**
     * Tạo ghi chú khi có vấn đề với phòng
     */
    public function createRoomIssueNote(Booking $booking, string $issue): void
    {
        $content = "Vấn đề với phòng {$booking->room->name}: {$issue}";
        
        $this->bookingNoteService->createInternalNote($booking->id, $content);
    }

    /**
     * Tạo ghi chú khi có thanh toán
     */
    public function createPaymentNote(Booking $booking, string $paymentMethod, float $amount): void
    {
        $content = "Thanh toán thành công: {$paymentMethod}, " .
                   "Số tiền: " . number_format($amount) . " VND, " .
                   "Khách hàng: {$booking->user->name}";

        $this->bookingNoteService->createSystemNote($booking->id, $content, 'admin');
    }

    /**
     * Tạo ghi chú khi có phản hồi từ khách hàng
     */
    public function createCustomerFeedbackNote(Booking $booking, string $feedback): void
    {
        $content = "Phản hồi từ khách hàng {$booking->user->name}: {$feedback}";
        
        $this->bookingNoteService->createCustomerNotification($booking->id, $content);
    }

    /**
     * Tạo ghi chú nhắc nhở check-in
     */
    public function createCheckInReminderNote(Booking $booking): void
    {
        $content = "Nhắc nhở: Khách hàng {$booking->user->name} sẽ check-in vào ngày mai. " .
                   "Vui lòng chuẩn bị phòng {$booking->room->name}.";

        $this->bookingNoteService->createInternalNote($booking->id, $content);
    }

    /**
     * Tạo ghi chú nhắc nhở check-out
     */
    public function createCheckOutReminderNote(Booking $booking): void
    {
        $content = "Nhắc nhở: Khách hàng {$booking->user->name} sẽ check-out vào ngày mai. " .
                   "Vui lòng chuẩn bị thủ tục thanh toán cho phòng {$booking->room->name}.";

        $this->bookingNoteService->createInternalNote($booking->id, $content);
    }
} 