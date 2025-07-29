<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Interfaces\Services\BookingServiceInterface;
use Carbon\Carbon;

class CreateBookingNoteReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking-notes:create-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tạo ghi chú nhắc nhở check-in/check-out cho các booking';

    protected $bookingNoteService;

    /**
     * Create a new command instance.
     */
    public function __construct(BookingServiceInterface $bookingService)
    {
        parent::__construct();
        $this->bookingNoteService = $bookingService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Bắt đầu tạo ghi chú nhắc nhở...');

        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        // Tìm các booking check-in hôm nay
        $checkInBookings = Booking::where('check_in_date', $today)
            ->where('status', 'confirmed')
            ->get();

        $this->info("Tìm thấy {$checkInBookings->count()} booking check-in hôm nay");

        foreach ($checkInBookings as $booking) {
            try {
                $this->bookingNoteService->createSystemNote(
                    $booking->id,
                    "Nhắc nhở: Khách hàng check-in hôm nay ({$today->format('d/m/Y')}). Vui lòng chuẩn bị phòng và đón tiếp khách.",
                    'system'
                );
                $this->line("✓ Đã tạo ghi chú nhắc nhở check-in cho booking #{$booking->id}");
            } catch (\Exception $e) {
                $this->error("✗ Lỗi khi tạo ghi chú cho booking #{$booking->id}: " . $e->getMessage());
            }
        }

        // Tìm các booking check-in ngày mai
        $tomorrowCheckInBookings = Booking::where('check_in_date', $tomorrow)
            ->where('status', 'confirmed')
            ->get();

        $this->info("Tìm thấy {$tomorrowCheckInBookings->count()} booking check-in ngày mai");

        foreach ($tomorrowCheckInBookings as $booking) {
            try {
                $this->bookingNoteService->createSystemNote(
                    $booking->id,
                    "Nhắc nhở: Khách hàng check-in ngày mai ({$tomorrow->format('d/m/Y')}). Vui lòng chuẩn bị phòng.",
                    'system'
                );
                $this->line("✓ Đã tạo ghi chú nhắc nhở check-in ngày mai cho booking #{$booking->id}");
            } catch (\Exception $e) {
                $this->error("✗ Lỗi khi tạo ghi chú cho booking #{$booking->id}: " . $e->getMessage());
            }
        }

        // Tìm các booking check-out hôm nay
        $checkOutBookings = Booking::where('check_out_date', $today)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->get();

        $this->info("Tìm thấy {$checkOutBookings->count()} booking check-out hôm nay");

        foreach ($checkOutBookings as $booking) {
            try {
                $this->bookingNoteService->createSystemNote(
                    $booking->id,
                    "Nhắc nhở: Khách hàng check-out hôm nay ({$today->format('d/m/Y')}). Vui lòng kiểm tra phòng và thanh toán.",
                    'system'
                );
                $this->line("✓ Đã tạo ghi chú nhắc nhở check-out cho booking #{$booking->id}");
            } catch (\Exception $e) {
                $this->error("✗ Lỗi khi tạo ghi chú cho booking #{$booking->id}: " . $e->getMessage());
            }
        }

        // Tìm các booking check-out ngày mai
        $tomorrowCheckOutBookings = Booking::where('check_out_date', $tomorrow)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->get();

        $this->info("Tìm thấy {$tomorrowCheckOutBookings->count()} booking check-out ngày mai");

        foreach ($tomorrowCheckOutBookings as $booking) {
            try {
                $this->bookingNoteService->createSystemNote(
                    $booking->id,
                    "Nhắc nhở: Khách hàng check-out ngày mai ({$tomorrow->format('d/m/Y')}). Vui lòng chuẩn bị hóa đơn.",
                    'system'
                );
                $this->line("✓ Đã tạo ghi chú nhắc nhở check-out ngày mai cho booking #{$booking->id}");
            } catch (\Exception $e) {
                $this->error("✗ Lỗi khi tạo ghi chú cho booking #{$booking->id}: " . $e->getMessage());
            }
        }

        $this->info('Hoàn thành tạo ghi chú nhắc nhở!');
    }
} 