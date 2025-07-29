<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingService extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'service_id',
        'quantity',
        'unit_price',
        'total_price',
        'notes',
        'type'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'quantity' => 'integer'
    ];

    /**
     * Lấy thông tin đặt phòng của dịch vụ
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Lấy thông tin dịch vụ
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    
    /**
     * Tính lại tổng tiền mỗi khi số lượng hoặc đơn giá thay đổi.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($bookingService) {
            $bookingService->total_price = $bookingService->quantity * $bookingService->unit_price;
        });
    }

    /**
     * Lấy giá trị tổng tiền đã được định dạng.
     */
    public function getFormattedTotalPriceAttribute()
    {
        return number_format($this->total_price, 0, ',', '.') . ' VNĐ';
    }

    /**
     * Lấy giá trị đơn giá đã được định dạng.
     */
    public function getFormattedUnitPriceAttribute()
    {
        return number_format($this->unit_price, 0, ',', '.') . ' VNĐ';
    }
}
