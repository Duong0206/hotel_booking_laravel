<?php

namespace App\Interfaces\Services\Admin;

interface AdminBookingServiceServiceInterface
{
    /**
     * Lấy tất cả dịch vụ của một đơn đặt phòng.
     */
    public function getBookingServices($bookingId);

    /**
     * Lấy các dịch vụ của loại phòng còn khả dụng cho đơn đặt phòng.
     */
    public function getAvailableRoomTypeServices($bookingId);

    /**
     * Lấy tất cả dịch vụ còn khả dụng (chưa được thêm vào đơn đặt phòng).
     */
    public function getAvailableServices($bookingId);

    /**
     * Thêm dịch vụ tuỳ chọn vào đơn đặt phòng.
     */
    public function addCustomServiceToBooking($bookingId, $serviceName, $servicePrice, $quantity = 1, $notes = null);

    /**
     * Xoá dịch vụ khỏi đơn đặt phòng.
     */
    public function destroyServiceFromBooking($bookingServiceId);
}
