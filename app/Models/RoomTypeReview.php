<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomTypeReview extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'room_type_id',
        'booking_id',
        'rating',
        'comment',
        'cleanliness_rating',
        'comfort_rating',
        'location_rating',
        'facilities_rating',
        'value_rating',
        'status',
        'is_anonymous'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'integer',
        'cleanliness_rating' => 'integer',
        'comfort_rating' => 'integer',
        'location_rating' => 'integer',
        'facilities_rating' => 'integer',
        'value_rating' => 'integer',
        'is_anonymous' => 'boolean',
    ];

    /**
     * Get the user that wrote the review.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the room type that was reviewed.
     */
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    /**
     * Get the booking that was reviewed.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Scope để lấy reviews đã được duyệt
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope để lấy reviews theo loại phòng
     */
    public function scopeForRoomType($query, $roomTypeId)
    {
        return $query->where('room_type_id', $roomTypeId);
    }

    /**
     * Lấy tên hiển thị của người đánh giá
     */
    public function getReviewerNameAttribute()
    {
        if ($this->is_anonymous) {
            return 'Khách hàng ẩn danh';
        }
        
        return $this->user->name;
    }

    /**
     * Lấy trạng thái hiển thị
     */
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'Chờ duyệt',
            'approved' => 'Đã duyệt',
            'rejected' => 'Từ chối',
            default => 'Không xác định'
        };
    }

    /**
     * Kiểm tra xem review có thể được chỉnh sửa không
     */
    public function canBeEdited()
    {
        return $this->status === 'pending';
    }

    /**
     * Kiểm tra xem review có thể được xóa không
     */
    public function canBeDeleted()
    {
        return $this->status === 'pending';
    }
} 