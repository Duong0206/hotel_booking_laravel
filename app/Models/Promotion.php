<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Promotion extends Model
{
    use HasFactory;

    protected $table = 'promotions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
        protected $fillable = [
        'title',
        'description', 
        'code',
        'discount_type',
        'discount_value',
        'minimum_amount',
        'usage_limit',
        'used_count',
        'valid_from',
        'expired_at',
        'is_active',
        'is_featured',
        'can_combine',
        'image',
        'terms_conditions'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'valid_from' => 'date',
        'expired_at' => 'date',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'can_combine' => 'boolean',
        'discount_value' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
    ];

    /**
     * Lấy các promotion đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function($q) {
                        $q->whereNull('valid_from')
                          ->orWhere('valid_from', '<=', Carbon::today());
                    })
                    ->where('expired_at', '>=', Carbon::today());
    }

    /**
     * Lấy các promotion nổi bật
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Lấy các promotion còn có thể sử dụng
     */
    public function scopeAvailable($query)
    {
        return $query->where(function($q) {
            $q->whereNull('usage_limit')
              ->orWhereRaw('used_count < usage_limit');
        });
    }

    /**
     * Kiểm tra promotion có còn hiệu lực không
     */
    public function isValid()
    {
        return $this->is_active 
            && ($this->valid_from === null || $this->valid_from <= Carbon::today())
            && $this->expired_at >= Carbon::today()
            && ($this->usage_limit === null || $this->used_count < $this->usage_limit);
    }

    /**
     * Tính số tiền giảm giá
     */
    public function calculateDiscount($amount)
    {
        if (!$this->isValid() || $amount < $this->minimum_amount) {
            return 0;
        }

        if ($this->discount_type === 'percentage') {
            $discount = ($amount * $this->discount_value) / 100;
            // Giới hạn tối đa 100% giá trị đơn hàng
            return min($discount, $amount);
        }

        // Đối với giảm giá cố định, không được vượt quá giá trị đơn hàng
        return min($this->discount_value, $amount);
    }

    /**
     * Kiểm tra promotion có thể áp dụng cho số tiền cụ thể
     */
    public function canApplyToAmount($amount)
    {
        return $this->isValid() && $amount >= $this->minimum_amount;
    }

    /**
     * Lấy text hiển thị giảm giá
     */
    public function getDiscountTextAttribute()
    {
        if ($this->discount_type === 'percentage') {
            return $this->discount_value . '%';
        }
        
        return number_format($this->discount_value, 0, ',', '.') . 'đ';
    }

        /**
     * Lấy trạng thái promotion
     */
    public function getStatusAttribute()
    {
        if (!$this->is_active) {
            return 'Tạm dừng';
        }
        
        if ($this->expired_at < Carbon::today()) {
            return 'Hết hạn';
        }
        
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return 'Hết lượt';
        }
        
        if ($this->valid_from && $this->valid_from > Carbon::today()) {
            return 'Sắp diễn ra';
        }
        
        return 'Đang hoạt động';
    }

    /**
     * Quan hệ với các loại phòng có thể áp dụng khuyến mại
     */
    public function roomTypes()
    {
        return $this->belongsToMany(RoomType::class, 'promotion_room_type');
    }

    /**
     * Quan hệ với các phòng cụ thể có thể áp dụng khuyến mại
     */
    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'promotion_room');
    }

    /**
     * Kiểm tra xem promotion có áp dụng cho loại phòng cụ thể không
     */
    public function canApplyToRoomType($roomTypeId)
    {
        // Nếu có chọn phòng cụ thể, kiểm tra theo rooms
        if ($this->rooms()->count() > 0) {
            return $this->rooms()->whereHas('roomType', function($query) use ($roomTypeId) {
                $query->where('id', $roomTypeId);
            })->exists();
        }
        
        // Nếu có chọn loại phòng, kiểm tra theo room types
        if ($this->roomTypes()->count() > 0) {
            return $this->roomTypes()->where('room_type_id', $roomTypeId)->exists();
        }
        
        // Nếu không chọn gì thì áp dụng cho tất cả
        return true;
    }

    /**
     * Kiểm tra xem promotion có áp dụng cho phòng cụ thể không
     */
    public function canApplyToRoom($roomId)
    {
        // Nếu có chọn phòng cụ thể
        if ($this->rooms()->count() > 0) {
            return $this->rooms()->where('room_id', $roomId)->exists();
        }
        
        // Nếu có chọn loại phòng, kiểm tra phòng thuộc loại đó không
        if ($this->roomTypes()->count() > 0) {
            $room = Room::find($roomId);
            return $room && $this->canApplyToRoomType($room->room_type_id);
        }
        
        // Nếu không chọn gì thì áp dụng cho tất cả
        return true;
    }

 
} 