<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AdminNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
        'priority',
        'icon',
        'color'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Scope để lấy thông báo chưa đọc
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope để lấy thông báo đã đọc
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope để lấy thông báo theo loại
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope để lấy thông báo theo độ ưu tiên
     */
    public function scopeOfPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Đánh dấu thông báo đã đọc
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    /**
     * Đánh dấu thông báo chưa đọc
     */
    public function markAsUnread()
    {
        $this->update([
            'is_read' => false,
            'read_at' => null
        ]);
    }

    /**
     * Lấy thời gian tạo thông báo theo định dạng dễ đọc
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Lấy màu sắc cho badge
     */
    public function getBadgeColorAttribute()
    {
        return match($this->priority) {
            'urgent' => 'danger',
            'high' => 'warning',
            'normal' => 'primary',
            'low' => 'secondary',
            default => 'primary'
        };
    }

    /**
     * Lấy icon mặc định theo loại thông báo
     */
    public function getDefaultIconAttribute()
    {
        return match($this->type) {
            'booking_created' => 'fas fa-calendar-plus',
            'booking_status_changed' => 'fas fa-exchange-alt',
            'booking_cancelled' => 'fas fa-times-circle',
            'payment_received' => 'fas fa-credit-card',
            'payment_failed' => 'fas fa-exclamation-triangle',
            'support_ticket' => 'fas fa-headset',
            'review_submitted' => 'fas fa-star',
            'room_available' => 'fas fa-bed',
            'room_maintenance' => 'fas fa-tools',
            // Ghi chú
            'booking_note_created' => 'fas fa-sticky-note',
            'booking_note_updated' => 'fas fa-edit',
            'booking_note_deleted' => 'fas fa-trash',
            'booking_note_restored' => 'fas fa-undo',
            'booking_note_force_deleted' => 'fas fa-trash-alt',
            // Đánh giá phòng
            'room_type_review_created' => 'fas fa-star',
            'room_type_review_updated' => 'fas fa-edit',
            'room_type_review_deleted' => 'fas fa-trash',
            default => 'fas fa-bell'
        };
    }

    /**
     * Lấy icon hiển thị (ưu tiên icon tùy chỉnh, nếu không có thì dùng icon mặc định)
     */
    public function getDisplayIconAttribute()
    {
        return $this->icon ?: $this->default_icon;
    }

    /**
     * Tạo thông báo mới
     */
    public static function createNotification($type, $title, $message, $data = [], $priority = 'normal', $icon = null, $color = null)
    {
        return self::create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'priority' => $priority,
            'icon' => $icon,
            'color' => $color ?: self::getDefaultColor($type, $priority)
        ]);
    }

    /**
     * Lấy màu mặc định theo loại và độ ưu tiên
     */
    private static function getDefaultColor($type, $priority)
    {
        if ($priority === 'urgent') return 'danger';
        if ($priority === 'high') return 'warning';

        return match($type) {
            'booking_created' => 'primary',
            'booking_status_changed' => 'info',
            'booking_cancelled' => 'danger',
            'payment_received' => 'success',
            'payment_failed' => 'danger',
            'support_ticket' => 'warning',
            'review_submitted' => 'info',
            'room_available' => 'success',
            'room_maintenance' => 'warning',
            // Ghi chú
            'booking_note_created' => 'info',
            'booking_note_updated' => 'info',
            'booking_note_deleted' => 'warning',
            'booking_note_restored' => 'success',
            'booking_note_force_deleted' => 'danger',
            // Đánh giá phòng
            'room_type_review_created' => 'info',
            'room_type_review_updated' => 'info',
            'room_type_review_deleted' => 'warning',
            default => 'primary'
        };
    }
}
