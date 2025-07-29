<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingNote extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_id',
        'user_id',
        'content',
        'type',
        'visibility',
        'is_internal'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_internal' => 'boolean',
    ];

    /**
     * Get the booking that owns the note.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the user that created the note.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope để lấy ghi chú theo loại
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope để lấy ghi chú theo visibility
     */
    public function scopeOfVisibility($query, $visibility)
    {
        return $query->where('visibility', $visibility);
    }

    /**
     * Scope để lấy ghi chú công khai
     */
    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    /**
     * Scope để lấy ghi chú nội bộ
     */
    public function scopeInternal($query)
    {
        return $query->where('is_internal', true);
    }

    /**
     * Lấy tên hiển thị của loại ghi chú
     */
    public function getTypeTextAttribute()
    {
        return match($this->type) {
            'customer' => 'Khách hàng',
            'staff' => 'Nhân viên',
            'admin' => 'Quản lý',
            default => 'Không xác định'
        };
    }

    /**
     * Lấy tên hiển thị của visibility
     */
    public function getVisibilityTextAttribute()
    {
        return match($this->visibility) {
            'public' => 'Công khai',
            'private' => 'Riêng tư',
            'internal' => 'Nội bộ',
            default => 'Không xác định'
        };
    }

    /**
     * Kiểm tra xem ghi chú có thể xem bởi user không
     */
    public function canBeViewedBy($user)
    {
        // Admin có thể xem tất cả
        if ($user->hasRole('admin')) {
            return true;
        }

        // Ghi chú nội bộ chỉ admin xem được
        if ($this->is_internal) {
            return false;
        }

        // Ghi chú công khai ai cũng xem được
        if ($this->visibility === 'public') {
            return true;
        }

        // Ghi chú riêng tư chỉ người tạo xem được
        if ($this->visibility === 'private') {
            return $this->user_id === $user->id;
        }

        // Ghi chú nội bộ chỉ staff và admin xem được
        if ($this->visibility === 'internal') {
            return $user->hasRole('admin') || $user->hasRole('staff');
        }

        return false;
    }

    /**
     * Kiểm tra xem ghi chú có thể chỉnh sửa bởi user không
     */
    public function canBeEditedBy($user)
    {
        // Admin có thể chỉnh sửa tất cả
        if ($user->hasRole('admin')) {
            return true;
        }

        // Người tạo ghi chú có thể chỉnh sửa (trừ ghi chú nội bộ)
        if ($this->user_id === $user->id && !$this->is_internal) {
            return true;
        }

        return false;
    }

    /**
     * Kiểm tra xem ghi chú có thể xóa bởi user không
     */
    public function canBeDeletedBy($user)
    {
        // Admin có thể xóa tất cả
        if ($user->hasRole('admin')) {
            return true;
        }

        // Người tạo ghi chú có thể xóa (trừ ghi chú nội bộ)
        if ($this->user_id === $user->id && !$this->is_internal) {
            return true;
        }

        return false;
    }
}
