<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'booking_id',
        'room_id',
        'check_in_date',
        'check_out_date',
        'status',
        'price'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'check_in_date' => 'datetime',
        'check_out_date' => 'datetime',
    ];

    /**
     * Get the user that owns the booking.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the room that is booked.
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the review for this booking.
     */
    public function review()
    {
        return $this->hasOne(RoomTypeReview::class);
    }

    /**
     * Accessor cho ngày check-in (chỉ lấy phần date)
     */
    public function getCheckInDateOnlyAttribute()
    {
        return $this->check_in_date ? $this->check_in_date->format('Y-m-d') : null;
    }

    /**
     * Get the notes for this booking.
     */
    public function notes()
    {
        return $this->hasMany(BookingNote::class);
    }

    /**
     * Get the public notes for this booking.
     */
    public function publicNotes()
    {
        return $this->hasMany(BookingNote::class)->public();
    }

    /**
     * Get the internal notes for this booking.
     */
    public function internalNotes()
    {
        return $this->hasMany(BookingNote::class)->internal();
    }

    /**
     * Lấy danh sách dịch vụ đã thêm cho booking này.
     */
    public function bookingServices()
    {
        return $this->hasMany(BookingService::class);
    }

    /**
     * Lấy danh sách dịch vụ thuộc loại phòng.
     */
    public function roomTypeServices()
    {
        return $this->hasMany(BookingService::class)->roomType();
    }

    /**
     * Lấy danh sách dịch vụ bổ sung (được thêm thủ công cho booking này).
     */
    public function additionalServices()
    {
        return $this->hasMany(BookingService::class)->additional();
    }

    /**
     * Lấy các dịch vụ có sẵn từ loại phòng nhưng chưa được thêm vào booking.
     */
    public function getAvailableRoomTypeServices()
    {
        $roomTypeId = $this->room->room_type_id;
        $existingServiceIds = $this->bookingServices()->pluck('service_id')->toArray();

        return \App\Models\Service::whereHas('roomTypes', function ($query) use ($roomTypeId) {
            $query->where('room_type_id', $roomTypeId);
        })->whereNotIn('id', $existingServiceIds)->get();
    }

    /**
     * Tính tổng giá tiền của các dịch vụ trong booking.
     */
    public function getTotalServicesPriceAttribute()
    {
        return $this->bookingServices()->sum('total_price');
    }

    /**
     * Tính tổng giá trị của booking bao gồm cả tiền phòng và dịch vụ.
     */
    public function getTotalBookingPriceAttribute()
    {
        return $this->price + $this->total_services_price;
    }

    /**
     * Accessor cho ngày check-out (chỉ lấy phần date)
     */
    public function getCheckOutDateOnlyAttribute()
    {
        return $this->check_out_date ? $this->check_out_date->format('Y-m-d') : null;
    }

    /**
     * Accessor cho giờ check-in (chỉ lấy phần time)
     */
    public function getCheckInTimeAttribute()
    {
        return $this->check_in_date ? $this->check_in_date->format('H:i') : null;
    }

    /**
     * Accessor cho giờ check-out (chỉ lấy phần time)
     */
    public function getCheckOutTimeAttribute()
    {
        return $this->check_out_date ? $this->check_out_date->format('H:i') : null;
    }

    /**
     * Accessor for total_price
     */
    public function getTotalPriceAttribute()
    {
        return $this->price;
    }

    /**
     * Get the number of guests.
     */
    public function getGuestsAttribute()
    {
        // Mặc định là 2 khách nếu không có thông tin
        return 2;
    }

    /**
     * Kiểm tra xem booking đã hoàn thành chưa (check-out xong)
     */
    public function isCompleted()
    {
        return $this->status === 'completed' ||
            ($this->check_out_date && $this->check_out_date->isPast());
    }

    /**
     * Lấy trạng thái hiển thị
     */
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'checked_in' => 'Đã nhận phòng',
            'checked_out' => 'Đã trả phòng',
            'completed' => 'Đã hoàn thành',
            'cancelled' => 'Đã hủy',
            'no_show' => 'Khách không đến',
            default => 'Không xác định'
        };
    }
}