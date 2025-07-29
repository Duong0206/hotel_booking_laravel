<?php

namespace App\Services\Admin;

use App\Interfaces\Services\Admin\AdminBookingServiceServiceInterface;
use App\Models\Booking;
use App\Models\BookingService;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Exception;

class AdminBookingServiceService implements AdminBookingServiceServiceInterface
{
    /**
     * Lấy tất cả dịch vụ thuộc đơn đặt phòng.
     */
    public function getBookingServices($bookingId)
    {
        return BookingService::with(['service.category'])
            ->where('booking_id', $bookingId)
            ->orderBy('type', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Lấy các dịch vụ của loại phòng còn khả dụng cho đơn đặt phòng.
     */
    public function getAvailableRoomTypeServices($bookingId)
    {
        $booking = Booking::with('room.roomType')->findOrFail($bookingId);
        return $booking->getAvailableRoomTypeServices();
    }

    /**
     * Lấy tất cả dịch vụ còn khả dụng (chưa thuộc đơn đặt phòng).
     */
    public function getAvailableServices($bookingId)
    {
        $existingServiceIds = BookingService::where('booking_id', $bookingId)
            ->pluck('service_id')
            ->toArray();

        return Service::with('category')
            ->whereNotIn('id', $existingServiceIds)
            ->orderBy('name')
            ->get();
    }
    /**
     * Thêm dịch vụ vào đơn đặt phòng.
     */
    public function addCustomServiceToBooking($bookingId, $serviceName, $servicePrice, $quantity = 1, $notes = null)
    {
        try {
            DB::beginTransaction();

            // Tạo dịch vụ tạm thời trong bảng services
            $service = Service::create([
                'name' => $serviceName,
                'price' => $servicePrice,
                'description' => 'Dịch vụ tùy chỉnh cho booking',
                'service_category_id' => null
            ]);

            // Tạo booking service mới
            $bookingService = BookingService::create([
                'booking_id' => $bookingId,
                'service_id' => $service->id,
                'quantity' => $quantity,
                'unit_price' => $servicePrice,
                'type' => 'custom',
                'notes' => $notes
            ]);

            DB::commit();
            return $bookingService->load('service');

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Xóa dịch vụ khỏi đơn đặt phòng.
     */
    public function destroyServiceFromBooking($bookingServiceId)
    {
        try {
            $bookingService = BookingService::findOrFail($bookingServiceId);
            $bookingService->delete();
            
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
