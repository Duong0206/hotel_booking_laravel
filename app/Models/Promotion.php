<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;

class Promotion extends Model
{
    use SoftDeletes;

        protected $fillable = [
        'title',
        'code',
        'description',
        'terms_conditions',
        'image',
        'discount_type',
        'discount_value',
        'minimum_amount',
        'apply_scope',
        'valid_from',
        'expired_at',
        'usage_limit',
        'used_count',
        'is_active',
        'is_featured',
        'can_combine'
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'valid_from' => 'datetime',
        'expired_at' => 'datetime',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'can_combine' => 'boolean'
    ];

    /**
     * Relationships
     */
    public function roomTypes(): BelongsToMany
    {
        return $this->belongsToMany(RoomType::class, 'promotion_room_type', 'promotion_id', 'room_type_id')
                    ->withTimestamps();
    }



    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function($q) {
                        $q->whereNull('valid_from')
                          ->orWhere('valid_from', '<=', now());
                    })
                    ->where('expired_at', '>', now());
    }

    public function scopeAvailable($query)
    {
        return $query->where(function($q) {
            $q->whereNull('usage_limit')
              ->orWhereRaw('used_count < usage_limit');
        });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Kiểm tra khuyến mại có thể áp dụng cho loại phòng
     */
    public function canApplyToRoomType(int $roomTypeId): bool
    {
        $cacheKey = "promotion_{$this->id}_room_type_{$roomTypeId}";
        
        return Cache::remember($cacheKey, now()->addMinutes(60), function() use ($roomTypeId) {
            switch ($this->apply_scope) {
                case 'room_types':
                    // Kiểm tra loại phòng có được chọn không
                    return $this->roomTypes()
                        ->where('room_types.id', $roomTypeId)
                        ->exists();
                    
                case 'all':
                default:
                    return true;
            }
        });
    }

    /**
     * Kiểm tra khuyến mại có thể áp dụng cho phòng cụ thể
     */
    public function canApplyToRoom(int $roomId): bool
    {
        $cacheKey = "promotion_{$this->id}_room_{$roomId}";
        
        return Cache::remember($cacheKey, now()->addMinutes(60), function() use ($roomId) {
            switch ($this->apply_scope) {
                case 'room_types':
                    // Kiểm tra phòng có thuộc loại được chọn không
                    return $this->roomTypes()
                        ->whereHas('rooms', function($query) use ($roomId) {
                            $query->where('id', $roomId);
                        })
                        ->exists();
                    
                case 'all':
                default:
                    return true;
            }
        });
    }

    /**
     * Clear cache khi cập nhật relationships
     */
    public function clearApplyCache(): void
    {
        $cacheKeys = [];
        
        // Clear room type cache
        foreach ($this->roomTypes()->pluck('room_types.id') as $roomTypeId) {
            $cacheKeys[] = "promotion_{$this->id}_room_type_{$roomTypeId}";
        }
        
        Cache::deleteMultiple($cacheKeys);
    }

    /**
     * Kiểm tra khuyến mại có đang có hiệu lực
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        
        if ($this->valid_from && $this->valid_from->gt($now)) {
            return false;
        }
        
        if ($this->expired_at->lte($now)) {
            return false;
        }
        
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }
        
        return true;
    }

    /**
     * Kiểm tra có thể áp dụng cho đơn hàng với giá trị cụ thể
     */
    public function canApplyToAmount(float $amount): bool
    {
        return $this->isValid() && $amount >= $this->minimum_amount;
    }

    /**
     * Tính số tiền được giảm
     */
    public function calculateDiscount(float $amount): float
    {
        if (!$this->canApplyToAmount($amount)) {
            return 0;
        }

        if ($this->discount_type === 'percentage') {
            return min(
                ($amount * $this->discount_value) / 100,
                $amount // Không giảm quá 100%
            );
        }

        return min(
            $this->discount_value,
            $amount // Không giảm quá giá gốc
        );
    }

    /**
     * Tăng số lần sử dụng
     */
    public function incrementUsage(): bool
    {
        return $this->increment('used_count');
    }

    /**
     * Attributes
     */
    public function getDiscountTextAttribute(): string
    {
        if ($this->discount_type === 'percentage') {
            return number_format($this->discount_value, 1) . '%';
        }
        
        return number_format($this->discount_value, 0, ',', '.') . 'đ';
    }

    public function getStatusTextAttribute(): string
    {
        if (!$this->is_active) {
            return 'Tạm dừng';
        }
        
        if ($this->expired_at->isPast()) {
            return 'Hết hạn';
        }
        
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return 'Hết lượt';
        }
        
        if ($this->valid_from && $this->valid_from->isFuture()) {
            return 'Sắp diễn ra';
        }
        
        return 'Đang hoạt động';
    }

    public function getStatusColorAttribute(): string
    {
        switch ($this->status_text) {
            case 'Tạm dừng':
                return 'secondary';
            case 'Hết hạn':
                return 'danger';
            case 'Hết lượt':
                return 'warning';
            case 'Sắp diễn ra':
                return 'info';
            default:
                return 'success';
        }
    }

    /**
     * Lấy text hiển thị phạm vi áp dụng
     */
    public function getApplyScopeTextAttribute(): string
    {
        switch ($this->apply_scope) {
            case 'room_types':
                $roomTypes = $this->roomTypes()->get();
                if ($roomTypes->isEmpty()) {
                    return 'Áp dụng cho tất cả phòng';
                }
                if ($roomTypes->count() === 1) {
                    return 'Áp dụng cho loại phòng: ' . $roomTypes->first()->name;
                }
                return 'Áp dụng cho ' . $roomTypes->count() . ' loại phòng';
                
            case 'all':
            default:
                return 'Áp dụng cho tất cả phòng';
        }
    }

    /**
     * Lấy danh sách chi tiết phạm vi áp dụng
     */
    public function getApplyScopeDetailsAttribute(): array
    {
        switch ($this->apply_scope) {
            case 'room_types':
                $roomTypes = $this->roomTypes()->with('rooms')->get();
                if ($roomTypes->isEmpty()) {
                    return [];
                }
                return $roomTypes->map(function($type) {
                    return [
                        'name' => $type->name,
                        'count' => $type->rooms->count() . ' phòng'
                    ];
                })->toArray();
                
            case 'all':
            default:
                return [];
        }
    }
} 